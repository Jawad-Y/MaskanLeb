<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * List conversations for the authenticated user.
     */
    public function conversations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // Get the latest message for each conversation (grouped by other_user + apartment)
        $conversations = Message::select([
            'apartment_id',
            DB::raw("CASE WHEN sender_id = {$userId} THEN receiver_id ELSE sender_id END as other_user_id"),
            DB::raw('MAX(id) as latest_message_id'),
            DB::raw("SUM(CASE WHEN receiver_id = {$userId} AND is_read = 0 THEN 1 ELSE 0 END) as unread_count"),
        ])
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->groupBy('apartment_id', DB::raw("CASE WHEN sender_id = {$userId} THEN receiver_id ELSE sender_id END"))
            ->orderBy('latest_message_id', 'desc')
            ->get();

        // Eager load the related data
        $conversationData = $conversations->map(function ($conv) {
            $latestMessage = Message::with(['sender', 'receiver', 'apartment.images'])
                ->find($conv->latest_message_id);

            $otherUser = \App\Models\User::find($conv->other_user_id);

            return [
                'apartment_id' => $conv->apartment_id,
                'apartment' => $latestMessage?->apartment ? new \App\Http\Resources\ApartmentResource($latestMessage->apartment) : null,
                'other_user' => $otherUser ? new \App\Http\Resources\UserResource($otherUser) : null,
                'last_message' => $latestMessage ? new MessageResource($latestMessage) : null,
                'unread_count' => (int) $conv->unread_count,
            ];
        });

        return response()->json(['data' => $conversationData]);
    }

    /**
     * Get messages in a conversation.
     */
    public function show(Request $request, int $apartmentId, int $userId): JsonResponse
    {
        $authUserId = $request->user()->id;

        $messages = Message::conversation($authUserId, $userId, $apartmentId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->paginate($request->integer('per_page', 50));

        // Mark unread messages as read
        Message::where('apartment_id', $apartmentId)
            ->where('sender_id', $userId)
            ->where('receiver_id', $authUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'data' => MessageResource::collection($messages->items()),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    /**
     * Send a message.
     */
    public function store(SendMessageRequest $request): JsonResponse
    {
        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $request->receiver_id,
            'apartment_id' => $request->apartment_id,
            'message' => $request->message,
        ]);

        $message->load(['sender', 'receiver']);

        return response()->json([
            'message' => 'Message sent successfully.',
            'data' => new MessageResource($message),
        ], 201);
    }

    /**
     * Get unread message count.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = Message::where('receiver_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }
}
