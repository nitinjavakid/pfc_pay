<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentSet extends Model
{
    protected $table = 'payment_sets';
    protected $fillable = ['event_attendee_id'];

    public function payment()
    {
        return $this->belongsTo('App\Payment');
    }

    public function attendee()
    {
        return $this->belongsTo('App\EventAttendee', 'event_attendee_id');
    }
}
