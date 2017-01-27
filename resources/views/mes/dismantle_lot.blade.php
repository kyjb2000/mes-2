@extends('mes.layouts')
@section('css')

@endsection
@section('content')

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Run Card拆批</h5>

                        <div class="ibox-tools">

                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a href="{{url('dismantle_lot')}}/{{$lot_id}}#<?php echo lcg_value(); ?>">
                                <i class="fa fa-refresh"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">

                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>勾选</th>
                                    <th>Wafer</th>
                                    <th>Lot Type</th>
                                    <th>priority</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $num = 1;
                                        $index = 0;
                                ?>
                                @while($lot_num >= $num)
                                    @if((($wafer_pos>>$index) & 1) == 1)
                                    <tr>
                                        <td>{{$num}}</td>
                                        <td><input type="checkbox" class="box" value="{{$index+1}}"/></td>
                                        <td>
                                            @if(($index+1) < 10)
                                                {{$lot_no}}-0{{$index+1}}
                                            @else
                                                {{$lot_no}}-{{$index+1}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($lot_type == 0)
                                                P
                                            @elseif($lot_type == 1)
                                                Q
                                            @elseif($lot_type == 2)
                                                R
                                            @else
                                                P
                                            @endif
                                        </td>
                                        <td>{{$priority}}</td>
                                    </tr>
                                    <?php $num++; ?>
                                    @endif
                                    <?php $index++; ?>
                                @endwhile

                            </tbody>
                            <tfoot>
                            <tr>
                            <tr>
                                <th>NO</th>
                                <th>勾选</th>
                                <th>Wafer</th>
                                <th>Lot Type</th>
                                <th>priority</th>
                            </tr>
                            </tfoot>

                        </table>
                        <form method="post" action="{{url('dismantle_lot')}}/{{$lot_id}}" id="dismantle_lot">
                            {{csrf_field()}}
                            <input type="hidden" name="dis_wafer" value="">
                            <input type="hidden" name="dis_num" value="">
                            <input type="hidden" name="parent_num" value="{{$lot_num}}">
                            <table style="width: 530px;margin:auto; clear: both;">
                                <tbody><tr>
                                    <td align="center" width="100px;"></td>
                                    <td align="center">制程代码</td>
                                    <td align="center">主制程</td>
                                    <td align="center">子制程</td>
                                </tr>
                                <tr>
                                    <td align="center" width="100px;">选择流程：</td>
                                    <td align="center"><select id="process" name="process_id" style="width: 100px;height: 25px;">
                                            <option></option>
                                            @foreach($main_config as $process)
                                                <option value="{{$process->main_config_serial}}">{{$process->main_config_name}}</option>
                                            @endforeach
                                        </select></td>
                                    <td align="center"><select id="main" name="main_id" style="width: 180px;height: 25px;"><option></option></select></td>
                                    <td align="center"><select id="class" name="class_id" style="width: 150px;height: 25px;"><option></option></select><br></td>
                                </tr>
                                </tbody></table><br/>
                            <button id="back" class="btn btn-success" type="button">返回</button>
                            <button type="button" id="commit" class="btn btn-success" style="float: right;">提交</button>
                        </form>
                        <br/>
                    </div>
                </div>
            </div>
        </div>


@endsection
    <!-- 全局js -->
@section('plugins')

    <!-- Data Tables -->
    <script src="{{asset('resources/js/plugins/dataTables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('resources/js/plugins/dataTables/dataTables.bootstrap.js')}}"></script>

    <!-- 自定义js -->
    {{--<script src="{{asset('resources/js/content.js?v=1.0.0')}}"></script>--}}


    <!-- Page-Level Scripts -->
    <script>
        $(document).ready(function () {

            $('.dataTables-example').dataTable();

            var process_id = 0;
            var main_id = 0;
            var class_id = 0;
            $("#process").change(function () {
                process_id = $(this).find("option:selected").val();
                htmlobj = $.ajax({
                    type: 'post',
                    url:"{{url('ajax_sel_flow')}}",
                    async: false,
                    dataType: 'html',
                    data: {process_id:process_id,"_token": "{{csrf_token()}}" },
                    success:function (data) {
//                    alert(data);
                        $("#main").empty();
                        $("#class").empty();
                        $("#main").append(data);
                    },
                    error:function () {
                        alert("false");
                    }
                })
            });
            $("#main").change(function () {
                main_id = $(this).find("option:selected").val();
                htmlobj = $.ajax({
                    type: 'post',
                    url:"{{url('ajax_sel_flow')}}",
                    async: false,
                    dataType: 'html',
                    data: {main_id:main_id,"_token": "{{csrf_token()}}"},
                    success:function (data) {
//                    alert(data);
                        $("#class").empty();
                        $("#class").append(data);
                    },
                    error:function () {
                        alert("false");
                    }
                })
            });

            $("#commit").click(function () {
                var pos = 0;
                var num = 0;
                var class_id = $("#class").find("option:selected").val();
                $(".box").each(function () {
                    if($(this).is(":checked"))
                    {
                        pos |= (1<<($(this).val()-1));
                        num++;
                    }
                });
                $("input[name='dis_wafer']").val(pos);
                $("input[name='dis_num']").val(num);

                if(pos <= 0 || num <= 0)
                {
                    alert("未选择要拆批的wafer！");
                    return;
                }
                if(process_id <= 0)
                {
                    alert("未选择制程代码！");
                    return;
                }
                if(main_id <= 0)
                {
                    alert("未选择主制程！");
                    return;
                }
                if(class_id <= 0)
                {
                    alert("未选择子制程！");
                    return;
                }
                $("#dismantle_lot").submit();

            });
            $("#back").click(function () {
                window.location.href = "{{url('dismantle_run_card')}}";
            })
        });

    </script>
@endsection
