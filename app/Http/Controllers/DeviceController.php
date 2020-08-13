<?php

namespace App\Http\Controllers;


use App\DeviceLog;
use App\Http\Controllers\DeviceResponsables\AddDevice;
use App\Http\Controllers\DeviceResponsables\Chart;
use App\Http\Controllers\DeviceResponsables\DeviceList;
use App\Http\Controllers\DeviceResponsables\deviceReport;
use App\Http\Controllers\DeviceResponsables\RemoveDeviceData;
use App\Http\Controllers\DeviceResponsables\Sharing;
use App\Http\Controllers\DeviceResponsables\UpdateDeviceData;
use App\Services\Device\RequestValidationService;
use App\Services\ReturnMsgFormatter;
use Illuminate\Http\Request;

class DeviceController extends Controller
{

    private $formatter;

    public function __construct(ReturnMsgFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    //    ============ Updating Device Information ============
    /*
     * Data Needed : name|ssid|w_ssid|region|city and token and unique_id to find the device
     * Data returns : name,token,message
     */
    public function update(Request $request){


        return new UpdateDeviceData($this->formatter);

    }

    //    ============ Removing Device form Database ============
    /*
     * Data Needed : unique_id,token
     * Data returns : name,token,message
     */

    public function remove(Request $request,RequestValidationService $rq){

        $val = $rq->removeDevice($request);
        if(!is_null($val)){

            return $val;
        }

        return new RemoveDeviceData($request);
    }


    //    ============ Receiving Data From Devices and store them in database ============

    /*
     * Data Needed : power,capacity,push,unique_id,type
     * Data returns : time and date
     * Device Middleware will check the entry data and validate them
     */
    public function sendData(DeviceLog $deviceLog){

        return new AddDevice($deviceLog);


    }


    //    ============ send device list to related admin and user (if admin key has been registered before)  ============

    /*
     * Data Needed : token,date
     * Data returns : message
     */

    public function DeviceList(Request $request,RequestValidationService $rq){

        $val = $rq->deviceList($request);
        if(!is_null($val)){

            return $val;
        }
        return new DeviceList();
    }

    //    ============ Registering admin key by user  ============

    /*
     * Data Needed : token,key
     * Data returns : message
     */
    public function sharing(Request $request,RequestValidationService $rq){


        $val = $rq->sharing($request);
        if(!is_null($val)){

            return $val;
        }

        return new Sharing();
    }

    //    ============ Sending device liquid usage for app chart ============

    /*
     * Data Needed : filter_name(week,month,year),token,unique_id
     * Data returns : data
     *
     */


    public function liquidChart(Request $request,RequestValidationService $rq){

//        return [Auth::guard('user')->id(),Auth::guard('admin')->id()];
        $val = $rq->chart($request);
        if(!is_null($val)){

            return $val;
        }

        return new Chart();
    }
    //    ============ Getting Transactions List  ============

    /*
     * Data Needed : token
     * Data returns : data
     */
    public function transList(){

    }

    //    ============ Device Usage Report (send user devices report including usages)  ============

    /*
     * Data Needed : token
     * Data returns : data
     */
    public function deviceReport(Request $request,RequestValidationService $rq){

//        $val = $rq->report($request);
//        if(!is_null($val)){
//
//            return $val;
//        }
        return new deviceReport();
    }

}
