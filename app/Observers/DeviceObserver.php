<?php

namespace App\Observers;

use App\Device;
use App\DeviceEvent;
use App\Log;
use App\Repo;

class DeviceObserver
{
    /**
     * Handle the device "created" event.
     *
     * @param  \App\Device  $device
     * @return void
     */
    public function created(Device $device)
    {
        $repo = new Repo();

        $device->user->update(['role_id'=>$repo->findRoleId('admin')]);

//        $newEvent = new DeviceEvent();
//        $newEvent->type = 'created';
//        $newEvent->unique_id = $device->unique_id;
//        $newEvent->save();

    }

    /**
     * Handle the device "updated" event.
     *
     * @param  \App\Device  $device
     * @return void
     */
    public function updated(Device $device)
    {
//        $newEvent = new DeviceEvent();
//        $newEvent->type = 'update';
//        $newEvent->unique_id = $device->unique_id;
//        $newEvent->save();
    }

    /**
     * Handle the device "deleted" event.
     *
     * @param  \App\Device  $device
     * @return void
     */
    public function deleted(Device $device)
    {
        $newEvent = new DeviceEvent();
        $newEvent->type = 'delete';
        $newEvent->unique_id = $device->unique_id;
        $newEvent->user_id = $device->user_id;
        $newEvent->save();
    }

    /**
     * Handle the device "restored" event.
     *
     * @param  \App\Device  $device
     * @return void
     */
    public function restored(Device $device)
    {
        //
    }

    /**
     * Handle the device "force deleted" event.
     *
     * @param  \App\Device  $device
     * @return void
     */
    public function forceDeleted(Device $device)
    {
        //
    }
}
