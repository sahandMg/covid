<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\App\DeviceLog::class, function (Faker $faker) {
    return [
        'power' => rand(1,100),
        'capacity' => rand(1,100),
        'push' => rand(1,100),
        'admin_id'=>3,
        'created_at'=> Carbon::create(rand(2015,2020), rand(1,12), rand(1,29))
    ];
});

$factory->define(\App\Device::class, function (Faker $faker) {
    return [
        'unique_id' => uniqid(),
        'name' => $faker->name(),
        'ssid' => rand(1,100),
        'w_ssid'=> rand(1,100),
        'region'=>$faker->state,
        'city'=>$faker->city,
        'admin_id'=>3,
        'created_at'=> Carbon::create(rand(2015,2020), rand(1,12), rand(1,29))
    ];
});