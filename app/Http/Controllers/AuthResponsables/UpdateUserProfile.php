<?php



namespace App\Http\Controllers\AuthResponsables;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdateUserProfile implements Responsable {

    private $repo;
    private $formatter;

    public function __construct($formatter)
    {
        $this->formatter = $formatter;
    }
    
    public function toResponse($request){

        try{

            $user = Auth::guard('user')->user();

            $user->update($request->except('password'));

            if($request->has('old_password') && $request->has('password')){

                if(Hash::check($request->old_password,$user->password)){

                    $user->update(['password'=>Hash::make($request->password)]);
                }else{

                    return $this->formatter->create($status = 404, $type = 'error',$message = ['کلمه عبور فعلی نادرست است']);
                }
            }
            return $this->formatter->create($status = 200, $type = 'success',$message = ['اطلاعات کاربر به روز شد']);

        }catch (\Exception $exception){

            return $this->formatter->create($status = 500, $type = 'error',$message = $exception->getMessage());
        }
    }
}

?>