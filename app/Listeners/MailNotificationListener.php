<?php

namespace App\Listeners;

use App\Events\MailNotificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class MailNotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  MailNotificationEvent  $event
     * @return void
     */
    public function handle(MailNotificationEvent $event)
    {

        Mail::send($event->page,$event->data,function($message)use($event){

            $message->to($event->recipient->email);
            $message->from($event->sender);
            $message->subject($event->subject);
        });
    }
}
