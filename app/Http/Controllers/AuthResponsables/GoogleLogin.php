<?php



namespace App\Http\Controllers\AuthResponsables;

use App\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleLogin implements Responsable {

    private $repo;
    private $formatter;

    public function __construct($repo,$formatter)
    {
        $this->repo = $repo;
        $this->formatter = $formatter;
    }
    
    public function toResponse($request){

        $user = User::where('email',$request->email)->with('shared')->first();
        if(!is_null($user)){
            $token = Auth::guard('user')->login($user);
            $user->update(['token'=>$token]);
            $user->update(['fcm_token'=>$request->fcm_token]);
            $respMsg = ['name'=>$user->name,'token'=>$token,'code'=>$user->key,'phone'=>$user->phone,'address'=>$user->address,'shared'=>is_null($user->shared) ? 0:1];
            $user->role_id == $this->repo->findRoleId('user')?
                $respMsg['role'] = 'user':
                $respMsg['role'] = 'admin';

            $resp = $this->formatter->create($status = 200, $type = 'data',$message = $respMsg);

            return $resp;

        }

        else{

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->key = strtoupper(str_shuffle('ZINOVAA').Str::random(7));
            $user->save();
            $token = Auth::guard('user')->login($user);
            $user->update(['token'=>$token]);
            $resp = $this->formatter->create($status = 200, $type = 'data',$message =
                [
                    'name'=>$user->name,
                    'token'=>$token,
                    'role'=>'user',
                    'code'=>$user->key,
                    'phone'=>$user->phone,
                    'address'=>$user->address,
                    'shared'=>0
                ]);
            return $resp;
        }
    }
}

?>