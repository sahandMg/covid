<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $guarded = ['unique_id'];

    public function admin(){

        return $this->belongsTo(Admin::class);
    }
}
