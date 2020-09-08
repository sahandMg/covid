<?php

/**
 * Created by PhpStorm.
 * User: Sahand
 * Date: 7/31/20
 * Time: 11:53 PM
 */

namespace App\Http\Controllers\DeviceResponsables;

use App\Device;
use App\Repo;
use App\SharedKey;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class Chart implements Responsable {

    public function __construct()
    {

    }

    public function toResponse($request){

        $repo = new Repo();
        $date = $repo->convertJalali($request->date);
//        $date = $request->date;
        try{

            try{

                $device = Device::where('unique_id',$request->unique_id)->firstOrFail();
            }
            catch (\Exception $exception){

                return $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['دستگاهی یافت نشد']]];
            }
            $user = Auth::guard('user')->user();
            $user->role_id == $repo->findRoleId('user') ? $id = SharedKey::where('user_id',$user->id)->first()->admin_id:$id = $user->id;

            $deviceReports = DB::table('reports')->where('device_id',$device->id)
                ->where('created_at','>',Carbon::parse($date))
                ->where('user_id',$id)
                ->orderBy('created_at','desc')->select('id','total_pushed','created_at')
                ->get();

            if(count($deviceReports) == 0){

                return $resp = ['status'=>404,'body'=>['type'=>'error','message'=>[],'date'=>Carbon::now()->format("Y-m-d H:i:s")]];
            }
            if($request->filter_name == 'day'){

                $result = [];

                foreach ($deviceReports as $deviceReport){


                    array_push($result,['total_pushed'=>$deviceReport->total_pushed,'date'=> $repo->converte2p(Jalalian::fromCarbon(Carbon::parse($deviceReport->created_at))->format('Y-m-d')) ]);
                }

                return $resp = ['status'=>200,'body'=>['type'=>'day','message'=>array_reverse($result),'date'=>Carbon::now()->format("Y-m-d H:i:s")]];

            }
            elseif ($request->filter_name == 'week'){

                $total_push = 0;
                $endDate = Carbon::parse($deviceReports[count($deviceReports)-1]->created_at);
                $today = Carbon::yesterday();
                $today2 = Carbon::yesterday();
                $result = [];
                $i = 1;
                $days = 0;
                while ($today->greaterThanOrEqualTo($endDate)){

                    foreach ($deviceReports as $deviceReport){

                        $queryDate = Carbon::parse($deviceReport->created_at);
                        $today = Carbon::yesterday();
                        $lastWeekDay = $today->subDays(7*$i);

                        if($queryDate->greaterThanOrEqualTo($lastWeekDay) && $queryDate->lessThan(Carbon::yesterday()->endOfDay()->subDays(7*($i-1)+$days))){

                            $total_push = $total_push + $deviceReport->total_pushed;

                        }else{

                        }
                    }
//                    array_push($result,['total_pushed'=>$total_push,'date'=>$repo->converte2p(Jalalian::fromCarbon($today2)->format('Y-m-d')).'*'.$repo->converte2p(Jalalian::fromCarbon($today2)->subDays(7)->format('Y-m-d'))]);
                    array_push($result,['total_pushed'=>$total_push,'date'=>$repo->converte2p(Jalalian::fromCarbon($today2)->format('Y-m-d'))]);
                    $total_push = 0;
                    $today2->subDays(8);
                    $i += 1;
                    $days = 1;
                }

                return $resp = ['status'=>200,'body'=>['type'=>'week','message'=>$result,'date'=>Carbon::now()->format("Y-m-d H:i:s")]];
            }
            elseif ($request->filter_name == 'month'){

                $total_push = 0;
                $endDate = Carbon::parse($deviceReports[count($deviceReports)-1]->created_at)->firstOfMonth();
                $today2 = Carbon::yesterday();
                $result = [];
                $i = 1;
                while ($today2->greaterThanOrEqualTo($endDate)){

                    foreach ($deviceReports as $deviceReport){

                        $queryDate = Carbon::parse($deviceReport->created_at);
                        if($today2->firstOfMonth()->equalTo($queryDate->firstOfMonth())){

                            $total_push = $total_push + $deviceReport->total_pushed;
                        }else{

                        }
                    }
                    array_push($result,['total_pushed'=>$total_push,'date'=>$repo->converte2p(Jalalian::fromCarbon($today2)->format('%B %y'))]);
                    $total_push = 0;
                    $today2->subMonths(1);
                    $i += 1;
                }
                return $resp = ['status'=>200,'body'=>['type'=>'month','message'=>$result,'date'=>Carbon::now()->format("Y-m-d H:i:s")]];
            }else{

                return $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['این فیلتر موجود نیست']]];
            }
//            return $deviceReports;

        }catch (\Exception $exception){

            return $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage().' '.$exception->getLine()]];
        }
    }
}

?>