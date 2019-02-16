<?php

namespace App\Policies;

use DMS\Service\Meetup\MeetupKeyAuthClient;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class EventPolicy
{
    use HandlesAuthorization;

    protected $meetup;
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct(MeetupKeyAuthClient $meetup)
    {
        $this->meetup = $meetup;
    }

    public function viewall($user)
    {
        $members = $this->meetup->getProfiles(array(
            'group_urlname' => env("MEETUP_GROUP"),
            'member_id' => $user->provider_id
        ));

        foreach($members as $member)
        {
            if(isset($member['role']) &&
               (strcasecmp($member['role'], 'co-organizer') == 0 ||
                strcasecmp($member['role'], 'event organizer') == 0 ||
                strcasecmp($member['role'], 'organizer') == 0))
            {
                return true;
            }
        }
        return false;
    }

    public function update($user, $event)
    {
        $members = $this->meetup->getProfiles(array(
            'group_urlname' => env("MEETUP_GROUP"),
            'member_id' => $user->provider_id
        ));

        foreach($members as $member)
        {
            if(isset($member['role']) &&
               (strcasecmp($member['role'], 'co-organizer') == 0 ||
                strcasecmp($member['role'], 'event organizer') == 0 ||
                strcasecmp($member['role'], 'organizer') == 0))
            {
                return true;
            }
        }
        return false;
    }

    public function cash($user, $event)
    {
        $members = $this->meetup->getProfiles(array(
            'group_urlname' => env("MEETUP_GROUP"),
            'member_id' => $user->provider_id
        ));

        foreach($members as $member)
        {
            if(isset($member['role']) &&
               (strcasecmp($member['role'], 'co-organizer') == 0 ||
                strcasecmp($member['role'], 'organizer') == 0))
            {
                return true;
            }
        }
        return false;
    }
}
