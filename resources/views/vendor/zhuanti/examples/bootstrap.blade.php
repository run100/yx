@extends('zhuanti::common.normal')

@lconfig([
    'js_bootstrap'  => true
])

@section('title', '我是标题')
@section('keywords', '我是关键字')
@section('description', '我是摘要')

@section('head')
    {{-- 头部其他内容 --}}
    <meta name="xx" content="xx" />
@stop

@section('body')
    {{-- Body其他内容 --}}

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal">
        我是按钮
    </button>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">我是弹窗标题</h4>
                </div>
                <div class="modal-body">
                    我是弹窗
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary">确认</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('styles')
    {{-- 加入其他样式 --}}
    <!--<link rel="stylesheet" type="text/css" href="xxx" />-->
@stop

@section('scripts')
    <script type="text/javascript">
        $(function() {
            console.log('我是日志');
        });
    </script>
@stop
