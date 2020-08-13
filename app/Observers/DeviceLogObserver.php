<?php

namespace App\Observers;

use App\DeviceLog;
use Illuminate\Support\Facades\Cache;
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
        $id = $deviceLog->device->unique_id;

        if($deviceLog->capacity < env('CAPACITY_THRESHOLD')){
            $device_name = $deviceLog->device->d_name;
            $body = " حجم الکل دستگاه $device_name زیر ۲۰ درصد است ";
            $title = "اخطار حجم الکل";
            if(!Cache::has($id.'_cap')){
                Cache::forever($id.'_cap',1);
                \App\Events\DeviceNotificationEvent::dispatch($title,$body,$deviceLog->device->user->fcm_token);
            }
        }else{
            Cache::forget($id.'_cap');
        }
        if($deviceLog->power < env('POWER_THRESHOLD')){
            $device_name = $deviceLog->device->d_name;
            $body = " ظرفیت باتری دستگاه $device_name زیر ۲۰ درصد است ";
            $title = "اخطار ظرفیت باتری";
            if(!Cache::has($id.'_pow')) {
                \App\Events\DeviceNotificationEvent::dispatch($title, $body, $deviceLog->device->user->fcm_token);
                Cache::forever($id . '_pow', 1);
            }
        }else{
            Cache::forget($id.'_pow');
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
