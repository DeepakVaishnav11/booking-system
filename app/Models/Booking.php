<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'user_id', 'customer_name', 'customer_email', 'booking_date',
        'booking_type', 'booking_slot', 'from_time', 'to_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
