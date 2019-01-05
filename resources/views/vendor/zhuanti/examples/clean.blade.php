@extends('zhuanti::common.normal')

@lconfig([
    'is_mobile'     => true,
    'is_clean'      => true,
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
    <button id="btn-test" class="btn btn-success">我是按钮</button>
@stop

@section('styles')
    {{-- 加入其他样式 --}}
    <!--<link rel="stylesheet" type="text/css" href="xxx" />-->
@stop

@section('scripts')
    <script type="text/javascript">

        window.onload = function() {
            document.getElementById('btn-test').onclick = function() {
                alert("我是弹窗");
            };

            console.log('我是日志');
        };
    </script>
@stop
