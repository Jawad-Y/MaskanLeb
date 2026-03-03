<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'apartment_id',
        'message',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    /**
     * Scope to get conversation between two users about an apartment.
     */
    public function scopeConversation($query, int $userA, int $userB, int $apartmentId)
    {
        return $query->where('apartment_id', $apartmentId)
            ->where(function ($q) use ($userA, $userB) {
                $q->where(function ($q2) use ($userA, $userB) {
                    $q2->where('sender_id', $userA)->where('receiver_id', $userB);
                })->orWhere(function ($q2) use ($userA, $userB) {
                    $q2->where('sender_id', $userB)->where('receiver_id', $userA);
                });
            });
    }
}
