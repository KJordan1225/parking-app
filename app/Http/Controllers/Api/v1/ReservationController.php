<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function store(Request $request):JsonResponse
    {
        
        User::create([
            'name' => 'test',
            'email' => 'test@email.com',
            'password' => 'password'
        ]);
        
        //check if user already has a reservation
        $reservationExists = Reservation::where([
            'user_id' => 1,
            'status' => 'reserved'
        ])->exists();

        //if yes
        if($reservationExists) {
        return response()->json([
            'error' => 'You already have a reserved place. Please cancel it before making a new reservation.'
            ], 400);
        };

         //check if user already has parked
        $reservationParked = Reservation::where([
            'user_id' => 1,
            'status' => 'parked'
        ])->exists();

        //if yes
        if($reservationParked) {
        return response()->json([
            'error' => 'You already have a parked place. Please end it before making a new reservation.'
            ], 400);
        };

        //find the place
        $place = Place::find($request->place_id);

        if(!$place || $place->status !== 'available') {
            return response()->json([
            '   error' => 'Place not found or not available.'
            ]);
        }

        DB::transaction (function() use ($place, $request) {
            $reservation = Reservation::create([
                'user_id' => 1,
                'place_id' => $place->id
             ]);

              $reservation->place()->update([
                'status' => 'reserved'
            ]);
        });

        //refresh the place to get the updated status
        $place->refresh();

        return response()->json([
            'place' => PlaceResource::make($place->load('sector', 'reservations')),        
            'message' => 'Reservation added successfully.'
        ]);
    }
}
