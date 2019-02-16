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
        return Util::local_time($this->created_at);
    }
}
