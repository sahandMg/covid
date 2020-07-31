<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{

    protected $fillable = ['d_name','ssid','w_ssid','city','region','user_id','unique_id','power_off'];

    public function user(){

        return $this->belongsTo(User::class);
    }

    public function deviceLogs(){

        return $this->hasMany(DeviceLog::class);
    }
}
