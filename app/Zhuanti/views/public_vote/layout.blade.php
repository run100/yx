<!DOCTYPE html>
<html>
<head lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{$proj->name }}</title>
    <meta name="description" content="{{$proj->name }}"/>
    <meta name="keywords" content="{{$proj->name }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <link rel="stylesheet" type="text/css" media="screen" href="http://365jia.cn/css/m/mobile-basic.css" />
    <link rel="stylesheet" href="{{ URL::asset('public_vote/style/css.css') }}" type="text/css" />
    <script type="text/javascript" src="{{ URL::asset('public_vote') }}/js/zepto.min.js"></script>
    <script type="text/javascript" src="/js/clipboard.min.js"></script>
    <script type="text/javascript" src="/vendor/zhuanti/js-cookie/src/js.cookie.js"></script>
    @include('web::common._header_baidu_tj')
    <base href="{{ URL::asset('public_vote') }}/">
    <style>body{ {{isset($proj->configs->vote->vote_bgcolor) ? 'background-color: '.$proj->configs->vote->vote_bgcolor : ''}} }</style>
</head>
<input type="hidden" id="projPath" value="{{$proj->path}}">
<body style="padding: 0;margin-bottom: 50px;">


@yield('content')
</body>
@include('zhuanti::public_vote._login')
</html>