@extends('mes.layouts')
@section('css')

@endsection
@section('content')

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Run Card入库</h5>

                        <div class="ibox-tools">
                            {{--刷新页面的按键--}}

                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a href="{{url('run_card_close')}}?<?php echo lcg_value(); ?>">
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
                                    <th>Parent Lot</th>
                                    <th>Station</th>
                                    <th>Lot Type</th>
                                    <th>QTY</th>
                                    <th>Status</th>
                                    <th>Process</th>
                                    <th>Close</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $num = 1; ?>
                                @foreach ($run_trace as $run_trace)
                                    <tr>
                                        <td>{{$num}}</td>
                                        <td>{{$run_trace->run_trace_lotno}}</td>
                                        <td>{{$run_trace->run_trace_parent_lot}}</td>
                                        <td>{{$run_trace->make_area_name}}</td>
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
                                        <td>{{$run_trace->run_trace_num}}</td>

                                        <td>
                                            @if($run_trace->run_trace_state_type == 11)
                                                Queue
                                            @elseif($run_trace->run_trace_state_type == 14)
                                                scope
                                            @elseif($run_trace->run_trace_state_type == 15)
                                                制程结束
                                            @endif
                                        </td>

                                        <td>{{$run_trace->run_trace_process_name}}</td>
                                        <td><a href="#">入库</a></td>
                                    </tr>
                                    <?php $num++; ?>
                                @endforeach

                            </tbody>
                            <tfoot>
                            <tr>
                                <th>NO</th>
                                <th>Lot No</th>
                                <th>Parent Lot</th>
                                <th>Station</th>
                                <th>Lot Type</th>
                                <th>QTY</th>
                                <th>Status</th>
                                <th>Process</th>
                                <th>Close</th>
                            </tr>

                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>
        </div>


@endsection
    <!-- 全局js -->
@section('plugins')


    <script src="{{asset('resources/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script>
        //外部js调用
//        laydate({
//            elem: '#hello', //目标元素。由于laydate.js封装了一个轻量级的选择器引擎，因此elem还允许你传入class、tag但必须按照这种方式 '#id .class'
//            event: 'focus' //响应事件。如果没有传入event，则按照默认的click
//        });
//        laydate.skin('yalan');
        //日期范围限制
        var start = {
            elem: '#start',
            format: 'YYYY-MM-DD',
//            min: laydate.now(), //设定最小日期为当前日期
            max: '2099-06-16 23:59:59', //最大日期
            istime: true,
            istoday: false,
            choose: function (datas) {
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas ;//将结束日的初始值设定为开始日
            }
        };
        var end = {
            elem: '#end',
            format: 'YYYY-MM-DD',
//            min: laydate.now(),
            max: '2099-06-16 23:59:59',
            istime: true,
            istoday: false,
            choose: function (datas) {
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };
        laydate(start);
        laydate(end);
    </script>


    <script src="{{asset('resources/js/plugins/jeditable/jquery.jeditable.js')}}"></script>

    <!-- Data Tables -->
    <script src="{{asset('resources/js/plugins/dataTables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('resources/js/plugins/dataTables/dataTables.bootstrap.js')}}"></script>

    <!-- 自定义js -->
    {{--<script src="{{asset('resources/js/content.js?v=1.0.0')}}"></script>--}}


    <!-- Page-Level Scripts -->
    <script>
        $(document).ready(function () {
            $("#search_result").click(function () {
                var start = $("#start").val();
                var end = $("#end").val();
                if(start.length > 0 && end.length<=0)
                {

                    layer.alert('未选择结束日期！', {
                        icon: 8,
                        skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
                    });
                    return;
                }
                if(end.length > 0 && start.length <= 0)
                {
                    layer.alert('未选择起始日期！', {
                        icon: 8,
                        skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
                    });
                    return;
                }
                $('#form_search').submit();
            });
            $('.dataTables-example').dataTable();

            /* Init DataTables */
            var oTable = $('#editable').dataTable();

            /* Apply the jEditable handlers to the table */
            oTable.$('td').editable('../example_ajax.php', {
                "callback": function (sValue, y) {
                    var aPos = oTable.fnGetPosition(this);
                    oTable.fnUpdate(sValue, aPos[0], aPos[1]);
                },
                "submitdata": function (value, settings) {
                    return {
                        "row_id": this.parentNode.getAttribute('id'),
                        "column": oTable.fnGetPosition(this)[2]
                    };
                },

                "width": "90%",
                "height": "100%"
            });


        });

        function fnClickAddRow() {
            $('#editable').dataTable().fnAddData([
                "Custom row",
                "New row",
                "New row",
                "New row",
                "New row"]);

        }
    </script>
@endsection
