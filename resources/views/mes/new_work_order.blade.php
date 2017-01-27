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
                    <h5>新建工单</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal m-t" method="post" action="" id="add_work">
                        {{csrf_field()}}
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">工单号：</label>
                                    <div class="col-sm-4">
                                        <input name="work_name" class="form-control" placeholder="工单号" type="text" id="work_name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">产品名称：</label>
                                    <div class="col-sm-2">
                                        <select class="form-control" id="product" name="product">
                                            <option></option>
                                            @foreach($product as $product)
                                                <option value="{{$product->product_list_serial}}">{{$product->product_list_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Lot Type：</label>
                                    <div class="col-sm-2">
                                        <select class="form-control" name="lot_type" id="lot_type">
                                            <option></option>
                                            <option value="0">P</option>
                                            <option value="1">Q</option>
                                            <option value="2">R</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">QTY：</label>
                                    <div class="col-sm-3">
                                        <input name="qty" class="form-control" placeholder="请输入数字" type="text" id="qty">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Owner：</label>
                                    <div class="col-sm-3">
                                        <input name="owner" class="form-control" placeholder="Owner" type="text" id="owner">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Customer：</label>
                                    <div class="col-sm-3">
                                        <input name="customer" class="form-control" placeholder="Customer" type="text" id="customer">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Create Day：</label>
                                    <div class="col-sm-3">
                                        <input placeholder="" class="form-control layer-date" id="start_day" name="start_day">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Due Day：</label>
                                    <div class="col-sm-3">
                                        <input placeholder="" class="form-control layer-date" id="due_day" name="due_day">
                                    </div>
                                </div>
                                <div class="form-group col-sm-8">
                                    <button type="submit" id="commit" class="btn btn-success" style="float: right;">提交</button>
                                </div>
                                <input id="msg" type="hidden" value="{{$msg}}">
                                <input id="err" type="hidden" value="{{$err}}">
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
<script src="{{asset('resources/js/plugins/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('resources/js/plugins/validate/messages_zh.min.js')}}"></script>
<script>
    $(document).ready(function () {
        var msg = $("#msg").val();
        var err = $("#err").val();
        if(msg.length > 0)
        {
            toastr.success(msg, 'Success Massage!')
        }
        if(err.length > 0)
        {
            toastr.error(err, 'Error Massage!')
        }
        var icon = "<i class='fa fa-times-circle'></i> ";
        $("#add_work").validate({
            rules: {
                work_name: {
                    required: true,
                    minlength: 4
                },
                product: "required",
                lot_type: "required",
                qty: {
                    required: true,
                    digits:true
                },
                owner: "required",
                customer: "required",
                due_day: {
                    required: true,
                    date:true
                }
            },
            message: {
                work_name: {
                    required:icon + "请输入工单号",
                    minlength:icon + "工单号长度最少为4个字符"
                },
                product: icon + "请选择产品",
                lot_type: icon + "请选择Lot Type",
                qty: {
                    required: icon + "请输入数量",
                    digits: icon + "请填入整数"
                },
                owner: icon + "请输入负责人",
                customer: icon + "请输入客户名",
                due_day: {
                    required: icon + "请选择预计完成时间",
                    date: icon + "请填入正确的日期格式"
                }
            }
        });
//        $("#commit").click(function () {
//            var work_name = $("#work_name").val();
//            var product = $("#product").val();
//            var lot_type = $("#lot_type").val();
//            var qty = $("#qty").val();
//            var owner = $("#owner").val();
//            var customer = $("#customer").val();
////            var start_day = $("input[name='start_day']").val();
//            var due_day = $("input[name='due_day']").val();
//
//            if(work_name.length<=0)
//            {
//                layer.alert('未填写工单号！', {
//                    icon: 8,
//                    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
//                });
//                return;
//            }
//            if(product.length<=0)
//            {
//                layer.alert('未选择制程代码！', {
//                    icon: 8,
//                    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
//                });
//                return;
//            }
//            if(lot_type.length<=0)
//            {
//                layer.alert('未选择Lot Type！', {
//                    icon: 8,
//                    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
//                });
//                return;
//            }
//            if(qty.length<=0)
//            {
//                layer.alert('未填入数量！', {
//                    icon: 8,
//                    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
//                });
//                return;
//            }
//            if(owner.length<=0)
//            {
//                layer.alert('未填入负责人！', {
//                    icon: 8,
//                    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
//                });
//                return;
//            }
//            if(customer.length<=0)
//            {
//                layer.alert('未填入客户信息！', {
//                    icon: 8,
//                    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
//                });
//                return;
//            }
//            if(due_day.length<=0)
//            {
//                layer.alert('未选择预计完成日期！', {
//                    icon: 8,
//                    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
//                });
//                return;
//            }
//            $("#add_work").submit();
//        });
    });

    //外部js调用
    laydate({
        elem: '#due_day', //目标元素。由于laydate.js封装了一个轻量级的选择器引擎，因此elem还允许你传入class、tag但必须按照这种方式 '#id .class'
        event: 'focus' ,//响应事件。如果没有传入event，则按照默认的click
        format: 'YYYY-MM-DD',
//            min: laydate.now(),
        max: '2099-06-16 23:59:59',
        istime: true,
        istoday: false
    });
    //        laydate.skin('yalan');
    //日期范围限制
    var start = {
        elem: '#start_day',
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

    laydate(start);

</script>
@endsection
