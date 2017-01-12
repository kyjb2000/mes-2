<?php

namespace App\Http\Controllers\Mes;

use App\Http\Model\ProductList;
use App\Http\Model\WorkOrder;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

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

        $count = WorkOrder::where('work_order_rest_qty','!=','work_order_wip_qty')->get();
        $data = [
            'title' => $title,
            'work_order' => $count,
        ];
        return view('mes/create_run_card',$data);
    }

    //create lot
    public function create_lot($work_order)
    {
        $title = 'Create Lot';
        $work_res = WorkOrder::where('work_order_serial',$work_order)->first();
        if($input = Input::all())
        {
//            dd($input);
            $lot_index = DB::table(SYS_INI)->where("sys_ini_type",3)->first();
            $all_num = $input["all_num"];
            if(empty($input["per_num"]))
            {
                $per_num = 18;
            }else
            {
                $per_num = $input["per_num"];
            }
            if(empty($input["bill_date"]))
            {
                $bill_date = date("Y-m-d");
            }else
            {
                $bill_date = $input["bill_date"];
            }
            $bill_num = ceil($all_num / $per_num);
            $lot_no = DB::table(LOT_NO)->where("lot_no_serial",">=",$lot_index->sys_ini_info)->take($bill_num)->get();
            switch($work_res->work_order_type)
            {
                case 0:
                    $lot_pre = "P";
                    break;
                case 1:
                    $lot_pre = "Q";
                    break;
                case 2:
                    $lot_pre = "R";
                    break;
                default:
                    $lot_pre = "P";
            }

            foreach($lot_no as $lot_no_arr)
            {
                $lot_no_arr->lot_no_id = $lot_pre.$lot_no_arr->lot_no_id;
            }
//            dd($lot_no);
//            if($all_num % $per_num == 0)
//            {
//                $last_lot_num = $per_num;
//            }else
//            {
//                $last_lot_num = $all_num % $per_num;
//            }
//            dd($lot_no);
            $data = [
                'title' => $title,
                'work_order' => $work_res,
                'all_num' => $all_num,
                'per_num' => $per_num,
                'priority' => $input["priority"],
                'bill_date' => $bill_date,
                'lot_no' =>$lot_no
            ];
            return view('mes/create_lot_list',$data);

        }else
        {

//        dd($work_res);
            $data = [
                'title' => $title,
                'work_order' => $work_res,
            ];
            return view('mes/create_lot',$data);
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

}
