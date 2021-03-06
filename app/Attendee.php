<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    protected $fillable = ['external_id', 'name'];

    public function events()
    {
        return $this->hasMany('App\EventAttendee');
    }
}
