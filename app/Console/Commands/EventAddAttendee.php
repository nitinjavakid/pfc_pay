<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Attendee;
use App\Event;
use Illuminate\Support\Facades\DB;

class EventAddAttendee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:add_attendee {id : ID of event} {attendee_id : ID of attendee} {guest : Guest boolean}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add attendee to event';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = $this->argument("id");
        $attendee_id = $this->argument('attendee_id');
        $guest = $this->argument('guest');
        DB::transaction(function() use ($id, $attendee_id, $guest) {
            $event = Event::findOrFail($id);
            $attendee = Attendee::findOrFail($attendee_id);
            $event->attendees()->create([
                "attendee_id" => $attendee->id,
                "guest" => ($guest === 'true')
            ]);
        });
    }
}
