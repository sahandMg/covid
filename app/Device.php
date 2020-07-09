<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $guarded = ['unique_id'];
    protected $fillable = ['d_name','ssid','w_ssid','city','region'];

    public function user(){

        return $this->belongsTo(User::class);
    }

    public function deviceLogs(){

        return $this->hasMany(DeviceLog::class);
    }
}
