<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Judiciary extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
    ];

    /**
     * Apartments in this judiciary.
     */
    public function apartments(): HasMany
    {
        return $this->hasMany(Apartment::class);
    }
}
