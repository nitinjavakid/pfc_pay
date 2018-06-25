<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DMS\Service\Meetup\MeetupKeyAuthClient;
use Instamojo\Instamojo;

use App\Event;
use App\Payment;
use App\Http\Requests\ReCaptchaRequest;

class EventController extends Controller
{
    protected $insta;

    public function __construct(Instamojo $insta)
    {
        $this->insta = $insta;
    }

    public function index()
    {
        $events = Event::orderBy('time', 'desc')->paginate(9);
        return view('events.index', ['events' => $events]);
    }

    public function show($id)
    {
        return view('events.show', ['event' => Event::findOrFail($id)]);
    }

    public function payment($id, Request $request, ReCaptchaRequest $recaptcha)
    {
        $event = Event::findOrFail($id);
        if(($request->input("type") != "instamojo" &&
           $request->input("type") != "cash" &&
           $request->input("type") != "paytm") ||
           $request->input("attendee") == null)
        {
            return redirect()->route('events.show', ["id" => $id]);
        }

        $perperson = $event->cost;
        if($request->input("type") == "cash" || $request->input("type") == "paytm")
        {
            $this->authorize('cash', $event);
            $perperson = $request->input("newcost");
        }

        $attendees = $event->attendees;
        $payfor = [];
        foreach($request->input("attendee") as $selected)
        {
            foreach($attendees as $attendee)
            {
                if($attendee->attendee_id == $selected)
                {
                    if($attendee->payment_id != null)
                    {
                        print("Payment already received for " . $attendee->attendee->name);
                        return;
                    }
                    else
                    {
                        array_push($payfor, $attendee);
                    }
                }
            }
        }

        $cost = $perperson * count($payfor);

        $redirect = redirect()->route('events.show', ["id" => $id]);

        $payment = new Payment();
        $payment->net_amount = $cost;
        if($request->input("type") == "instamojo")
        {
            $purpose = $event->name . " " . $event->local_time . " " . " for ";
            foreach($payfor as $user)
            {
                 $purpose = $purpose . $user->attendee->name . ", ";
            }

            $purpose = substr($purpose, 0, 255);
            $response = $this->insta->paymentRequestCreate(array(
                "purpose" => $purpose,
                "amount" => $cost,
                "webhook" => route("events.payment_status", ['id'=>$event->id]),
                "redirect_url" => route("events.show", ['id'=>$event->id]),
                "expires_at" => Carbon::now()->addSeconds(60)->toIso8601ZuluString()
            ));

            $payment->type = 'instamojo';
            $payment->status = 'pending';
            $payment->external_id = $response['id'];
            $redirect = redirect()->away($response['longurl']);
        }
        else if($request->input("type") == "cash" || $request->input("type") == "paytm")
        {
            $payment->type = $request->input("type");
            $payment->status = 'pending';
        }

        $payment->event_id = $event->id;

        DB::transaction(function() use ($payfor, $request, $payment) {
            $payment->save();

            foreach($payfor as $attendee)
            {
                $paidfor = $payment->paid_for()->create([
                    "event_attendee_id" => $attendee->id
                ]);

                $paidfor->save();
            }

            if($payment->type == 'cash' || $payment->type == 'paytm')
            {
                $payment->status = 'paid';
                $payment->save();
            }
        });

        return $redirect;
    }

    protected function is_request_valid(Request $request)
    {
        $data = $request->all();
        $mac_provided = $data['mac'];  // Get the MAC from the POST data
        unset($data['mac']);  // Remove the MAC key from the data.
        $ver = explode('.', phpversion());
        $major = (int) $ver[0];
        $minor = (int) $ver[1];
        if($major >= 5 and $minor >= 4){
            ksort($data, SORT_STRING | SORT_FLAG_CASE);
        }
        else{
            uksort($data, 'strcasecmp');
        }

        // Pass the 'salt' without <>
        $mac_calculated = hash_hmac("sha1", implode("|", $data), env('INSTAMOJO_SALT'));
        if($mac_provided != $mac_calculated){
            return false;
        }
        return true;
    }

    public function payment_status($id, Request $request)
    {
        if(!$this->is_request_valid($request))
        {
            Log::error("request_not_valid");
            return;
        }

        Log::debug($request);

        if($request->input("status") == "Credit")
        {
            DB::transaction(function() use ($request) {
                $payment = Payment::where(["external_id" => $request->input('payment_request_id')])->first();
                $payment->net_amount = float($request->input("amount")) - float($request->input("fees"));
                $payment->status = 'paid';
                $payment->save();
            });
        }

        return redirect()->route('events.show', ["id" => $id]);
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $description = "";

        if($request->input("cost") != null)
        {
            if($event->cost != $request->input("cost"))
            {
                $description .= "Cost change " . $event->cost . " to " . $request->input("cost") . "\n";
            }
            $event->cost = $request->input("cost");
        }

        if($request->input("water") != null)
        {
            if($event->water != $request->input("water"))
            {
                $description .= "Water change " . $event->water . " to " . $request->input("water") ."\n";
            }
            $event->water = $request->input("water");
        }

        if($request->input("ground") != null)
        {
            if($event->ground != $request->input("ground"))
            {
                $description .= "Ground change " . $event->ground . " to " . $request->input("ground") . "\n";
            }
            $event->ground = $request->input("ground");
        }

        if($request->input("comment") != null)
        {
            if($event->comment != $request->input("comment"))
            {
                $description .= "Comment change " . $event->comment . " to " . $request->input("comment") . "\n";
            }
            $event->comment = $request->input("comment");
        }

        if($request->input("settled") != null)
        {
            $description .= "Settled";
            $event->status = 'settled';
        }

        DB::transaction(function() use ($event, $description) {
            $event->save();

            $event->history()->create([
                "user_id" => Auth::id(),
                "description" => $description
            ]);
        });

        return redirect()->route('events.show', ["id" => $event->id]);
    }
}
