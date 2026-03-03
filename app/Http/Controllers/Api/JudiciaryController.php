<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JudiciaryResource;
use App\Models\Judiciary;
use Illuminate\Http\JsonResponse;

class JudiciaryController extends Controller
{
    /**
     * List all judiciaries.
     */
    public function index(): JsonResponse
    {
        $judiciaries = Judiciary::withCount('apartments')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => JudiciaryResource::collection($judiciaries),
        ]);
    }

    /**
     * Get apartments in a specific judiciary.
     */
    public function apartments(Judiciary $judiciary): JsonResponse
    {
        $apartments = $judiciary->apartments()
            ->with(['owner', 'images'])
            ->available()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'judiciary' => new JudiciaryResource($judiciary),
            'data' => \App\Http\Resources\ApartmentResource::collection($apartments->items()),
            'meta' => [
                'current_page' => $apartments->currentPage(),
                'last_page' => $apartments->lastPage(),
                'per_page' => $apartments->perPage(),
                'total' => $apartments->total(),
            ],
        ]);
    }
}
