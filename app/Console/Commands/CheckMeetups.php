<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

use DMS\Service\Meetup\MeetupOAuth2Client;
use App\MeetupOAuth2ClientCreator;
use App\Event;
use App\Attendee;
use Carbon\Carbon;

class CheckMeetups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:meetup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check meetup';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    protected function addEvent($client, $event)
    {
        $inevent = Event::where('external_id', $event['id'])->first();
        if($inevent == null)
        {
            DB::transaction(function() use ($client, $event) {
                $newevent = new Event();
                $newevent->name = $event['name'];
                $newevent->external_id = $event['id'];
                $newevent->time = Carbon::createFromTimestamp($event['time']/1000);
                $newevent->cost = 0.0;

                $rsvps = $client->getRsvps(array(
                    'event_id' => $event['id'],
                    'rsvp' => 'yes'
                ));

                $newevent->save();

                foreach($rsvps as $rsvp)
                {
                    $member = Attendee::firstOrCreate(
                        ['external_id' => $rsvp['member']['member_id']],
                        ['name' => $rsvp['member']['name']]
                    );

                    $member->save();
                    $ea = $newevent->attendees()->create([
                        "attendee_id" => $member->id
                    ]);

                    for($i=0; $i < $rsvp['guests']; $i++)
                    {
                        $ea = $newevent->attendees()->create([
                            "attendee_id" => $member->id,
                            "guest" => true
                        ]);
                    }
                }
            });
        }
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = MeetupOAuth2ClientCreator::create();
        $events = $client->getEvents(array(
            'group_urlname' => env("MEETUP_GROUP"),
            'status' => 'past',
            'desc' => 'true'
        ));

        $from = strtotime(date("Y-m-d", time()) . " -3 month");
        foreach($events as $event) {
            if($event['yes_rsvp_count'] == 0) continue;

            if($event['time']/1000 <= $from) continue;

            $this->addEvent($client, $event);
        }

        $events = $client->getEvents(array(
            'group_urlname' => env("MEETUP_GROUP"),
            'status' => 'upcoming',
            'page' => 10
        ));

        foreach($events as $event) {
            if($event['time']/1000 > date_timestamp_get(date_add(date_create(),
	                             date_interval_create_from_date_string(env("TEAMS_BEFORE"))))) continue;

            $this->addEvent($client, $event);
        }
    }
}
