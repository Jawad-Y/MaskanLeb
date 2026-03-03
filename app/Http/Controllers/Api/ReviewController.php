<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Apartment;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Get reviews for an apartment.
     */
    public function index(Request $request, Apartment $apartment): JsonResponse
    {
        $reviews = $apartment->reviews()
            ->with(['reviewer'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => ReviewResource::collection($reviews->items()),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'average_rating' => round((float) $apartment->reviews()->avg('rating'), 1),
            ],
        ]);
    }

    /**
     * Store a new review.
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $apartment = Apartment::findOrFail($request->apartment_id);

        // Prevent owner from reviewing their own apartment
        if ($request->user()->id === $apartment->owner_id) {
            return response()->json([
                'message' => 'You cannot review your own apartment.',
            ], 422);
        }

        // Check if already reviewed
        $existing = Review::where('reviewer_id', $request->user()->id)
            ->where('apartment_id', $apartment->id)
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'You have already reviewed this apartment.',
            ], 422);
        }

        $review = Review::create([
            'reviewer_id' => $request->user()->id,
            'owner_id' => $apartment->owner_id,
            'apartment_id' => $apartment->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $review->load('reviewer');

        return response()->json([
            'message' => 'Review submitted successfully.',
            'data' => new ReviewResource($review),
        ], 201);
    }

    /**
     * Delete a review.
     */
    public function destroy(Request $request, Review $review): JsonResponse
    {
        if ($request->user()->id !== $review->reviewer_id && ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully.']);
    }

    /**
     * Get reviews for an owner (all their apartments).
     */
    public function ownerReviews(Request $request, int $ownerId): JsonResponse
    {
        $reviews = Review::where('owner_id', $ownerId)
            ->with(['reviewer', 'apartment'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        $avgRating = Review::where('owner_id', $ownerId)->avg('rating');

        return response()->json([
            'data' => ReviewResource::collection($reviews->items()),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'average_rating' => $avgRating ? round((float) $avgRating, 1) : null,
            ],
        ]);
    }
}
