@extends('mes.layouts')
@section('content')
        <div class="row">
            <div class="col-sm-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>执行结果</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="notifications.html#">
                                <i class="fa fa-wrench"></i>
                            </a>

                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="alert alert-{{$msg_type}}">
                            {{$msg}}<br/>
                            <a class="alert-link" href="{{$back_url}}">{{$back_str}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection