<?php


namespace App\Http\Controllers\DeviceResponsables;


use App\Device;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;

class RemoveDeviceData implements Responsable
{

    private $formatter;

    public function __construct($formatter)
    {
        $this->formatter = $formatter;
    }

    public function toResponse($request){

        try{

            $device = Device::where('unique_id',$request->unique_id)->firstOrFail();
            $device->delete();
            $resp = ['status'=>200,'body'=>['type'=>'success','message'=>['scc' =>'دستگاه موردنظر حذف شد']]];

        }catch (\Exception $exception){

            $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['err' => 'دستگاه موردنظر پیدا نشد']]];
            return $exception->getMessage();
        }

        return $resp;
    }
}