<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Attendee;

class AttendeeController extends Controller
{
    public function index()
    {
        $attendees = Attendee::orderBy('name')->paginate(9);
        return view('attendees.index', ['attendees' => $attendees]);
    }
}
