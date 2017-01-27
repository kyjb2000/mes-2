<?php

namespace App\Http\Controllers\Mes;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class IndexController extends CommonController
{
    //
    public function index()
    {
//        dd(session('user'));
        $user_info = session('user');
        $msg_res = DB::table(SHIFT_RECORD)->where("shift_record_to_user",$user_info["user_serial"])->where("shift_record_status",0)->orderBy('shift_record_create_time', 'desc');
        $msg_last = $msg_res->first();
        $time_diff = "";
        if(!empty($msg_last))
        {
            $time_diff = timeDiff($msg_last->shift_record_create_time,date("Y-m-d H:i:s"));
        }

        $make_area_res = DB::table(MAKE_AREA)->where("make_area_serial",">",10)->get();
        $msg_num = $msg_res->count();
        $data = [
            "user" => $user_info['user_name'],
            'identity'=>$user_info['user_department'],
            'msg_num' => $msg_num,
            "time_diff" => $time_diff,
            "make_area_res" => $make_area_res
        ];
        return view('mes/index',$data);

    }

    public function contacts()
    {
        return view("mes/contacts");
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
