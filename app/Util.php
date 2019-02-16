<?php

namespace App;

class Util
{
    public static function local_time($time)
    {
        return \Carbon\Carbon::createFromTimeStamp(strtotime($time))
            ->setTimezone('Asia/Kolkata')->format("d M Y h:i A");
    }
}