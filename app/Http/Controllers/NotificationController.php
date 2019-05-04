<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //
    public function save_subscription(Request $request)
    {
        $user = \Auth::user();
        $user->updatePushSubscription($request->input('endpoint'),
                                      $request->input('keys.p256dh'),
                                      $request->input('keys.auth'));
    }

    public function delete_subscription(Request $request)
    {
        $user = \Auth::user();
        $user->deletePushSubscription($request->input('endpoint'));
    }
}
