<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    protected $fillable = ['sector_id', 'place_number', 'status'];

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }
    
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
