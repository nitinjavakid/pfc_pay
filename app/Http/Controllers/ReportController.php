<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use App\Payment;
use App\EventAttendee;

class ReportController extends Controller
{
    public function index()
    {
        $expense = Event::sum('ground') + Event::sum('water');
        $collection = Payment::where('status', '=', 'paid')->sum('net_amount');
        $pending = 0.0;
        $pendingea = EventAttendee::whereNull('payment_id')
                   ->whereHas('event', function($query) {
                       $query->where('cost', '!=', 0);
                   })
                   ->orderBy('attendee_id')->get();
        foreach($pendingea as $ea)
        {
            $pending += $ea->event->cost;
        }

        return view('reports.index', [
            'expense' => $expense,
            'payments_received' => $collection,
            'payments_pending' => $pending,
            'pendingea' => $pendingea
        ]);
    }
}
