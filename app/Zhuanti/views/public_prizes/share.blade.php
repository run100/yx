<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{$proj->name }}</title>
    <meta name="description" content="{{$proj->name }}"/>
    <meta name="keywords" content="{{$proj->name }}"/>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" type="text/css" media="screen" href="http://365jia.cn/css/m/mobile-basic.css" />
    <link rel="stylesheet" type="text/css" href="/vendor/prizes/style/css.css">
    <style>
        .bg1{
            background: {{isset($proj->configs->draw->bg_color)? $proj->configs->draw->bg_color : '#FFCA00'}}
        }
    </style>
</head>
<body>
<div class="box bg1">
    <div><img src="{{isset($proj->configs->draw->img) ? uploads_url($proj->configs->draw->img) : '/vendor/prizes/images/banner_00.jpg'}}" class="wp100"></div>
    <div class="pl20 pr20 pb30">
        <div class="mt5 tac"><img src="{{$player['info_wx_headimg']}}" class="img3"/></div>
        <p class="mt5 fz14 tac cor_6">{{$player['info_wx_nickname']}}</p>
        <p class="mt25 fz16 lh26 tac fwb cor_6">我正在参加<span class="mar1 cor_4">{{$proj->name}}</span>你也<br/>快来参加吧</p>
        <div class="mt25 tac"><img src="{{isset($proj->configs->draw->wechat_img) ? uploads_url($proj->configs->draw->wechat_img) : ''}}" class="img4"/></div>
        <p class="mt30 fz14 lh16 tac cor_6">识别二维码，关注公众号“{{$proj->configs->draw->wechat_name}}”，</p>
        <p class="mt5 fz14 lh16 tac cor_6">回复关键词“{{$proj->configs->draw->keyword}}”即可参与活动</p>
        <p class="mt30 pt30 fz14 tac cor_f"> </p>
    </div>
</div>
<input type="hidden" id="wxShareConfig" data-title="{{$proj->configs->share_title}}" data-share="{{$proj->configs->share_desc}}" data-link="{{$proj->configs->draw->is_attention == 'Y' ? $proj->path.'/'.$playerKey : $proj->path}}" data-img="{{ isset($proj->configs->share_image) ? '/uploads/'.$proj->configs->share_image : '' }}" data-url="{{route('wechat.jssdk_config', [], false)}}">
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.0.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/zepto/zepto.min.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/ztjs/common.js?v=1"></script>
<script>setJSAPI();</script>
</body>
</html>