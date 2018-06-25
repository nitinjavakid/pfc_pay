<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

use DMS\Service\Meetup\MeetupKeyAuthClient;
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
    protected $client;
    public function __construct(MeetupKeyAuthClient $client)
    {
        parent::__construct();
        $this->client = $client;
    }


    protected function addEvent($event)
    {
        $inevent = Event::where('external_id', $event['id'])->first();
        if($inevent == null)
        {
            DB::transaction(function() use ($event) {
                $newevent = new Event();
                $newevent->name = $event['name'];
                $newevent->external_id = $event['id'];
                $newevent->time = Carbon::createFromTimestamp($event['time']/1000);
                $newevent->cost = 0.0;

                $rsvps = $this->client->getRsvps(array(
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
        $events = $this->client->getEvents(array(
            'group_urlname' => env("MEETUP_GROUP"),
            'status' => 'past',
            'desc' => 'true'
        ));

        $from = strtotime(date("Y-m-d", time()) . " -3 month");
        foreach($events as $event) {
            if($event['yes_rsvp_count'] == 0) continue;

            if($event['time']/1000 <= $from) continue;

            $this->addEvent($event);
        }

        $events = $this->client->getEvents(array(
            'group_urlname' => env("MEETUP_GROUP"),
            'status' => 'upcoming',
            'page' => 10
        ));

        foreach($events as $event) {
            if($event['time']/1000 > time()) continue;

            $this->addEvent($event);
        }
    }
}
