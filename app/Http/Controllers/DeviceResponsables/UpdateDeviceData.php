<?php

namespace App\Http\Controllers\DeviceResponsables;

use App\Device;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpdateDeviceData implements Responsable {


    public function toResponse($request){

        try{

            gettype($request) == 'object' ? $request = $request->all():$request;
            $device = Device::where('unique_id',$request['unique_id'])->firstOrFail();

            $device->update($request);

            $device->update(['user_id'=> $device->user->id]);

        }catch (\Exception $exception){

            Log::error($exception->getMessage());

        }

        return $resp = ['status'=>200,'body'=>['type'=>'success','message'=>['scc' =>'اطلاعات دستگاه به روز شد']]];
    }
}

?>