<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>Unicompound MES - {{$title or '功能'}}</title>


    <link rel="shortcut icon" href="favicon.ico"> <link href="{{asset('resources/css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
    <link href="{{asset('resources/css/font-awesome.css?v=4.4.0')}}" rel="stylesheet">

    <!-- Data Tables -->
    <link href="{{asset('resources/css/plugins/dataTables/dataTables.bootstrap.css')}}" rel="stylesheet">

    <link href="{{asset('resources/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('resources/css/style.css?v=4.1.0')}}" rel="stylesheet">
    @section('css')

    @show

</head>

<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">

    {{--主体内容--}}
    @section('content')
        <div class="row">
            <div class="col-sm-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>制造部文化</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <h1 class="text-danger">纪律 服务 效率
                            <small>
                                <p class="text-success"></p>
                                <p class="text-danger">工作要求：严守纪律、忠于所托</p>
                                <p class="text-danger">工作态度：积极服务、乐于助人</p>
                                <p class="text-danger">工作方法：重视效率、勤于学习</p>
                            </small>
                        </h1>
                    </div>
                </div>
            </div>

        </div>
    @show

</div>

<!-- 全局js -->
<script src="{{asset('resources/js/jquery.min.js?v=2.1.4')}}"></script>
<script src="{{asset('resources/js/bootstrap.min.js?v=3.3.6')}}"></script>
<script src="{{asset('resources/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
<script src="{{asset('resources/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
<script src="{{asset('resources/js/plugins/layer/layer.min.js')}}"></script>

<!-- 自定义js -->
<script src="{{asset('resources/js/hplus.js?v=4.1.0')}}"></script>
<script type="text/javascript" src="{{asset('resources/js/contabs.js')}}"></script>
<script src="{{asset('resources/js/content.js?v=1.0.0')}}"></script>

<!-- 第三方插件 -->
<script src="{{asset('resources/js/plugins/pace/pace.min.js')}}"></script>

@section('plugins')

@show

</body>

</html>