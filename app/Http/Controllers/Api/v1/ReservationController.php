<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;


class ReservationController extends Controller
{
    public function store(Request $request):JsonResponse
    {
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

        return $this->placeResource($place, 'Reservation added successfully.');
        
    }

    public function cancel (Request $request, Reservation $reservation): JsonResponse
    {
        if($response = $this->ensureUserOwnsReservation ($request, $reservation)) {
            return $response;
        }else{
                DB::transaction (function() use ($reservation) {
                        $reservation->update([
                            'status' => 'cancelled'
                        ]);

                        $reservation->place()->update([
                            'status' => 'available'
                        ]);
                    });

                    return $this->placeResource($reservation->place, 'Reservation cancelled successfully.');
                }
    }

    public function startParking (Request $request, Reservation $reservation): JsonResponse
    {
        if($response = $this->ensureUserOwnsReservation ($request, $reservation)) {
            return $response;
        }else{
                DB::transaction (function() use ($reservation) {
                        $reservation->update([
                            'status' => 'parked',
                            'start_time' => Carbon::now()
                        ]);

                        $reservation->place()->update([
                            'status' => 'occupied'
                        ]);
                    });

                    return $this->placeResource($reservation->place, 'Parking started.');
                }
    }

    public function endParking(Request $request, Reservation $reservation): JsonResponse
    {
       if($response = $this->ensureUserOwnsReservation ($request, $reservation)) {
            return $response;
        }

        DB::transaction(function () use ($reservation) {
            $reservation->update([
                'status' => 'finished',
                'end_time' => now(),
            ]);

            $reservation->place()->update([
                'status' => 'available',
            ]);
        });

        $reservation->refresh()->load('place.sector', 'place.reservations');

        $startTime = Carbon::parse($reservation->start_time);
        $endTime = Carbon::parse($reservation->end_time);

        $hours = ceil($startTime->diffInMinutes($endTime) / 60);

        $place = $reservation->place;
        $sector = $place->sector;
        $pricePerHour = $sector->price;
        $amount = $hours * $pricePerHour;

        $reservation->update([
            'amount' => $amount,
        ]);

        return $this->placeResource($reservation->place, "Parking ended. The amount to pay is $amount for $hours hour(s).");
        
    }

    private function ensureUserOwnsReservation (Request $request, Reservation $reservation):?JsonResponse
    {
        if ($reservation->user_id !== 1) {
            return response()->json([
                'error' => 'Reservation not found.'
            ], 404);
        }

        return null;
    }

    private function placeResource(Place $place, string $message): JsonResponse
    {
        return response()->json([
            'place' => PlaceResource::make($place->load('sector', 'reservations')),        
            'message' => $message
        ]);
    }

}