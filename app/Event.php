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

    public function getLocalTimeAttribute()
    {
        return \Carbon\Carbon::createFromTimeStamp(strtotime($this->time))
            ->setTimezone('Asia/Kolkata')->format("d M Y h:i A");
    }

    public function history()
    {
        return $this->hasMany('App\EventHistory')->orderBy("created_at", "desc");
    }
}
