<!DOCTYPE html>
<html>
<head lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{$proj->name }}</title>
    <meta name="description" content="{{$proj->name }}"/>
    <meta name="keywords" content="{{$proj->name }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
    <link rel="stylesheet" type="text/css" media="screen" href="http://365jia.cn/css/m/mobile-basic2.0.css"/>
    <link rel="stylesheet" href="/vendor/jizi/style/css.css?v=1.3" type="text/css"/>
    <script type="text/javascript" src="/vendor/zhuanti/zepto/zepto.min.js"></script>
    <script type="text/javascript" src="/vendor/zhuanti/resize/320.js"></script>
    <style>
        body{
            background: {{isset($proj->configs->jizi->jizi_bgcolor)? $proj->configs->jizi->jizi_bgcolor : '#0A58BC'}}
        }
        .btn_zt_com{
            background-color: {{isset($proj->configs->jizi->jizi_btcolor)? $proj->configs->jizi->jizi_btcolor : '#FEE900'}};
            color: {{isset($proj->configs->jizi->jizi_ftcolor)? $proj->configs->jizi->jizi_ftcolor : '#fff'}}
        }
        form li label span{
            width: .5rem;
        }
    </style>
</head>
<body>
<div class="box">
    <div><img src="{{isset($proj->configs->jizi->jizi_img) ?  uploads_url($proj->configs->jizi->jizi_img) : '/vendor/jizi/images/topbg.jpg'}}" class="wp100"/></div>
    <div class="pl30 pr30">
        <form id="regForm" method="POST" action="{{ $proj->path }}/reg">
            <ul>
                @foreach($proj->configs->base_form_design as $form)
                    @if(isset($form->registration) && $form->registration == 'on' && in_array($form->type, ['string' , 'integer', 'name', 'phone', 'idcard', 'passport', 'email', 'qq', 'address', 'age', 'city']))
                        @php
                            $validate = [];
                            if(isset($form->required) && $form->required == 'on'){
                                $validate[] = 'required';
                            }
                            if(isset($form->type) && !empty($form->type)){
                                $validate[] = $form->type;
                            }
                        @endphp
                        <li class="ipt_out mt15"><label class="flex_box"><span class="cor_1 mr10">{{$form->name}}</span><span class="item"><input type="text" maxlength="18" name="info_{{$form->field}}" king-label="{{$form->name}}" king-filter="{{implode('|',$validate)}}"/></span></label></li>
                    @endif
                @endforeach
            </ul>
            <div class="mt15"><a href="javascript:void(0)" class="btn_1 btn_zt_com" id="bmBtn">确认报名</a></div>
            <p class="mt10 tac cor_f">以上信息仅用于中奖通知，请仔细核对</p>
            <div class="bg2 pa15 cor_f lh20 mb20 mt25">
                {!! htmlspecialchars_decode($proj->rules) !!}
            </div>
        </form>
    </div>
</div>
<input type="hidden" id="wxShareConfig" data-title="{{$proj->configs->share_title}}" data-share="{{$proj->configs->share_desc}}" data-link="{{$proj->path}}" data-img="{{ isset($proj->configs->share_image) ? '/uploads/'.$proj->configs->share_image : ''}}" data-url="{{route('wechat.jssdk_config', [], false)}}">
<input type="hidden" id="pageHtml" value="baoming">
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.0.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/ztjs/common.js?v=1"></script>
<script type="text/javascript" src="/vendor/jizi/js/jizi.js"></script>
</body>
</html>
