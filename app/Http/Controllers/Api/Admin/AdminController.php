<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreJudiciaryRequest;
use App\Http\Requests\Api\Admin\UpdateJudiciaryRequest;
use App\Http\Resources\ApartmentResource;
use App\Http\Resources\JudiciaryResource;
use App\Http\Resources\ReportResource;
use App\Http\Resources\UserResource;
use App\Models\Apartment;
use App\Models\Judiciary;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // ── Dashboard Stats ──

    /**
     * Get admin dashboard statistics.
     */
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'data' => [
                'total_users' => User::count(),
                'total_owners' => User::where('role', 'owner')->count(),
                'total_renters' => User::where('role', 'renter')->count(),
                'banned_users' => User::where('is_banned', true)->count(),
                'total_apartments' => Apartment::count(),
                'available_apartments' => Apartment::where('status', 'available')->count(),
                'rented_apartments' => Apartment::where('status', 'rented')->count(),
                'pending_reports' => Report::where('status', 'pending')->count(),
                'total_judiciaries' => Judiciary::count(),
            ],
        ]);
    }

    // ── User Management ──

    /**
     * List all users with optional filtering.
     */
    public function users(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('banned_only')) {
            $query->where('is_banned', true);
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 20));

        return response()->json([
            'data' => UserResource::collection($users->items()),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Ban a user.
     */
    public function banUser(User $user): JsonResponse
    {
        if ($user->isAdmin()) {
            return response()->json(['message' => 'Cannot ban an admin.'], 422);
        }

        $user->update(['is_banned' => true]);

        // Revoke all tokens
        $user->tokens()->delete();

        return response()->json([
            'message' => 'User banned successfully.',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Unban a user.
     */
    public function unbanUser(User $user): JsonResponse
    {
        $user->update(['is_banned' => false]);

        return response()->json([
            'message' => 'User unbanned successfully.',
            'user' => new UserResource($user),
        ]);
    }

    // ── Judiciary Management ──

    /**
     * Store a new judiciary.
     */
    public function storeJudiciary(StoreJudiciaryRequest $request): JsonResponse
    {
        $judiciary = Judiciary::create($request->validated());

        return response()->json([
            'message' => 'Judiciary created successfully.',
            'data' => new JudiciaryResource($judiciary),
        ], 201);
    }

    /**
     * Update a judiciary.
     */
    public function updateJudiciary(UpdateJudiciaryRequest $request, Judiciary $judiciary): JsonResponse
    {
        $judiciary->update($request->validated());

        return response()->json([
            'message' => 'Judiciary updated successfully.',
            'data' => new JudiciaryResource($judiciary),
        ]);
    }

    /**
     * Delete a judiciary (only if no apartments).
     */
    public function destroyJudiciary(Judiciary $judiciary): JsonResponse
    {
        if ($judiciary->apartments()->exists()) {
            return response()->json([
                'message' => 'Cannot delete judiciary with existing apartments.',
            ], 422);
        }

        $judiciary->delete();

        return response()->json(['message' => 'Judiciary deleted successfully.']);
    }

    // ── Apartment Management ──

    /**
     * List all apartments (admin view).
     */
    public function apartments(Request $request): JsonResponse
    {
        $query = Apartment::with(['owner', 'judiciary', 'images'])
            ->withCount(['favorites', 'reviews', 'reports']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('judiciary_id')) {
            $query->where('judiciary_id', $request->judiciary_id);
        }

        $apartments = $query->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 20));

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
     * Verify an apartment listing.
     */
    public function verifyApartment(Apartment $apartment): JsonResponse
    {
        $apartment->update(['is_verified' => true]);

        return response()->json([
            'message' => 'Apartment verified successfully.',
            'data' => new ApartmentResource($apartment),
        ]);
    }

    /**
     * Remove an apartment listing (admin force delete).
     */
    public function removeApartment(Apartment $apartment): JsonResponse
    {
        foreach ($apartment->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $apartment->delete();

        return response()->json(['message' => 'Apartment removed successfully.']);
    }

    // ── Report Management ──

    /**
     * List all reports.
     */
    public function reports(Request $request): JsonResponse
    {
        $query = Report::with(['reporter', 'apartment.owner']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 20));

        return response()->json([
            'data' => ReportResource::collection($reports->items()),
            'meta' => [
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'per_page' => $reports->perPage(),
                'total' => $reports->total(),
            ],
        ]);
    }

    /**
     * Update report status.
     */
    public function updateReport(Request $request, Report $report): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:reviewed,rejected'],
        ]);

        $report->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Report status updated.',
            'data' => new ReportResource($report),
        ]);
    }

    // ── Owner Verification ──

    /**
     * Verify an owner (give them a verified badge).
     */
    public function verifyUser(User $user): JsonResponse
    {
        $user->update(['is_verified' => true]);

        return response()->json([
            'message' => 'User verified successfully.',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Remove verification from a user.
     */
    public function unverifyUser(User $user): JsonResponse
    {
        $user->update(['is_verified' => false]);

        return response()->json([
            'message' => 'User verification removed.',
            'user' => new UserResource($user),
        ]);
    }
}
