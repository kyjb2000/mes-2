<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 2017/1/22
 * Time: 9:06
 * Info: 公共函数文件
 */


//放回一个页面，该页面现实操作执行结果
function show_exe_res($msg,$back_url,$back_str,$type=1)
{
    if(empty($back_str))
    {
        $back_str = "返回";
    }
    if($type == 1)
    {
        $msg_type = "success";
    }elseif($type == 0)
    {
        $msg_type = "danger";
    }else
    {
        $msg_type = "warning";
    }
    $data = [
        "msg_type" => $msg_type,
        "msg" => $msg,
        "back_url" => $back_url,
        "back_str" => $back_str
    ];
    return view('mes/show_exe_res',$data);
}

//计算时间差
function timeDiff( $begin_time, $end_time )
{
    $begin_time = strtotime($begin_time);
    $end_time = strtotime($end_time);
    if ( $begin_time < $end_time ) {
        $starttime = $begin_time;
        $endtime = $end_time;
    } else {
        $starttime = $end_time;
        $endtime = $begin_time;
    }
    $timediff = $endtime - $starttime;
    $days = intval( $timediff / 86400 );
    $remain = $timediff % 86400;
    $hours = intval( $remain / 3600 );
    $remain = $remain % 3600;
    $mins = intval( $remain / 60 );
//    $secs = $remain % 60;
    $res = $days."天".$hours."小时".$mins."分钟";
    return $res;
}

function getLotStatus($status)
{
    switch($status)
    {
        case 11:
            $status_str = "Queue";
            break;
        case 12:
            $status_str = "Running";
            break;
        case 13:
            $status_str = "待点收";
            break;
        case 20: //process hold
//            $run_array['{S_BC}'] = "#EB6A05";
            $status_str = "P-Hold";
            break;
        case 21: //process hold
//            $run_array['{S_BC}'] = "#EB6A05";
            $status_str = "R-Hold";
            break;
        default:
            $status_str = "Queue";
    }//end-switch($run_state)
    return $status_str;
}

function get_qtime($qtime)
{
    $qtime_arr = explode(";",$qtime);
//    var_dump($qtime_arr);
    $str = "";
    if(!empty($qtime_arr[0]))
    {
        $before_qtime = explode(",",$qtime_arr[0]);
        $str .= "在".$before_qtime[1]."后进行".get_class_name($before_qtime[0]);
    }
    if(!empty($qtime_arr[1]))
    {
        if(!empty($qtime_arr[0]))
        {
            $str .= ",";
        }
        $after_qtime = explode(",",$qtime_arr[1]);
        $str .= "在".$after_qtime[1]."内到".get_class_name($after_qtime[0])."checkin";
    }
    return $str;
}
function get_class_name($class_id)
{
    $class_flow_res = \Illuminate\Support\Facades\DB::table(CLASS_FLOW)->where("class_flow_serial",$class_id)->first();
    return $class_flow_res->class_flow_list_name;
}

function show_machine_recipe($machine_recipe)
{
    $str = "";
    $machine = explode(";",$machine_recipe);
    $machine_res = \Illuminate\Support\Facades\DB::table(MACHINE_LIST)->where("machine_list_serial",$machine[0])->first();
    $str = $machine_res->machine_list_name.":PORT_".$machine[1];
    return $str;
}