<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    public function event()
    {
        return $this->belongsTo('App\Event');
    }

    public function paid_for()
    {
        return $this->hasMany('App\PaymentSet');
    }
}
