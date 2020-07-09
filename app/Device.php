<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $guarded = ['unique_id'];

    public function user(){

        return $this->belongsTo(User::class);
    }

    public function deviceLogs(){

        return $this->hasMany(DeviceLog::class);
    }
}
