<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'judiciary_id',
        'title',
        'description',
        'price_usd',
        'number_of_rooms',
        'number_of_bathrooms',
        'size_m2',
        'furnished',
        'parking',
        'minimum_months',
        'latitude',
        'longitude',
        'status',
        'is_verified',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'price_usd' => 'decimal:2',
            'furnished' => 'boolean',
            'parking' => 'boolean',
            'is_verified' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    // ── Relationships ──

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function judiciary(): BelongsTo
    {
        return $this->belongsTo(Judiciary::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ApartmentImage::class)->orderBy('sort_order');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(ApartmentView::class);
    }

    // ── Scopes ──

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeByJudiciary($query, int $judiciaryId)
    {
        return $query->where('judiciary_id', $judiciaryId);
    }

    public function scopeByPriceRange($query, ?float $min, ?float $max)
    {
        if ($min !== null) {
            $query->where('price_usd', '>=', $min);
        }
        if ($max !== null) {
            $query->where('price_usd', '<=', $max);
        }

        return $query;
    }

    public function scopeByRooms($query, ?int $rooms)
    {
        if ($rooms !== null) {
            $query->where('number_of_rooms', $rooms);
        }

        return $query;
    }

    public function scopeFurnished($query)
    {
        return $query->where('furnished', true);
    }

    public function scopeWithParking($query)
    {
        return $query->where('parking', true);
    }
}
