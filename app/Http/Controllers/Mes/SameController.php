<?php

namespace App\Http\Controllers\Mes;

use App\Http\Model\ProductList;
use App\Http\Model\WorkOrder;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Imagine\Exception\Exception;

class SameController extends CommonController
{
    //
    public function home()
    {
        return view('mes/layouts',['title' => '首页']);
    }

    //工单查询
    public function search_work_order()
    {
        $title = '工单查询';
        if(!empty($input = Input::all()) && !empty($input['start']) && !empty($input["end"]))
        {

            $start_day = $input['start'];
            $end_day = $input["end"];

        }else
        {
            $start_day = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-7,date("Y")));;
            $end_day = date("Y-m-d");
        }
        $count = WorkOrder::whereBetween('work_order_start_day',[$start_day,$end_day])->get();
        $data = [
            'title' => $title,
            'start_day' => $start_day,
            'end_day' => $end_day,
            'work_order' => $count,
        ];
        return view('mes/search_work_order',$data);
    }

    //新建工单
    public function new_work_order()
    {
        $title = '新建工单';
        $msg = "";
        $err = "";
        $product = ProductList::where('product_list_process','!=',0)->get();
        if($input = Input::all()) {
//            dd($input);
//            $res = ProductList::where('product_list_serial',$input['product']);
            $res_product = DB::select('select * from `product_list` WHERE product_list_serial=?',[$input['product']]);
            $res_process = DB::select('select * from `main_config` WHERE main_config_serial=?',[$res_product[0]->product_list_process]);
//            return $res_product[0]->product_list_process;
            $start_day = empty($input['start_day'])?date("Y-m-d"):$input['start_day'];
            $insert_data = [
                'work_order_name' => $input['work_name'],
                'work_order_productid' => $input['product'],
                'work_order_product_name' => $res_product[0]->product_list_name,
                'work_order_proid' => $res_product[0]->product_list_process,
                'work_order_pro_name' => $res_process[0]->main_config_name,
                'work_order_type' => $input['lot_type'],
                'work_order_rest_qty' => $input['qty'],
                'work_order_owner' => $input['owner'],
                'work_order_start_day' => $start_day,
                'work_order_due_day' => $input['due_day'],
                'work_order_customer' => $input['customer']
            ];

            DB::beginTransaction();
            try{
                $res =  DB::table('work_order')->insert($insert_data);
                DB::commit();
                $msg = '新增工单成功！';
            }catch (\Exception $e)
            {
                DB::rollBack();
                $err = '新增工单失败！';
            }
           
        }
        $data = [
            'title' => $title,
            'product' => $product,
            'msg' => $msg,
            'err' => $err
        ];

        return view('mes/new_work_order',$data);
    }
    //开run_card的入口表
    public function create_run_card()
    {
        $title = 'Run Card开立';
        $msg = "";
        $err = "";
        $count = WorkOrder::where('work_order_rest_qty','!=','work_order_wip_qty')->get();
        $data = [
            'title' => $title,
            'work_order' => $count,
            'msg' => $msg,
            'err' => $err
        ];
        return view('mes/create_run_card',$data);
    }

    //create lot
    public function create_lot($work_order)
    {
        $title = 'Create Lot';
        $work_res = WorkOrder::where('work_order_serial',$work_order)->first();
//        dd($work_res);
        $data = [
            'title' => $title,
            'work_order' => $work_res,
        ];
        return view('mes/create_lot',$data);
    }

    public function create_lot_list($work_order)
    {
        $title = "Create lot";
        $input = Input::except("_token");
        if($input)
        {
            $all_num = $input["all_num"];
            $per_num = empty($input["per_num"])?18:$input["per_num"];
            $res_work_order = DB::table(WORK_ORDER)->where('work_order_serial',$work_order)->first();
            $res_sys_ini = DB::table(SYS_INI)->where('sys_ini_type',3)->first();
            $lot_index = $res_sys_ini->sys_ini_info;
            $num = ceil($all_num/$per_num);
            $res_lot_no = DB::table(LOT_NO)->where('lot_no_serial',">=",$lot_index)->limit($num)->get();
            $data = [
                'title' => $title,
                'work_order' => $res_work_order,
                'lot_no' => $res_lot_no,
                'all_num' => $all_num,
                'per_num' => $per_num,
                'priority' => $input["priority"],
                'date' => date("Y-m-d")
            ];
            return view('mes/create_lot_list',$data);
        }
//
    }

    /*
    将开出新的工单信息写入lot_info表以及开出run_trace，还要更新sys_ini表中type=3表示lot序列号的索引值.注意防呆。
    * 1.lot的序列号是从lot_no表中读取的，lot_no表是存放lot序列号的库，该库使用随机生成算法将生成的lot存入lot_no表中，并保证没有重复的lot
    * 2.为了防止run_trace表中会出现相同的lot序列号，除上述方法还要在读取了lot_no表后再与run_trace中的lot比对看是否已存在，如果存在则lot_no表的索引加1，直到不存在为止。
    * 3.指向lot_no的表的索引是存在于表sys_ini中，其中sys_ini_type=3表示是lot_no表的索引，sys_ini_name=lot_no_index,sys_ini_info的值就是该索引值，生产lot的时候就是先读取这个值再指到lot_no表的
    * 4.run_trace表中的run_trace_lot_fullname是用在lot上线时生成wafer_index表的wafer_index_lot_fullname的信息时使用的。
    */
    public function new_lots($work_order)
    {
        $input = Input::except("_token");
        $back_str = "返回Run_card开立页面";
//        dd($input);
        if($input)
        {
            $max_num = $input["max_num"];
            $all_num = $input["all_num"];
            $per_num = $input["per_num"];
            $lot_num = $per_num;
            $priority = $input["priority"];
            $time = date("Y-m-d H:i:s");

            DB::beginTransaction();
            try{
                $work_order_res = DB::select("SELECT * FROM ".WORK_ORDER." WHERE work_order_serial=$work_order FOR UPDATE");
                $lot_type = $work_order_res[0]->work_order_type;
                $process_id = $work_order_res[0]->work_order_proid;
                $main_config_res = DB::table(MAIN_CONFIG)->where("main_config_serial",$process_id)->first();
                $main_id = $main_config_res->main_config_main_sid;
                $main_flow_res = DB::table(MAIN_FLOW)->where("main_flow_serial",$main_id)->first();
                $main_name = $main_flow_res->main_flow_list_name;
                $next_main_id = $main_flow_res->main_flow_next_sid;
                $class_id = $main_flow_res->main_flow_class_sid;
                $class_flow_res = DB::table(CLASS_FLOW)->where("class_flow_serial",$class_id)->first();
                $class_name = $class_flow_res->class_flow_list_name;
                $next_class_id = $class_flow_res->class_flow_next_sid;
                $flow_list_id = $class_flow_res->class_flow_list_sid;
                $current_make_id = $class_flow_res->class_flow_list_makeid;
                $next_class_res = DB::table(CLASS_FLOW)->where("class_flow_serial",$next_class_id)->first();
                $next_make_id = $next_class_res->class_flow_list_makeid;
                $next_make_name = $next_class_res->class_flow_make_name;
                switch($lot_type)
                {
                    case 0:
                        $first_lot_name = "P";
                        break;
                    case 1:
                        $first_lot_name = "Q";
                        break;
                    case 2:
                        $first_lot_name = "R";
                        break;
                    default:
                        $first_lot_name = "P";
                }
                //如果最大的数量有发生变化则给出错误提示
                if($max_num != ($work_order_res[0]->work_order_rest_qty-$work_order_res[0]->work_order_wip_qty))
                {
                    DB::rollBack();
                    $rtn_msg = "<br><br/><font color=#F00101><span name='tip' style='color: red;'>生成run card 失败，剩余数量已更改！</span></font>".'<br><br><a href="">返回Run-card开立页面</a>';
                    return $rtn_msg ;
                }
                $res_sys_ini = DB::table("sys_ini")->where('sys_ini_type',3)->first();
                $lot_index = $res_sys_ini->sys_ini_info;
                $res_lot_no = DB::table(LOT_NO)->where('lot_no_serial',">=",$lot_index)->limit(ceil($all_num/$per_num))->get();
                //需要生成的lot的数量为ceil($all_num/$per_num)
                for($i = 0; $i < ceil($all_num/$per_num); $i++)
                {
                    if($i != (ceil($all_num/$per_num)-1) || ($all_num % $per_num) == 0)
                    {
                        $lot_num = $per_num;
                    }else
                    {
                        $lot_num = $all_num % $per_num;
                    }
                    $ins_flow_record_data = [
                        "flow_record_lotno" => $first_lot_name.$res_lot_no[$i]->lot_no_id,
                        "flow_record_origin_lot" => date("Ymd").$first_lot_name.$res_lot_no[$i]->lot_no_id,
                        "flow_record_num" => $lot_num,
                        "flow_record_start_time" => $time,
                        "flow_record_process" => $work_order_res[0]->work_order_pro_name,
                        "flow_record_mainid" => $main_id,
                        "flow_record_main" => $main_name,
                        "flow_record_classid" => $class_id,
                        "flow_record_class" => $class_name,
                        "flow_record_op_name" => session("user")->user_name,
                        "flow_record_wafer" => pow(2,$lot_num)-1
                    ];

                    $ins_flow_record = DB::table(FLOW_RECORD)->insertGetId($ins_flow_record_data);
                    //生成lot_info信息
                    $ins_lot_info_data = [
                        "lot_info_lotno" => $first_lot_name.$res_lot_no[$i]->lot_no_id,
                        "lot_info_origin_lot" => date("Ymd").$first_lot_name.$res_lot_no[$i]->lot_no_id,
                        "lot_info_type" => $lot_type,
                        "lot_info_priority" => $priority,
                        "lot_info_pro" => $work_order_res[0]->work_order_proid,
                        "lot_info_pro_name" => $work_order_res[0]->work_order_pro_name,
                        "lot_info_work_id" => $work_order_res[0]->work_order_serial,
                        "lot_info_date" => $time,
                        "lot_info_wafer" => pow(2,$lot_num)-1,
                        "lot_info_first_record" => $ins_flow_record
                    ];
                    $ins_lot_info = DB::table(LOT_INFO)->insertGetId($ins_lot_info_data);
//                    echo $ins_flow_record."<br/>";
                    $ins_run_trace_data = [
                        "run_trace_lotno" => $first_lot_name.$res_lot_no[$i]->lot_no_id,
                        "run_trace_origin_lot" => date("Ymd").$first_lot_name.$res_lot_no[$i]->lot_no_id,
                        "run_trace_process_name" => $work_order_res[0]->work_order_pro_name,
                        "run_trace_process_code" => $work_order_res[0]->work_order_proid,
                        "run_trace_level" => $lot_type,
                        "run_trace_priority" => $priority,
                        "run_trace_current_mainsid" => $main_id,
                        "run_trace_next_mainsid" => $next_main_id,
                        "run_trace_current_classid" => $class_id,
                        "run_trace_next_classid" => $next_class_id,
                        "run_trace_classid" => $flow_list_id,
                        "run_trace_classname" => $class_name,
                        "run_trace_current_makeid" => $current_make_id,
                        "run_trace_next_makeid" => $next_make_id,
                        "run_trace_next_makename" => $next_make_name,
                        "run_trace_num" => $lot_num,
                        "run_trace_wafer" => pow(2,$lot_num)-1,
                        "run_trace_last_record" => $ins_flow_record,
                        "run_trace_lotinfo_sid" => $ins_lot_info
                    ];
                    $ins_run_trace = DB::table(RUN_TRACE)->insert($ins_run_trace_data);
                }
                $sql_for_upd_sys_ini = DB::select("SELECT * FROM ".SYS_INI." WHERE sys_ini_type='3' FOR UPDATE");
                $upd_sys_ini = DB::update("UPDATE ".SYS_INI." SET sys_ini_info=sys_ini_info+'".ceil($all_num/$per_num)."' WHERE sys_ini_type='3'");
                $upd_work_order = DB::update("UPDATE work_order SET work_order_wip_qty=work_order_wip_qty+'".$all_num."' WHERE work_order_serial=$work_order");
//                test();
                DB::commit();
                $suc_msg = "生成run card 成功！";
                return show_exe_res($suc_msg,url("create_run_card"),$back_str,1);
            }catch (\Exception $e)
            {
//                var_dump($e);
                DB::rollBack();
                $err_msg = "生成run card 失败！";
                return show_exe_res($err_msg,url("create_run_card"),$back_str,0);
            }
        }else
        {

        }
    }

    //run_card拆批的入口表
    public function dismantle_run_card()
    {
        $title = 'Run Card拆批';
        //获取状态为11（等待）的所有lot信息
        $count = DB::table(RUN_TRACE)->select('run_trace_lotno','run_trace_num','run_trace_parent_lot','run_trace_current_classid','run_trace_classname',
            'run_trace_state_type','run_trace_process_name','run_trace_serial','run_trace_current_makeid','run_trace_level','class_flow_stage_name',
            'class_flow_stage_sort','class_flow_make_name')
            ->where('run_trace_state_type',11)
            ->join('class_flow', 'run_trace.run_trace_current_classid', '=', 'class_flow.class_flow_serial')
            ->get();
        $data = [
            'title' => $title,
            'run_trace' => $count,
        ];
        return view('mes/dismantle_run_card',$data);

    }

    public function dismantle_lot($lot_id)
    {
        $title = "Run Card拆批";
        if($input = Input::except("_token"))
        {
//            dd($input);
            $time = date("Y-m-d H:i:s");
            //获取制程代码的名称
            $process_res = DB::table(MAIN_CONFIG)->where("main_config_serial",$input["process_id"])->first();
            $process_name = $process_res->main_config_name;
            //获取子制程名称，其所在站区id，其list id,下一子制程的id
            $class_flow_res = DB::table(CLASS_FLOW)->where("class_flow_serial",$input["class_id"])->first();
            $class_name = $class_flow_res->class_flow_list_name;
            $next_classid = $class_flow_res->class_flow_next_sid;
            $class_list_sid = $class_flow_res->class_flow_list_sid;
            $current_makeid = $class_flow_res->class_flow_list_makeid;
            //获取主制程名称及下一主制程id
            $main_config_res = DB::table(MAIN_FLOW)->where("main_flow_serial",$input["main_id"])->first();
            $main_name = $main_config_res->main_flow_list_name;
            $next_mainid = $main_config_res->main_flow_next_sid;
            if($next_classid == 0)
            {
                $next_classid = $main_config_res->main_flow_class_sid;
            }
            //获取下一子制程的make_id和make_name
            $class_flow_res = DB::table(CLASS_FLOW)->where("class_flow_serial",$next_classid)->first();
            $next_makeid = $class_flow_res->class_flow_list_makeid;
            $next_makename = $class_flow_res->class_flow_make_name;
            DB::beginTransaction();
            $run_trace_res = DB::select("SELECT * FROM ".RUN_TRACE." WHERE run_trace_serial='".$lot_id."' FOR UPDATE");
            $lot_type = $run_trace_res[0]->run_trace_level;
            $current_num = $run_trace_res[0]->run_trace_num;
            if ($current_num != $input["parent_num"])
            {
                DB::rollBack();
                $alarm_msg = "已被拆批！";
                return show_exe_res($alarm_msg,url("dismantle_run_card"),"",2);
            }
            switch ($lot_type)
            {
                case 0:
                    $lot_first = "P";
                    break;
                case 1:
                    $lot_first = "Q";
                    break;
                case 2:
                    $lot_first = "R";
                    break;
                default:
                    $lot_first = "P";
            }
            $parent_lot = $run_trace_res[0]->run_trace_lotno;
            $origin_lot = $run_trace_res[0]->run_trace_origin_lot;
            $lotinfo_id = $run_trace_res[0]->run_trace_lotinfo_sid;
            $wafer = $run_trace_res[0]->run_trace_wafer;
            $wafer = (int)$wafer^$input["dis_wafer"];
            try{
                $upd_run_trace = DB::update("UPDATE ".RUN_TRACE." SET run_trace_num=run_trace_num-'".$input['dis_num']."',run_trace_wafer='".$wafer."' WHERE run_trace_serial='".$lot_id."'");
                $sys_ini_for_upd = DB::select("SELECT * FROM ".SYS_INI." WHERE sys_ini_type='3' FOR UPDATE");
                $lot_index = $sys_ini_for_upd[0]->sys_ini_info;
                //判断lotno是否在run_trace中存在
                do{
                    $lot_info_res = DB::table(LOT_NO)->where("lot_no_serial",$lot_index)->first();
                    $lot_no = $lot_first.$lot_info_res->lot_no_id;
                    $run_trace_res = DB::table(RUN_TRACE)->where("run_trace_lotno",$lot_no)->first();
                    $lot_index++;
                }while(!empty($run_trace_res));
                $ins_flow_record_data = [
                    "flow_record_lotno" => $lot_no,
                    "flow_record_parent_lot" => $parent_lot,
                    "flow_record_origin_lot" => $origin_lot,
                    "flow_record_wafer" => $input["dis_wafer"],
                    "flow_record_num" => $input["dis_num"],
                    "flow_record_start_time" => $time,
                    "flow_record_op_name" => session("user")->user_name,
                    "flow_record_process" => $process_name,
                    "flow_record_mainid" => $input["main_id"],
                    "flow_record_main" => $main_name,
                    "flow_record_classid" => $input["class_id"],
                    "flow_record_class" => $class_name
                ];
                $ins_flow_record = DB::table(FLOW_RECORD)->insertGetId($ins_flow_record_data);
                $lot_info_for_upd = DB::select("SELECT * FROM ".LOT_INFO." WHERE lot_info_serial='".$lotinfo_id."' FOR UPDATE");
                $priority = $lot_info_for_upd[0]->lot_info_priority;
                $work_id = $lot_info_for_upd[0]->lot_info_work_id;
                $upd_lot_info = DB::update("UPDATE ".LOT_INFO." SET lot_info_wafer='".$wafer."' WHERE lot_info_serial='".$lotinfo_id."'");
                $ins_lot_info_data = [
                    "lot_info_lotno" => $lot_no,
                    "lot_info_parent_lot" => $parent_lot,
                    "lot_info_origin_lot" => $origin_lot,
                    "lot_info_first_record" => $ins_flow_record,
                    "lot_info_wafer" => $input["dis_wafer"],
                    "lot_info_type" => $lot_type,
                    "lot_info_priority" => $priority,
                    "lot_info_pro" => $input["process_id"],
                    "lot_info_pro_name" => $process_name,
                    "lot_info_date" => $time,
                    "lot_info_work_id" => $work_id
                ];
                $ins_lot_info = DB::table(LOT_INFO)->insertGetId($ins_lot_info_data);
                $ins_run_trace_data = [
                    "run_trace_lotno" => $lot_no,
                    "run_trace_parent_lot" => $parent_lot,
                    "run_trace_origin_lot" => $origin_lot,
                    "run_trace_lotinfo_sid" => $ins_lot_info,
                    "run_trace_num" => $input["dis_num"],
                    "run_trace_wafer" => $input["dis_wafer"],
                    "run_trace_last_record" => $ins_flow_record,
                    "run_trace_process_name" => $process_name,
                    "run_trace_process_code" => $input["process_id"],
                    "run_trace_level" => $lot_type,
                    "run_trace_priority" => $priority,
                    "run_trace_current_mainsid" => $input["main_id"],
                    "run_trace_next_mainsid" => $next_mainid,
                    "run_trace_current_classid" => $input["class_id"],
                    "run_trace_next_classid" => $next_classid,
                    "run_trace_classid" => $class_list_sid,
                    "run_trace_classname" => $class_name,
                    "run_trace_current_makeid" => $current_makeid,
                    "run_trace_next_makeid" => $next_makeid,
                    "run_trace_next_makename" => $next_makename
                ];
                $ins_run_trace = DB::table(RUN_TRACE)->insert($ins_run_trace_data);
                $upd_sys_ini = DB::update("UPDATE ".SYS_INI." SET sys_ini_info='".$lot_index."' WHERE sys_ini_type='3'");
                DB::commit();
                $suc_msg = "拆批成功！";
                return show_exe_res($suc_msg,url("dismantle_run_card"),"",1);
            }catch (\Exception $e)
            {
                DB::rollBack();
                $err_msg = "拆批失败！";
                return show_exe_res($err_msg,url("dismantle_run_card"),"",0);
            }


        }else
        {
            //没有post任何值时，现实该lot的wafer
            $run_trace_res = DB::table(RUN_TRACE)->where("run_trace_serial",$lot_id)->first();
            $main_config_res = DB::table(MAIN_CONFIG)->where("main_config_serial",$run_trace_res->run_trace_process_code)->get();
            $lot_no = $run_trace_res->run_trace_lotno;
            $lot_num = $run_trace_res->run_trace_num;
            $wafer_pos = $run_trace_res->run_trace_wafer;
            $lot_type = $run_trace_res->run_trace_level;
            $priority = $run_trace_res->run_trace_priority;
            $data = [
                'title' => $title,
                'lot_no' => $lot_no,
                'lot_num' => $lot_num,
                'wafer_pos' => $wafer_pos,
                'lot_type' => $lot_type,
                'priority' => $priority,
                'lot_id' => $lot_id,
                "main_config" => $main_config_res
            ];
            return view('mes/dismantle_lot',$data);
        }
    }

    //run_card下线页面
    public function run_card_on_line()
    {
        $title = 'Run Card下线';

        $search = DB::table(RUN_TRACE)->select('run_trace_lotno','run_trace_num','run_trace_process_name','run_trace_level','run_trace_priority')
            ->where('run_trace_state_type',0)->get();
        $data = [
            'title' => $title,
            'run_trace' => $search,
        ];
        return view('mes/run_card_on_line',$data);
    }

    //run_card入库页面
    public function run_card_close()
    {
        $title = 'Run Card入库';

        $search = DB::table(RUN_TRACE)->select('run_trace_lotno','run_trace_parent_lot','run_trace_num','run_trace_process_name','run_trace_level','run_trace_state_type',
            'run_trace_current_makeid','make_area_name')->whereIn('run_trace_state_type',[14,15])
            ->join(MAKE_AREA,'run_trace.run_trace_current_makeid','=','make_area.make_area_serial')
            ->get();
        $data = [
            'title' => $title,
            'run_trace' => $search,
        ];
        return view('mes/run_card_close',$data);
    }

    //站区WIP列表
    public function station_wip()
    {
        $title = '站区WIP表';
        $run_trace = collect();
        $lot = collect();
        $c = 0;
//        $search = DB::table(MAKE_AREA)->select('make_area_serial','make_area_name','run_trace_lotno','run_trace_num','run_trace_state_type')
//            ->where('make_area_serial','>',10)->where('run_trace_state_type','>',10)
//            ->join(RUN_TRACE,'make_area.make_area_serial','=','run_trace.run_trace_current_makeid')
//            ->orderBy('make_area_serial', 'asc')
//            ->get();
        $search = DB::table(MAKE_AREA)->select('make_area_serial','make_area_name')
            ->where('make_area_serial','>',10)
            ->get();
        foreach ($search as $search_make_area)
        {
           $sql_run_trace = DB::table(RUN_TRACE)->select('run_trace_lotno','run_trace_num','run_trace_state_type','run_trace_current_makeid',
               'run_trace_serial')->where('run_trace_current_makeid',$search_make_area->make_area_serial)->where('run_trace_state_type','>',10)->get();
            if(!empty($sql_run_trace))
            {
                $run_trace->put($search_make_area->make_area_serial,$sql_run_trace);
            }
//            $run_trace->put();
        }
//        dd($run_trace);
//        var_dump($run_trace[2]);
        $data = [
            'title' => $title,
            'make_area' => $search,
            'run_trace' => $run_trace
        ];
        return view('mes/station_wip',$data);
    }

    //通过js的ajax返回制程代码的主制程或放回某主制程的所有子制程
    public function ajax_sel_flow()
    {
//        $input = Input::all();
        $data = "";
        if(!empty($_POST["process_id"]))
        {
          $process_res = DB::table(MAIN_CONFIG)->where("main_config_serial",$_POST["process_id"])->first();
          $main_id = $process_res->main_config_main_sid;
          while($main_id)
          {
              $main_flow_res = DB::table(MAIN_FLOW)->where("main_flow_serial",$main_id)->first();
              $main_name = $main_flow_res->main_flow_list_name;
              if(empty($data))
              {
                  $data = '<option></option><option value="'.$main_id.'">'.$main_name.'</option>';
              }else
              {
                  $data .= '<option value="'.$main_id.'">'.$main_name.'</option>';
              }
              $main_id = $main_flow_res->main_flow_next_sid;
          }
        }

        if(!empty($_POST["main_id"]))
        {
            $main_flow_res = DB::table(MAIN_FLOW)->where("main_flow_serial",$_POST["main_id"])->first();
            $class_id = $main_flow_res->main_flow_class_sid;
            while($class_id)
            {
                $class_flow_res = DB::table(CLASS_FLOW)->where("class_flow_serial",$class_id)->first();
                $class_name = $class_flow_res->class_flow_list_name;
                if(empty($data))
                {
                    $data = '<option></option><option value="'.$class_id.'">'.$class_name.'</option>';
                }else
                {
                    $data .= '<option value="'.$class_id.'">'.$class_name.'</option>';
                }
                $class_id = $class_flow_res->class_flow_next_sid;
            }
        }
        echo $data;
//        var_dump($_POST);
    }

    public function test()
    {
        return get_qtime("37,2017-01-06 16:12:04;39,2017-01-06 16:52:04");
    }
}
