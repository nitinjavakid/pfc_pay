<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    public function attendees()
    {
        return $this->hasMany('App\EventAttendee');
    }

    public function sortedAttendees()
    {
        return $this->attendees()->get()->sortBy('attendee.name');
    }

    public function history()
    {
        return $this->hasMany('App\EventHistory')->orderBy("created_at", "desc");
    }

    public function payments()
    {
        return $this->hasMany('App\Payment');
    }

    public function getLocalTimeAttribute()
    {
        return Util::local_time($this->time);
    }
}
