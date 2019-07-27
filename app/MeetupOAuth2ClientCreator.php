<?php

namespace App;

use DMS\Service\Meetup\MeetupOAuth2Client;
use App\User;

class MeetupOAuth2ClientCreator
{
    public static function create()
    {
        $users = explode(",", env('MEETUP_API_USERS'));
        $user = User::where('provider_id', $users)->whereNotNull('access_token')->first();
        if($user)
        {
            return MeetupOAuth2Client::factory(array('access_token' => $user->access_token));
        }
        else
        {
            return null;
        }
    }
}