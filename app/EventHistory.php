<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventHistory extends Model
{
    protected $fillable = ['user_id', 'event_id', 'description'];
    public function event()
    {
        return $this->belongsTo('App\Event');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getLocalTimeAttribute()
    {
        return \Carbon\Carbon::createFromTimeStamp(strtotime($this->created_at))
            ->setTimezone('Asia/Kolkata')->format("d M Y h:i A");
    }
}
