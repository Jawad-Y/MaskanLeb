<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\ApartmentView;
use App\Models\Favorite;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerAnalyticsController extends Controller
{
    /**
     * Get analytics overview for the authenticated owner.
     */
    public function overview(Request $request): JsonResponse
    {
        $ownerId = $request->user()->id;

        $apartmentIds = Apartment::where('owner_id', $ownerId)->pluck('id');

        $totalViews = ApartmentView::whereIn('apartment_id', $apartmentIds)->count();
        $uniqueViews = ApartmentView::whereIn('apartment_id', $apartmentIds)
            ->distinct('ip_address')->count('ip_address');
        $totalFavorites = Favorite::whereIn('apartment_id', $apartmentIds)->count();
        $totalInquiries = Message::whereIn('apartment_id', $apartmentIds)
            ->where('receiver_id', $ownerId)
            ->count();

        $apartmentStats = Apartment::where('owner_id', $ownerId)
            ->select([
                'id',
                'title',
                'status',
                'views_count',
                'created_at',
            ])
            ->withCount(['favorites', 'views'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($apt) => [
                'id' => $apt->id,
                'title' => $apt->title,
                'status' => $apt->status,
                'views_count' => $apt->views_count,
                'favorites_count' => $apt->favorites_count,
                'inquiry_count' => Message::where('apartment_id', $apt->id)
                    ->where('receiver_id', $ownerId)
                    ->distinct('sender_id')
                    ->count('sender_id'),
            ]);

        return response()->json([
            'data' => [
                'total_listings' => $apartmentIds->count(),
                'total_views' => $totalViews,
                'unique_views' => $uniqueViews,
                'total_favorites' => $totalFavorites,
                'total_inquiries' => $totalInquiries,
                'apartments' => $apartmentStats,
            ],
        ]);
    }

    /**
     * Get daily views for a specific apartment (last 30 days).
     */
    public function apartmentViews(Request $request, Apartment $apartment): JsonResponse
    {
        if ($request->user()->id !== $apartment->owner_id && ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $views = ApartmentView::where('apartment_id', $apartment->id)
            ->where('viewed_at', '>=', now()->subDays(30))
            ->select([
                DB::raw('DATE(viewed_at) as date'),
                DB::raw('COUNT(*) as total_views'),
                DB::raw('COUNT(DISTINCT ip_address) as unique_views'),
            ])
            ->groupBy(DB::raw('DATE(viewed_at)'))
            ->orderBy('date')
            ->get();

        return response()->json([
            'data' => [
                'apartment_id' => $apartment->id,
                'title' => $apartment->title,
                'daily_views' => $views,
            ],
        ]);
    }
}
