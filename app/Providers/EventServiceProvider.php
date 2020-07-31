<?php

namespace App\Providers;

use App\Events\CapacityNotificationEvent;
use App\Events\DeviceNotification;
use App\Events\DeviceNotificationEvent;
use App\Events\MailNotificationEvent;
use App\Events\PowerNotificationEvent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        DeviceNotificationEvent::class => ['App\Listeners\DeviceNotificationListener'],
        MailNotificationEvent::class => ['App\Listeners\MailNotificationListener'],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
