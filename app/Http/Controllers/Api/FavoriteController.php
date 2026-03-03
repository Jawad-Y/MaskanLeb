<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApartmentResource;
use App\Http\Resources\FavoriteResource;
use App\Models\Apartment;
use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * List user's favorite apartments.
     */
    public function index(Request $request): JsonResponse
    {
        $favorites = Favorite::where('user_id', $request->user()->id)
            ->with(['apartment.owner', 'apartment.judiciary', 'apartment.images'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => FavoriteResource::collection($favorites->items()),
            'meta' => [
                'current_page' => $favorites->currentPage(),
                'last_page' => $favorites->lastPage(),
                'per_page' => $favorites->perPage(),
                'total' => $favorites->total(),
            ],
        ]);
    }

    /**
     * Toggle favorite on an apartment.
     */
    public function toggle(Request $request, Apartment $apartment): JsonResponse
    {
        $favorite = Favorite::where('user_id', $request->user()->id)
            ->where('apartment_id', $apartment->id)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return response()->json([
                'message' => 'Apartment removed from favorites.',
                'is_favorited' => false,
            ]);
        }

        Favorite::create([
            'user_id' => $request->user()->id,
            'apartment_id' => $apartment->id,
        ]);

        return response()->json([
            'message' => 'Apartment added to favorites.',
            'is_favorited' => true,
        ], 201);
    }
}
