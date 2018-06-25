<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventAttendee extends Model
{
    protected $table = 'event_attendees';
    protected $fillable = ['attendee_id', 'guest'];

    public function attendee()
    {
        return $this->belongsTo('App\Attendee');
    }

    public function event()
    {
        return $this->belongsTo('App\Event');
    }

    public function payment()
    {
        return $this->belongsTo('App\Payment');
    }
}
