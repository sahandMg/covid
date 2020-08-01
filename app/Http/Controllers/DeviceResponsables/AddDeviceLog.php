<?php

/**
 * Created by PhpStorm.
 * User: Sahand
 * Date: 7/31/20
 * Time: 3:10 PM
 */

namespace App\Http\Controllers\DeviceResponsables;

use App\DeviceLog;
use Illuminate\Contracts\Support\Responsable;

class AddDeviceLog implements Responsable {

    private $device;
    public function __construct($device)
    {
        $this->device = $device;
    }
    
    public function toResponse($request){

        $deviceLog = new DeviceLog();
        $deviceLog->power = $request['power'];
        $deviceLog->capacity = $request['capacity'];
        $deviceLog->push = $request['push'];
        $deviceLog->device_id = $this->device->id;;
        $deviceLog->user_id = $this->device->user->id;
        $deviceLog->save();
    }
}

?>