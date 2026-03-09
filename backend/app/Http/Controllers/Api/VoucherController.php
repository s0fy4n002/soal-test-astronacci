<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateVoucherRequest;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Services\SeatGeneratorService;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    public function generate(GenerateVoucherRequest $request)
    {
        $flightNumber = $request->flightNumber;
        $date = $request->date;
        $exists = Voucher::where('flight_number', $flightNumber)
            ->where('flight_date', $date)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher already exists'
            ]);
        }
        
        $occupiedSeats = Voucher::where('flight_number', $flightNumber)
            ->where('flight_date', $date)
            ->pluck('seat')
            ->toArray();

        $service = new SeatGeneratorService();

        $seats = $service->generate($request->aircraft, 3, $occupiedSeats);

        $voucher = Voucher::create([
            'crew_name' => $request->name,
            'crew_id' => $request->id,
            'flight_number' => $request->flightNumber,
            'flight_date' => $request->date,
            'aircraft_type' => $request->aircraft,
            'seat1' => $seats[0],
            'seat2' => $seats[1],
            'seat3' => $seats[2],
        ]);

        return response()->json([
            'success' => true,
            'seats' => $seats
        ]);
    }

    public function check(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'flightNumber' => 'required|string',
                'date' => 'required|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $exists = Voucher::where('flight_number', $request->flightNumber)
                ->where('flight_date', $request->date)
                ->exists();

            return response()->json([
                'success' => true,
                'exists' => $exists
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
