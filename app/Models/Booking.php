<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{

    use HasFactory;
    public function bills()
    {
        return $this->hasMany(BookingBill::class, 'booking_id');
    }

    public function bill()
    {
        return $this->hasOne(BookingBill::class, 'booking_id');
    }
    // Relationship with BookingPayment
    public function payments()
    {
        return $this->hasMany(BookingPayment::class, 'booking_id');
    }

    public function extention()
    {
        return $this->hasMany(BookingExtendHistory::class, 'booking_id');
    }


    public function dress()
{
    return $this->belongsTo(Dress::class, 'dress_id'); // assuming 'dress_id' is the foreign key in the booking table
}

}
