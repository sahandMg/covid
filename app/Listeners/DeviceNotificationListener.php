<?php

namespace App\Listeners;

use App\Events\DeviceNotificationEvent;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeviceNotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DeviceNotificationEvent  $event
     * @return void
     */
    public function handle(DeviceNotificationEvent $event)
    {
//        $api_key = env('NAJVA_API_KEY');
//        $token = env('NAJVA_TOKEN');
//
//        $notification = new \App\NajvaNotif(false);
//        $notification->title = $event->title;
//        $notification->body = $event->body;
//        $notification->onClickAction = "open-link";
//        $notification->url = env('NOTIF_LINK');
//        $notification->content = "nothing special";
//        $notification->json = array(
//            'key'=>'value',
//            'key2'=>'value2'
//        );
//        $notification->subscribersToken = ['129451097','129441009'];
//        $notification->icon = "https://www.ait-themes.club/wp-content/uploads/cache/images/2020/02/guestblog_featured/guestblog_featured-482918665.jpg";
//        $notification->image = "https://www.ait-themes.club/wp-content/uploads/cache/images/2020/02/guestblog_featured/guestblog_featured-482918665.jpg";
//        $time = Carbon::now()->addSeconds(5)->format("Y-m-d H:i:s");
//        $time = str_replace(' ','T',$time);
//        $notification->sentTime = $time;
//        $najva = new \App\Najva($api_key,$token);
//        $result = $najva->sendNotification($notification);
//        echo $result;


        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $token = $event->fcm_token;

        $notification = [
            'title' => $event->title,
            'body'=> $event->body,
            'sound' => true,
        ];

        $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $token, //single token
            'notification' => $notification,
            'data' => $extraNotificationData
        ];

        $headers = [
            'Authorization: key='.env('FCM_SERVER_KEY'),
            'Content-Type: application/json'
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);


    }
}
