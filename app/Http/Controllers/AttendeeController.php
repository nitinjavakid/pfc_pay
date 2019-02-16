<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Attendee;
use App\Payment;
use Auth;

class AttendeeController extends Controller
{
    public function index()
    {
        $attendees = Attendee::orderBy('name')->paginate(9);
        return view('attendees.index', ['attendees' => $attendees]);
    }

    public function show(Attendee $attendee)
    {
        $pending = $attendee->events->where('payment_id', '=', null)->where('event.cost', '!=', 0);
        $pendingTotal = 0;
        foreach($pending as $entry)
        {
            $pendingTotal += $entry->event->cost;
        }

        return view('attendees.show',
                    ['pending' => $pending,
                     'attendee' => $attendee,
                     'total' => $pendingTotal
                    ]);
    }

    public function passbook()
    {
        $attendee = \App\Attendee::where('external_id', '=', Auth::user()->provider_id)->first();
        return $this->passbook_by_id($attendee->id);
    }

    public function others_passbook($id)
    {
        $this->authorize('viewall', \App\Event::class);
        return $this->passbook_by_id($id);
    }

    public function passbook_by_id($id)
    {
        $payments = Payment::whereHas('paid_for', function($query) use ($id) {
            $query->whereHas('attendee', function($query) use ($id) {
                $query->where('attendee_id', '=', $id);
            });
        })->orderBy('updated_at', 'desc')->paginate(9);
        return view('attendees.passbook', ['payments' => $payments]);
    }

    public function actions()
    {
        $attendee = \App\Attendee::where('external_id', '=', Auth::user()->provider_id)->first();
        $pending = $attendee->events->where('payment_id', '=', null)->where('event.cost', '!=', 0);
        $total = 0;
        foreach($pending as $entry) {
            $total += $entry->event->cost;
        }

        return view('attendees.show', ['pending' => $pending, 'attendee' => $attendee, 'total' => $total]);
    }
}
