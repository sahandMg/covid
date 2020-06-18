<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\DeviceLog::class, 100)->create();

    }
}
