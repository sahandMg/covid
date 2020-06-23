<?php

namespace App\Console\Commands;

use App\Device;
use App\Report;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:report';

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
     *
     * @return mixed
     */
    public function handle()
    {
        $queries =  DB::table('devices')->join('device_logs', 'devices.id', '=', 'device_logs.device_id')->select('device_id','push','devices.admin_id','device_logs.created_at')->get();
        $deviceArr = [];
        $deviceOwner = [];

        foreach ($queries as $query){
            $queryDate = Carbon::parse($query->created_at);
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();
            if($queryDate->greaterThanOrEqualTo($yesterday) && $queryDate->lessThan($today)){

                if(!in_array($query->device_id,array_keys($deviceArr))){
                    $deviceArr[$query->device_id] = $query->push;
                }else{
                    $deviceArr[$query->device_id] = $deviceArr[$query->device_id] + $query->push;
                }
            }
            if(!in_array($query->device_id,array_keys($deviceOwner))){

                $deviceOwner[$query->device_id] = $query->admin_id;
            }
        }
        foreach($deviceArr as $key=>$item){

            $report = new Report();
            $report->total_pushed = $item;
            $report->device_id = $key;
            $report->admin_id = $deviceOwner[$key];
            $report->save();
        }

    }
}
