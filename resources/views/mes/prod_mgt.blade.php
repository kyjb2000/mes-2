@extends('mes.layouts')
@section('css')

@endsection
@section('content')

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>选择Lot</h5>

                        <div class="ibox-tools">
                            {{--刷新页面的按键--}}

                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a>
                                <i class="fa fa-refresh"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form class="form-horizontal m-t" method="post" action="" id="sel_lot">
                            {{csrf_field()}}
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">选择Lot：</label>
                                        <div class="col-sm-3">
                                            <input name="lot_no" class="form-control" placeholder="Lot No" type="text" id="lot_no">
                                        </div>
                                        <label class="col-sm-2 control-label">Check：</label>
                                        <div class="col-sm-3">
                                            <input name="check_no" class="form-control" placeholder="Check No" type="text" id="check">
                                        </div>

                                        <button type="submit" id="commit" class="btn btn-success" >提交</button>
                                        <button type="button" id="commit" class="btn btn-success" >取消</button>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if($post_flag == 0)
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>进站Lot表</h5>

                        <div class="ibox-tools">
                            {{--刷新页面的按键--}}

                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a>
                                <i class="fa fa-refresh"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">

                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>Lot No</th>
                                    <th>QTY</th>
                                    <th>Lot Type</th>
                                    <th>Priority</th>
                                    <th>投产状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $in_num = 1; ?>
                                @foreach ($run_trace_res as $run_trace)
                                    @if($run_trace->run_trace_next_makeid == $make_area && $run_trace->run_trace_state_type == 13)
                                    <tr>
                                        <td>{{$in_num}}</td>
                                        <td>{{$run_trace->run_trace_lotno}}</td>
                                        <td>{{$run_trace->run_trace_num}}</td>
                                        <td>
                                            @if($run_trace->run_trace_level == 0)
                                                P
                                            @elseif($run_trace->run_trace_level == 1)
                                                Q
                                            @elseif($run_trace->run_trace_level == 2)
                                                R
                                            @else
                                                P
                                            @endif
                                        </td>
                                        <td>{{$run_trace->run_trace_priority}}</td>
                                        <td>
                                            {{getLotStatus($run_trace->run_trace_state_type)}}
                                        </td>
                                        <td><a href="#">点收</a></td>
                                    </tr>
                                    <?php $in_num++; ?>
                                    @endif
                                @endforeach

                            </tbody>
                            <tfoot>
                            <tr>
                                <th>NO</th>
                                <th>Lot No</th>
                                <th>QTY</th>
                                <th>Lot Type</th>
                                <th>Priority</th>
                                <th>投产状态</th>
                                <th>操作</th>
                            </tr>

                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>
            @endif

            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>当站Lot表</h5>

                        <div class="ibox-tools">
                            {{--刷新页面的按键--}}

                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a>
                                <i class="fa fa-refresh"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">

                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                            <tr>
                                <th>NO</th>
                                <th>Lot No</th>
                                <th>QTY</th>
                                <th>Lot Type</th>
                                <th>Priority</th>
                                <th>投产状态</th>
                                <th width="200px">W/Q Time</th>
                                <th>Process Name</th>
                                <th>投产机台/Recipe</th>
                                <th>下站产区</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php $in_num = 1; ?>
                            @foreach ($run_trace_res as $run_trace)
                                @if($run_trace->run_trace_current_makeid == $make_area && $run_trace->run_trace_state_type != 13 && $run_trace->run_trace_state_type > 0
                                && $run_trace->run_trace_current_mainsid != 0)
                                    <tr>
                                        <td>{{$in_num}}</td>
                                        <td>{{$run_trace->run_trace_lotno}}</td>
                                        <td>{{$run_trace->run_trace_num}}</td>
                                        <td>
                                            @if($run_trace->run_trace_level == 0)
                                                P
                                            @elseif($run_trace->run_trace_level == 1)
                                                Q
                                            @elseif($run_trace->run_trace_level == 2)
                                                R
                                            @else
                                                P
                                            @endif
                                        </td>
                                        <td>{{$run_trace->run_trace_priority}}</td>
                                        <td>
                                            {{getLotStatus($run_trace->run_trace_state_type)}}
                                        </td>
                                        <td>
                                            @if(!empty($run_trace->run_trace_qtime) && $run_trace->run_trace_qtime != ";")
                                                {{get_qtime($run_trace->run_trace_qtime)}}
                                            @else
                                               N/A
                                            @endif
                                        </td>
                                        <td>{{$run_trace->run_trace_process_name}}</td>
                                        <td>
                                            @if(!empty($run_trace->run_trace_machine_recipe))
                                                {{show_machine_recipe($run_trace->run_trace_machine_recipe)}}
                                            @else
                                                依据机台限定Recipe
                                            @endif
                                        </td>
                                        <td>{{$run_trace->run_trace_next_makename}}</td>
                                    </tr>
                                    <?php $in_num++; ?>
                                @endif
                            @endforeach

                            </tbody>
                            <tfoot>
                            <tr>
                                <th>NO</th>
                                <th>Lot No</th>
                                <th>QTY</th>
                                <th>Lot Type</th>
                                <th>Priority</th>
                                <th>投产状态</th>
                                <th>w/q Time</th>
                                <th>Process Name</th>
                                <th>投产机台/Recipe</th>
                                <th>下站产区</th>
                            </tr>

                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>

            @if($post_flag == 0)
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>出站Lot表</h5>

                        <div class="ibox-tools">
                            {{--刷新页面的按键--}}

                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a>
                                <i class="fa fa-refresh"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">

                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                            <tr>
                                <th>NO</th>
                                <th>Lot No</th>
                                <th>QTY</th>
                                <th>Lot Type</th>
                                <th>Priority</th>
                                <th>投产状态</th>
                                <th>点收站区</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php $in_num = 1; ?>
                            @foreach ($run_trace_res as $run_trace)
                                @if($run_trace->run_trace_current_makeid == $make_area && $run_trace->run_trace_state_type == 13 && $run_trace->run_trace_current_mainsid != 0)
                                    <tr>
                                        <td>{{$in_num}}</td>
                                        <td>{{$run_trace->run_trace_lotno}}</td>
                                        <td>{{$run_trace->run_trace_num}}</td>
                                        <td>
                                            @if($run_trace->run_trace_level == 0)
                                                P
                                            @elseif($run_trace->run_trace_level == 1)
                                                Q
                                            @elseif($run_trace->run_trace_level == 2)
                                                R
                                            @else
                                                P
                                            @endif
                                        </td>
                                        <td>{{$run_trace->run_trace_priority}}</td>
                                        <td>
                                            {{getLotStatus($run_trace->run_trace_state_type)}}
                                        </td>
                                        <td>{{getLotStatus($run_trace->run_trace_next_makename)}}</td>
                                    </tr>
                                    <?php $in_num++; ?>
                                @endif
                            @endforeach

                            </tbody>
                            <tfoot>
                            <tr>
                                <th>NO</th>
                                <th>Lot No</th>
                                <th>QTY</th>
                                <th>Lot Type</th>
                                <th>Priority</th>
                                <th>投产状态</th>
                                <th>点收站区</th>
                            </tr>

                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>
            @endif
        </div>


@endsection
    <!-- 全局js -->
@section('plugins')


    <script src="{{asset('resources/js/plugins/layer/laydate/laydate.js')}}"></script>


    <script src="{{asset('resources/js/plugins/jeditable/jquery.jeditable.js')}}"></script>

    <!-- Data Tables -->
    <script src="{{asset('resources/js/plugins/dataTables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('resources/js/plugins/dataTables/dataTables.bootstrap.js')}}"></script>

    <!-- 自定义js -->
    {{--<script src="{{asset('resources/js/content.js?v=1.0.0')}}"></script>--}}


    <!-- Page-Level Scripts -->
    <script>
        $(document).ready(function () {

            $('.dataTables-example').dataTable();
            $(".fa-refresh").click(function () {
                window.location.reload();
            });
            /* Init DataTables */

        });


    </script>
@endsection
