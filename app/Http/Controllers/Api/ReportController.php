<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Submit a report on an apartment.
     */
    public function store(StoreReportRequest $request): JsonResponse
    {
        // Check if user already reported this apartment
        $existing = Report::where('reporter_id', $request->user()->id)
            ->where('apartment_id', $request->apartment_id)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'You have already reported this apartment.',
            ], 422);
        }

        $report = Report::create([
            'reporter_id' => $request->user()->id,
            'apartment_id' => $request->apartment_id,
            'reason' => $request->reason,
        ]);

        return response()->json([
            'message' => 'Report submitted successfully.',
            'data' => new ReportResource($report),
        ], 201);
    }

    /**
     * Get user's own reports.
     */
    public function myReports(Request $request): JsonResponse
    {
        $reports = Report::where('reporter_id', $request->user()->id)
            ->with('apartment')
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

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
}
