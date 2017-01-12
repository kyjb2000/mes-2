@extends('mes.layouts')
@section('css')

@endsection
@section('content')

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Run Card开立</h5>

                        <div class="ibox-tools">

                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
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
                                    <th>Process Name</th>
                                    <th>Create Date</th>
                                    <th>Comment</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $num = 1; ?>
                                @foreach ($lot_no as $lot_no)
                                    <tr>
                                        <td>{{$num}}</td>
                                        <td>
                                            {{$lot_no->lot_no_id}}
                                        </td>
                                        <td>
                                            @if((ceil($all_num / $per_num)) == $num)
                                                @if($all_num % $per_num == 0)
                                                    {{$per_num}}
                                                @else
                                                    {{$all_num % $per_num}}
                                                @endif
                                            @else
                                                {{$per_num}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($work_order->work_order_type == 0)
                                                P
                                            @elseif($work_order->work_order_type == 1)
                                                Q
                                            @elseif($work_order->work_order_type == 2)
                                                R
                                            @else
                                                P
                                            @endif
                                        </td>
                                        <td>{{$priority}}</td>
                                        <td>{{$work_order->work_order_pro_name}}</td>
                                        <td>{{$bill_date}}</td>
                                        <td></td>

                                    </tr>
                                    <?php $num++; ?>
                                @endforeach

                            </tbody>
                            <tfoot>
                            <tr>
                                <th>NO</th>
                                <th>Lot No</th>
                                <th>QTY</th>
                                <th>Lot Type</th>
                                <th>Priority</th>
                                <th>Process Name</th>
                                <th>Create Date</th>
                                <th>Comment</th>
                            </tr>

                            </tfoot>
                        </table>

                            <form id="create_lot_list" method="post" action="{{url('new_lot')}}/{{$work_order->work_order_serial}}">
                                <input name="all_num" value="{{$all_num}}" type="hidden">
                                <input name="lot_num" value="{{$per_num}}" type="hidden">
                                <input name="bill_date" value="{{$bill_date}}" type="hidden">
                                <input name="max_num" value="{{$work_order->work_order_rest_qty - $work_order->work_order_wip_qty}}" type="hidden">
                                <button id="backto" class="btn btn-success" type="button">返回</button>
                                <button type="button" id="commit" class="btn btn-success" style="float: right;">提交</button>
                            </form>

                    </div>
                </div>
            </div>
        </div>


@endsection
    <!-- 全局js -->
@section('plugins')


    <script src="{{asset('resources/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script>
        $(document).ready(function () {
            $("#commit").click(function () {
                $("#create_lot_list").submit();
            }) ;
            $("#backto").click(function () {
                window.location.href = '{{url('create_run_card')}}';
            }) ;
        });

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
