<?php

namespace App\Http\Controllers\DeviceResponsables;

use App\Device;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpdateDeviceData implements Responsable {


    public function toResponse($request){

        try{
            $device = Device::where('unique_id',$request['unique_id'])->firstOrFail();

            $device->update($request);

            $device->update(['user_id'=> $device->user->id]);

        }catch (\Exception $exception){

            Log::error($exception->getMessage());

        }
    }
}

?>