<?php

namespace App\Http\Controllers\Mes;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ProductMGTController extends CommonController
{
    //PROD_MGT为生产管理（production management）的意思，主要是显示某站区进站lot表，当站lot表及去往下站的lot表
    public function prod_mgt($make_area)
    {
        $title = "工作流";
        if($input = Input::except("_token"))
        {
//            dd($input);
            $post_flag = 1;
            $run_trace_res = DB::table(RUN_TRACE)->where("run_trace_lotno","like",$input["lot_no"]."%")
                ->where(function ($query) use ($make_area){
                    $query->where("run_trace_current_makeid",$make_area)->where("run_trace_next_makeid",$make_area);
                })
                ->get();
            $data = [
                "run_trace_res" => $run_trace_res,
                "title" => $title,
                "post_flag" => $post_flag,
                "make_area" => $make_area
            ];
            return view("mes/prod_mgt",$data);
        }else
        {
            $post_flag = 0;
            $run_trace_res = DB::table(RUN_TRACE)->where("run_trace_current_makeid",$make_area)
                ->orWhere("run_trace_next_makeid",$make_area)->get();
            $data = [
                "run_trace_res" => $run_trace_res,
                "title" => $title,
                "post_flag" => $post_flag,
                "make_area" => $make_area
            ];
            return view("mes/prod_mgt",$data);
        }
    }
}
