<?php

namespace App\Http\Controllers\DeviceResponsables;

use App\Device;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpdateDeviceData implements Responsable {


    public function toResponse($request,$updateUser = null){

        try{

            gettype($request) == 'object' ? $request = $request->all():$request;
            $device = Device::where('unique_id',$request['unique_id'])->firstOrFail();
            isset($request['location'])?$request['city'] = $request['location']:null;
            $device->update($request);
            if($updateUser == 1){
                $temp = $device->toArray();
                $temp['user_id'] = $device->user->id;
                $device->delete();
                Device::create($temp);
            }

        }catch (\Exception $exception){

            Log::error($exception->getMessage());

        }

        return $resp = ['status'=>200,'body'=>['type'=>'success','message'=>['scc' =>'اطلاعات دستگاه به روز شد']]];
    }
}

?>
