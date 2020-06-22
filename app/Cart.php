<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['completed'];

    public function transaction(){

        return $this->hasOne(Transaction::class);
    }
    public function user(){

        return $this->belongsTo(User::class);
    }

    public function admin(){

        return $this->belongsTo(Admin::class);
    }
}
