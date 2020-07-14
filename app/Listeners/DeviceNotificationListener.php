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
        $api_key = env('NAJVA_API_KEY');
        $token = env('NAJVA_TOKEN');

        $notification = new \App\NajvaNotif(true);
        $notification->title = $event->title;
        $notification->body = $event->body;
        $notification->onClickAction = "open-link";
        $notification->url = env('NOTIF_LINK');
        $notification->content = "nothing special";
        $notification->json = array(
            'key'=>'value',
            'key2'=>'value2'
        );
        $notification->icon = "https://www.ait-themes.club/wp-content/uploads/cache/images/2020/02/guestblog_featured/guestblog_featured-482918665.jpg";
        $notification->image = "https://www.ait-themes.club/wp-content/uploads/cache/images/2020/02/guestblog_featured/guestblog_featured-482918665.jpg";
        $time = Carbon::now()->addSeconds(5)->format("Y-m-d H:i:s");
        $time = str_replace(' ','T',$time);
        $notification->sentTime = $time;
        $najva = new \App\Najva($api_key,$token);
        $result = $najva->sendNotification($notification);
//        echo $result;
    }
}
