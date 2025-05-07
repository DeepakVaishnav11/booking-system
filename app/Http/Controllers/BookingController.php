<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function create()
    {
        return view('bookings.create');
    }

    public function store(Request $request)
    {

        $booking_date = Carbon::parse($request->booking_date)->format('Y/m/d');
        // print_r($request);die;
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_type' => 'required|in:Full Day,Half Day,Custom',
            'booking_slot' => 'nullable|required_if:booking_type,Half Day|in:First Half,Second Half',
            'from_time' => 'nullable|required_if:booking_type,Custom|date_format:HH:mm:ss',
            'to_time' => 'nullable|required_if:booking_type,Custom|date_format:HH:mm:ss|after:from_time',
        ]);


                // Get all bookings by user on that date
            $overlapBookings = Booking::where('booking_date', $booking_date)
            ->get();

        foreach ($overlapBookings as $booking) {

            // FULL DAY – No specific validation needed if you're not blocking other types
            if ($booking->booking_type === 'Full Day') {
                return back()->withErrors(['booking_date' => 'Already have a Full Day booking on this date.']);
            }

            // HALF DAY – Only one "First Half" and one "Second Half" allowed
            if ($request->booking_type === 'Half Day' && $booking->booking_type === 'Half Day') {
                if ($booking->booking_slot === $request->booking_slot) {
                    return back()->withErrors([
                        'booking_slot' => "Already have a {$request->booking_slot} booking on this date."
                    ]);
                }
            }

            // CUSTOM – Prevent overlapping or duplicate custom time ranges
            if ($request->booking_type === 'Custom' && $booking->booking_type === 'Custom') {
                $existingFrom = Carbon::parse($booking->from_time);
                $existingTo = Carbon::parse($booking->to_time);
                $newFrom = Carbon::parse($request->from_time);
                $newTo = Carbon::parse($request->to_time);

                $isOverlap = $newFrom->between($existingFrom, $existingTo) ||
                            $newTo->between($existingFrom, $existingTo) ||
                            ($newFrom->lte($existingFrom) && $newTo->gte($existingTo));

                if ($isOverlap) {
                    return back()->withErrors([
                        'from_time' => 'Your custom booking time overlaps with another booking on this date.'
                    ]);
                }
            }
        }

        // $overlap = Booking::where('booking_date', $booking_date)
        // ->where(function ($query) use ($request) {
        //     if ($request->booking_type == 'Full Day') {
        //         $query->where('booking_type', 'Full Day');
        //     } elseif ($request->booking_type == 'Half Day') {
        //         $query->where('booking_type', 'Full Day')
        //             ->orWhere(function ($query) use ($request) {
        //                 $query->where('booking_type', 'Half Day')
        //                     ->where('booking_slot', $request->booking_slot);
        //             })
        //             // Prevent booking both "First Half" and "Second Half" on the same day
        //             ->orWhere(function ($query) use ($request) {
        //                 if ($request->booking_slot == 'First Half') {
        //                     // Ensure the user is not booking the "Second Half" for the same day
        //                     $query->where('booking_type', 'Half Day')
        //                         ->where('booking_slot', 'Second Half')
        //                         ->where('booking_date', $booking_date);
        //                 } elseif ($request->booking_slot == 'Second Half') {
        //                     // Ensure the user is not booking the "First Half" for the same day
        //                     $query->where('booking_type', 'Half Day')
        //                         ->where('booking_slot', 'First Half')
        //                         ->where('booking_date', $booking_date);
        //                 }
        //             });
        //     } elseif ($request->booking_type == 'Custom') {
        //         $query->where('booking_type', 'Full Day')
        //             ->orWhere(function ($query) use ($request) {
        //                 $query->where('booking_type', 'Half Day')
        //                     ->where('booking_slot', $request->booking_slot);
        //             })
        //             ->orWhere(function ($query) use ($request) {
        //                 $query->where('booking_type', 'Custom')
        //                     ->where(function ($query) use ($request) {
        //                         // Assuming 'from_time' and 'to_time' are stored in database as datetime or timestamp
        //                         $from_time = Carbon::parse($request->from_time);
        //                         $to_time = Carbon::parse($request->to_time);
    
        //                         $query->where(function ($query) use ($from_time, $to_time) {
        //                             // Check if the new time range overlaps with existing bookings
        //                             $query->whereBetween('from_time', [$from_time, $to_time])
        //                                 ->orWhereBetween('to_time', [$from_time, $to_time])
        //                                 ->orWhere(function ($query) use ($from_time, $to_time) {
        //                                     // Check if the new time range completely overlaps any existing range
        //                                     $query->where('from_time', '<=', $to_time)
        //                                         ->where('to_time', '>=', $from_time);
        //                                 });
        //                         });
        //                     });
        //             });
        //     }
        // })->exists();
    
    // if ($overlap) {
    //     return back()->withErrors(['booking_date' => 'The selected booking time overlaps with an existing booking.']);
    // }

        Booking::create([
            'user_id' => Auth::id(),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'booking_date' => $booking_date,
            'booking_type' => $request->booking_type,
            'booking_slot' => $request->booking_slot,
            'from_time' => $request->from_time,
            'to_time' => $request->to_time,
        ]);

        return redirect()->route('bookings.create')->with('success', 'Booking successfully created!');
    }
}
