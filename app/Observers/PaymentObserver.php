<?php

namespace App\Observers;

use App\Payment;

class PaymentObserver
{
    public function updating(Payment $payment)
    {
        if($payment->status != 'paid')
        {
            return true;
        }

        foreach($payment->paid_for as $payee)
        {
            $attendee = $payee->attendee;
            $attendee->payment_id = $payment->id;
            $attendee->save();
            $done = true;
            foreach($attendee->event->attendees as $attendee)
            {
                if($attendee->payment_id == null)
                {
                    $done = false;
                }
            }

            if($done)
            {
                $attendee->event->status = 'received';
                $attendee->event->save();
            }
        }

        return true;
    }

    public function deleting(Payment $user)
    {
        //
    }
}