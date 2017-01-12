<?php

namespace App\Http\Controllers\Mes;

use App\Http\Model\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

require_once 'resources/org/code/Code.class.php';

class LoginController extends CommonController
{
    //
    public function login()
    {
        if($input = Input::all())
        {
            $code = new \Code();
            $_code = $code->get();
            if(strtoupper($input['code']) != $_code){
                return back()->with('msg','验证码错误');
            }
            $user_name = $input['user_name'];
            $user = User::where('user_id',$user_name)->get()->first();

            if(empty($user) || $user->user_password != $input['pwd'])
            {
                return back()->with('msg','用户或密码错误');
            }
            session(['user'=>$user]);
            return Redirect::to('index');
        }else{
//            $user = User::where('user_id','sysadmin')->get();

            return view('mes/login');
        }

    }

    public function code()
    {
        $code = new \Code();
        echo $code->make();
    }
    public function logout()
    {
        session(['user'=>null]);
        return redirect('login');
    }
    
}
