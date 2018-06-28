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
        foreach(EventAttendee::where('payment_id', '=', null)->get() as $ea)
        {
            $pending += $ea->event->cost;
        }

        return view('reports.index', [
            'expense' => $expense,
            'payments_received' => $collection,
            'payments_pending' => $pending
        ]);
    }
}
