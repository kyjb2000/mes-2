@extends('mes.layouts')
@section('css')

@endsection
@section('content')
    <?php $c_make_area = 0; ?>
    @foreach($make_area as $make_area)
        <?php ++$c_make_area;$sum_lot = 0;
        ?>
        @if($c_make_area % 2 == 1)
            <div class="row">
        @endif
            <div class="col-sm-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{$make_area->make_area_name}}WIP</h5>

                        <div class="ibox-tools">

                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a href="{{url('station_wip')}}?<?php echo lcg_value(); ?>">
                                <i class="fa fa-refresh"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                    @if(isset($run_trace[$make_area->make_area_serial]))
                        <?php $c_lot = 1;

                        ?>
                        @foreach($run_trace[$make_area->make_area_serial] as $run_trace_row)
                            @if($run_trace_row->run_trace_state_type == 11 || $run_trace_row->run_trace_state_type == 13)
                                <span style="color: black;">{{$run_trace_row->run_trace_lotno}}
                            @elseif($run_trace_row->run_trace_state_type == 12)
                                    <span style="color: green;">{{$run_trace_row->run_trace_lotno}}
                            @elseif($run_trace_row->run_trace_state_type == 20)
                                <span style="color: #EB6A05;">{{$run_trace_row->run_trace_lotno}}
                            @elseif($run_trace_row->run_trace_state_type == 21)
                                <span style="color: #F00780;">{{$run_trace_row->run_trace_lotno}}
                            @endif

                            ({{$run_trace_row->run_trace_num}})</span>
                            @if($c_lot % 6 == 0)
                                <br/>
                            @else
                                &nbsp;&nbsp;&nbsp;
                            @endif
                            <?php $c_lot++;
                                $sum_lot += $run_trace_row->run_trace_num;
                            ?>
                        @endforeach
                    @else
                        无
                    @endif
                        <br/>
                        <div style="width: 70px;float:right;">小计:{{$sum_lot}}</div>
                    </div>
                </div>
            </div>
        @if($c_make_area % 2 == 0)
        </div>
        @endif
    @endforeach

    @if($c_make_area % 2 == 1)
        </div>
    @endif

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
