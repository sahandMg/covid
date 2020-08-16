<?php

namespace App\Console\Commands;

use App\Device;
use App\DeviceLog;
use App\Report;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get a  Report from all Devices';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * Will be executed at 00:05 AM
     * @return mixed
     */
    public function handle()
    {
        $queries =  DB::table('devices')->join('device_logs', 'devices.id', '=', 'device_logs.device_id')
            ->where('device_logs.created_at','>',Carbon::yesterday())->where('device_logs.created_at','<',Carbon::today())
            ->select('device_id','push','devices.user_id','device_logs.created_at')->get();

        $deviceArr = [];
        $deviceOwner = [];
        try{

            foreach ($queries as $query){

                if(!in_array($query->device_id,array_keys($deviceArr))){
                    $deviceArr[$query->device_id] = $query->push;
                }else{
                    $deviceArr[$query->device_id] = $deviceArr[$query->device_id] + $query->push;
                }
                if(!in_array($query->device_id,array_keys($deviceOwner))){

                    $deviceOwner[$query->device_id] = $query->user_id;
                }
            }

            foreach($deviceArr as $key=>$item){

                $report = new Report();
                $report->total_pushed = $item;
                $report->device_id = $key;
                $report->user_id = $deviceOwner[$key];
                $report->created_at = Carbon::now()->subDay(1)->endOfDay();
                $report->updated_at = Carbon::now()->subDay(1)->endOfDay();
                $report->save();
                $deleteUs = \App\DeviceLog::where('created_at','<',Carbon::today())->orderBy('id','desc')->where('device_id',$key)->get()->skip(1);
                foreach ($deleteUs as $delelte){
                    $delelte->delete();
                }
            }
        }catch (\Exception $exception){

            Log::warning($exception->getMessage().$exception->getLine());
        }

    }
}
