@extends('mes.layouts')
@section('css')
    @parent
    <link href="{{asset('resources/css/plugins/toastr/toastr.min.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-10">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Create Lot</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal m-t" method="post" action="{{url("create_lot_list")}}/{{$work_order->work_order_serial}}" id="create_lot">
                        {{csrf_field()}}
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">工单号：</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static">{{$work_order->work_order_name}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">产品名称：</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static">{{$work_order->work_order_product_name}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">客户：</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static">{{$work_order->work_order_customer}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">制程代码：</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static">{{$work_order->work_order_pro_name}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">投产类别：</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static">
                                            @if($work_order->work_order_type == 0)
                                                P
                                            @elseif($work_order->work_order_type == 1)
                                                Q
                                            @elseif($work_order->work_order_type == 2)
                                                R
                                            @else
                                                P
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">优先次序：</label>
                                    <div class="col-sm-2">
                                        <select class="form-control" name="priority">
                                            <option value="0">0</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                            <option value="9">9</option>
                                            <option value="10">10</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">投产数量：</label>
                                    <div class="col-sm-2">
                                        <input name="all_num" class="form-control" placeholder="请输入数字" type="text" id="all_num">
                                        <span style="color: red;">最多<span class="note1">{{$work_order->work_order_rest_qty-$work_order->work_order_wip_qty}}</span>片</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">批次数量：</label>
                                    <div class="col-sm-2">
                                        <input name="per_num" class="form-control" placeholder="默认每批18片" type="text" id="per_num">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-8">
                                <button id="back" class="btn btn-success" type="button">返回</button>
                                <button type="button" id="commit" class="btn btn-success" style="float: right;">提交</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('plugins')
<script src="{{asset('resources/js/plugins/layer/laydate/laydate.js')}}"></script>
<script src="{{asset('resources/js/plugins/toastr/toastr.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $("#back").click(function () {

            window.location.href = "{{url("create_run_card")}}";

        });

        $("#commit").click(function () {
            var all_num = $("#all_num").val();
            var note1 = $(".note1").text();

            if(all_num.length<=0)
            {
                layer.alert('未填写投产数量！', {
                    icon: 8,
                    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
                });
                return;
            }
            if(parseInt(note1) < parseInt(all_num))
            {
                layer.alert('投产数量过多！', {
                    icon: 8,
                    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
                });
                return;
            }

            $("#create_lot").submit();
        });

    });



</script>
@endsection
