<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    protected $fillable = ['name', 'description', 'price'];
    
    public function places():HasMany
    {
        return $this->hasMany(Place::class);
    }
}
