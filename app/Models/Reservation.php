<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = ['user_id', 'place_id', 'status', 'start_time',
        'end_time', 'amount', 'paid'
    ];

    protected function casts(): array
    {
        return [
            'start time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function place():BelongsTo
    {
        return $this->belongsTo(Place::class);
    }
}
