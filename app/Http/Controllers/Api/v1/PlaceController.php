<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Place;
use App\Http\Resources\PlaceResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceController extends Controller
{
    public function index():JsonResource
    {
        $places = Place::with('sector', 'reservations')->get();
        return PlaceResource::collection($places);
    }
}
