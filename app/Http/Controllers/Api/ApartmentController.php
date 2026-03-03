<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreApartmentRequest;
use App\Http\Requests\Api\UpdateApartmentRequest;
use App\Http\Resources\ApartmentResource;
use App\Models\Apartment;
use App\Models\ApartmentImage;
use App\Models\ApartmentView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApartmentController extends Controller
{
    /**
     * List apartments with filtering, sorting, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Apartment::with(['owner', 'judiciary', 'images'])
            ->withCount(['favorites', 'reviews'])
            ->withAvg('reviews', 'rating');

        // Add is_favorited flag for authenticated users
        if ($request->user()) {
            $userId = $request->user()->id;
            $query->addSelect([
                'is_favorited' => DB::table('favorites')
                    ->selectRaw('count(*) > 0')
                    ->whereColumn('favorites.apartment_id', 'apartments.id')
                    ->where('favorites.user_id', $userId)
                    ->limit(1),
            ]);
        }

        // Filters
        if ($request->filled('judiciary_id')) {
            $query->byJudiciary($request->integer('judiciary_id'));
        }

        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->byPriceRange(
                $request->filled('min_price') ? (float) $request->min_price : null,
                $request->filled('max_price') ? (float) $request->max_price : null,
            );
        }

        if ($request->filled('rooms')) {
            $query->byRooms($request->integer('rooms'));
        }

        if ($request->boolean('furnished')) {
            $query->furnished();
        }

        if ($request->boolean('parking')) {
            $query->withParking();
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->available();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min_size')) {
            $query->where('size_m2', '>=', $request->integer('min_size'));
        }

        if ($request->filled('max_size')) {
            $query->where('size_m2', '<=', $request->integer('max_size'));
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'newest');
        $query = match ($sortBy) {
            'lowest_price' => $query->orderBy('price_usd', 'asc'),
            'highest_price' => $query->orderBy('price_usd', 'desc'),
            'most_viewed' => $query->orderBy('views_count', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $apartments = $query->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => ApartmentResource::collection($apartments->items()),
            'meta' => [
                'current_page' => $apartments->currentPage(),
                'last_page' => $apartments->lastPage(),
                'per_page' => $apartments->perPage(),
                'total' => $apartments->total(),
            ],
        ]);
    }

    /**
     * Show a single apartment.
     */
    public function show(Request $request, Apartment $apartment): JsonResponse
    {
        $apartment->load(['owner', 'judiciary', 'images', 'reviews.reviewer']);
        $apartment->loadCount(['favorites', 'reviews']);
        $apartment->loadAvg('reviews', 'rating');

        // Record view
        ApartmentView::create([
            'apartment_id' => $apartment->id,
            'viewer_id' => $request->user()?->id,
            'ip_address' => $request->ip(),
        ]);

        $apartment->increment('views_count');

        return response()->json([
            'data' => new ApartmentResource($apartment),
        ]);
    }

    /**
     * Store a new apartment listing.
     */
    public function store(StoreApartmentRequest $request): JsonResponse
    {
        $apartment = DB::transaction(function () use ($request) {
            $apartment = Apartment::create([
                'owner_id' => $request->user()->id,
                ...$request->safe()->except('images'),
            ]);

            // Handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('apartments/'.$apartment->id, 'public');
                    ApartmentImage::create([
                        'apartment_id' => $apartment->id,
                        'image_path' => $path,
                        'sort_order' => $index,
                    ]);
                }
            }

            return $apartment;
        });

        $apartment->load(['owner', 'judiciary', 'images']);

        return response()->json([
            'message' => 'Apartment listed successfully.',
            'data' => new ApartmentResource($apartment),
        ], 201);
    }

    /**
     * Update an apartment listing.
     */
    public function update(UpdateApartmentRequest $request, Apartment $apartment): JsonResponse
    {
        DB::transaction(function () use ($request, $apartment) {
            $apartment->update($request->safe()->except('images'));

            // Handle new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('apartments/'.$apartment->id, 'public');
                    ApartmentImage::create([
                        'apartment_id' => $apartment->id,
                        'image_path' => $path,
                        'sort_order' => $apartment->images()->max('sort_order') + 1 + $index,
                    ]);
                }
            }
        });

        $apartment->load(['owner', 'judiciary', 'images']);

        return response()->json([
            'message' => 'Apartment updated successfully.',
            'data' => new ApartmentResource($apartment->fresh()),
        ]);
    }

    /**
     * Delete an apartment listing.
     */
    public function destroy(Request $request, Apartment $apartment): JsonResponse
    {
        if ($request->user()->id !== $apartment->owner_id && ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        // Delete images from storage
        foreach ($apartment->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $apartment->delete();

        return response()->json(['message' => 'Apartment deleted successfully.']);
    }

    /**
     * Delete a specific apartment image.
     */
    public function deleteImage(Request $request, Apartment $apartment, ApartmentImage $image): JsonResponse
    {
        if ($request->user()->id !== $apartment->owner_id && ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($image->apartment_id !== $apartment->id) {
            return response()->json(['message' => 'Image does not belong to this apartment.'], 404);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['message' => 'Image deleted successfully.']);
    }

    /**
     * Get apartments owned by the authenticated user.
     */
    public function myListings(Request $request): JsonResponse
    {
        $apartments = Apartment::where('owner_id', $request->user()->id)
            ->with(['judiciary', 'images'])
            ->withCount(['favorites', 'reviews', 'views'])
            ->withAvg('reviews', 'rating')
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => ApartmentResource::collection($apartments->items()),
            'meta' => [
                'current_page' => $apartments->currentPage(),
                'last_page' => $apartments->lastPage(),
                'per_page' => $apartments->perPage(),
                'total' => $apartments->total(),
            ],
        ]);
    }
}
