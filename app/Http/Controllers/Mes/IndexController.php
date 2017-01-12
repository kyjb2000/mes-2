<?php

namespace App\Http\Controllers\Mes;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

class IndexController extends CommonController
{
    //
    public function index()
    {
//        dd(session('user'));
        $user_info = session('user');
        return view('mes/index',['user'=>$user_info['user_name'],'identity'=>$user_info['user_department']]);
    }

    public function send_email()
    {

        $num = Mail::raw('Alarm mail test!', function($message) {
//            $message->from('json_vip@163.com', '发件人名称');
            $message->subject('Cim Alarm test!this is a test from cim@unicompound.com');
            $message->to('hqfdotcom@gmail.com');
        });
        if($num)
        {
            echo "success";
        }else
        {
            echo "send false";
        }
    }
}
