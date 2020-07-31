<?php

namespace App\Observers;

use App\DeviceLog;
use Illuminate\Support\Facades\Log;

class DeviceLogObserver
{
    /**
     * Handle the device log "created" event.
     *
     * @param  \App\DeviceLog  $deviceLog
     * @return void
     */
    public function created(DeviceLog $deviceLog)
    {
        if($deviceLog->capacity < env('CAPACITY_THRESHOLD')){
            $device_name = $deviceLog->device->d_name;
            $body = " حجم مایع دستگاه $device_name زیر ۲۰ درصد است ";
            $title = "اخطار حجم مایع";
            \App\Events\DeviceNotificationEvent::dispatch($title,$body,$deviceLog->device->user->fcm_token);
        }
        if($deviceLog->power < env('POWER_THRESHOLD')){
            $device_name = $deviceLog->device->d_name;
            $body = " ظرفیت باتری دستگاه $device_name زیر ۲۰ درصد است ";
            $title = "اخطار ظرفیت باتری";
            \App\Events\DeviceNotificationEvent::dispatch($title,$body,$deviceLog->device->user->fcm_token);
        }
    }

    /**
     * Handle the device log "updated" event.
     *
     * @param  \App\DeviceLog  $deviceLog
     * @return void
     */
    public function updated(DeviceLog $deviceLog)
    {
        //
    }

    /**
     * Handle the device log "deleted" event.
     *
     * @param  \App\DeviceLog  $deviceLog
     * @return void
     */
    public function deleted(DeviceLog $deviceLog)
    {
        //
    }

    /**
     * Handle the device log "restored" event.
     *
     * @param  \App\DeviceLog  $deviceLog
     * @return void
     */
    public function restored(DeviceLog $deviceLog)
    {
        //
    }

    /**
     * Handle the device log "force deleted" event.
     *
     * @param  \App\DeviceLog  $deviceLog
     * @return void
     */
    public function forceDeleted(DeviceLog $deviceLog)
    {
        //
    }
}
