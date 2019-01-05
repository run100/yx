{{--
    这是一个万用 Layout;
    通过模板参数来控制 jQuery、Bootstrap、SweetAlert 库的加载，提供统一的 H5 底层界面及规范化资源加载。
    模板参数:
        参考 lconfig 注释。
        子模板可调用 lconfig 覆盖默认配置

    @section('title')
        标题

    @section('keywords')
        SEO关键字

    @section('description')
        SEO引言

    @section('head')
        HTML 头(style 之前)

    @section('styles')
        前置加载 CSS 样式

    @section('body')
        HTML body 内容

    @section('scripts')
        第三方 JS 库，以及页面 JS。(后置加载)
--}}
@lconfig([
    //设置模板参数默认值
    'is_mobile'     => false,   //手机端模式
    'is_clean'      => false,   //干净模式，不加载各种库
    'css_basic'     => true,    //加载365jia-css-basic
    'js_jquery'     => true,    //加载jQuery库
    'js_bootstrap'  => false,   //加载Bootstrap库
    'js_sweetalert' => true,    //SweetAlert库
    'js_iefix'      => true,    //修复低版本IE兼容性
    'js_cookie'     => false,   //是否加载js-cookie
])
<!doctype html>
<html lang="zh-cmn-Hans">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>@yield('title')</title>
    <meta name="keywords" content="@yield('keywords')" />
    <meta name="description" content="@yield('description')" />

    @if($__layout['is_mobile'])
        <meta name="viewport" content="width=device-width,initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
        <meta content="telephone=no" name="format-detection" />
    @endif

    @section('head')
    @show

    @if(!$__layout['is_clean'] && $__layout['css_basic'])
        @if($__layout['is_mobile'])
            <link rel="stylesheet" type="text/css" href="http://365jia.cn/css/m/mobile-basic.css" />
        @else
            <link rel="stylesheet" type="text/css" href="http://365jia.cn/css/basic.css" />
        @endif
    @endif

    @if(!$__layout['is_clean'] && $__layout['js_bootstrap'])
        <link rel="stylesheet" type="text/css" href="/vendor/zhuanti/bootstrap/dist/css/bootstrap.min.css" />
    @endif

    @if(!$__layout['is_mobile'] && $__layout['js_iefix'])
        <!--[if lt IE 9]>
        <script type="text/javascript" src="/vendor/zhuanti/html5shiv/dist/html5shiv.min.js"></script>
        <script type="text/javascript" src="/vendor/zhuanti/respond/dest/respond.min.js"></script>
        <![endif]-->
    @endif

    @if($__layout['js_cookie'])
        <script type="text/javascript" src="/vendor/zhuanti/js-cookie/src/js.cookie.js"></script>
    @endif

    @section('styles')
    @show

    @section('front_scripts')
    @show

</head>
<body>

@section('body')
@show


@if(!$__layout['is_clean'] && $__layout['js_jquery'])
    <script type="text/javascript" src="/vendor/zhuanti/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="/vendor/zhuanti/jquery-deserialize/dist/jquery.deserialize.min.js"></script>
    <script type="text/javascript" src="/js/jquery.ba-serializeobject.min.js"></script>
@endif

@if(!$__layout['is_clean'] && $__layout['js_bootstrap'])
    <script type="text/javascript" src="/vendor/zhuanti/bootstrap/dist/js/bootstrap.min.js"></script>
@endif

@if(!$__layout['is_clean'] && $__layout['js_sweetalert'])
    <script type="text/javascript" src="/vendor/zhuanti/sweetalert/dist/sweetalert.min.js"></script>
@endif

@section('scripts')
@show

</body>
</html>
