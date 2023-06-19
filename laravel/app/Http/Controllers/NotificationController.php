<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Vcard;

class NotificationController extends Controller
{
    public function store(Request $request)
    {
        $newNotification = Notification::create($request->all());;
        return $newNotification;
    }

    public function getVcardNotifications(Request $request, Vcard $vcard){
        return Notification::where('vcard_id', $vcard->phone_number)->get();
    }

    public function update(Request $request, Notification $notification){
        $notification = Notification::where('datetime',  $request->datetime)->update(['lida' => $request->lida]);
        return $notification;
    }
}
