<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Attendee;

class SearchAttendee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendee:search {name : name of attendee}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search attendee';

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
        $members = Attendee::where('name', 'like', $this->argument("name"))->get();

        foreach($members as $member)
        {
            echo "ID: " . $member->id . "\n";
            echo "Name: " . $member->name . "\n";
        }
    }
}
