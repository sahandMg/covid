<?php

namespace App\Http\Controllers\AuthResponsables;


use App\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Hash;

class AdminSignUp implements Responsable
{

    private $repo;
    private $formatter;

    public function __construct($repo,$formatter)
    {
        $this->repo = $repo;
        $this->formatter = $formatter;
    }

    public function toResponse($request)
    {

        $admin = new User();
        $admin->name = $request->name;
        $admin->password = Hash::make($request->password);
        $admin->email = $request->email;
        $admin->key = strtoupper(str_shuffle('ZINOVAA').uniqid());
        $admin->role_id = $this->repo->findRoleId('admin');
        $admin->save();
// !!!!!!!!!! DO NOT CHANGE THE RETURN FORMAT !!!!!!!!!!!!

        return $resp = $this->formatter->create($status = 200, $type = 'success',$message = ['scc'=>'ادمین با موفقیت ثبت شد']);
    }
}