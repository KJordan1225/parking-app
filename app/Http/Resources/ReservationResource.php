<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'place_id' => $this->place_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'amount' => $this->amount,
            'paid' => $this->paid,
            'status' => $this->status,            
        ];
    }
}
