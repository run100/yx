<!DOCTYPE html>
<html>
<head lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{$proj->name }}</title>
    <meta name="description" content="{{$proj->name }}" />
    <meta name="keywords" content="{{$proj->name }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <link rel="stylesheet" type="text/css" media="screen" href="http://365jia.cn/css/m/mobile-basic2.0.css" />
    <link rel="stylesheet" href="/vendor/jizi/style/css.css" type="text/css" />
    <script type="text/javascript" src="/vendor/zhuanti/zepto/zepto.min.js"></script>
    <script type="text/javascript" src="/vendor/zhuanti/resize/320.js"></script>
    <script type="text/javascript" src="/js/clipboard.min.js"></script>
    <style>
        body{
            background: {{isset($proj->configs->jizi->jizi_bgcolor)? $proj->configs->jizi->jizi_bgcolor : '#0A58BC'}}
        }
        .btn_zt_com{
            background-color: {{isset($proj->configs->jizi->jizi_btcolor)? $proj->configs->jizi->jizi_btcolor : '#FEE900'}};
            color: {{isset($proj->configs->jizi->jizi_ftcolor)? $proj->configs->jizi->jizi_ftcolor : '#fff'}}
        }
        @if($proj->configs->jizi->font_type == 'picture9' || $proj->configs->jizi->font_type == 'picture12')
        .ico_chart_bg,.ico_chart_bg_01 {
            display: inline-block;
            width:  .91rem;
            height: .91rem;
            background: url({{isset($proj->configs->jizi->jizi_picture) ? uploads_url($proj->configs->jizi->jizi_picture):'/vendor/jizi/images/picture12_list.png'}}) no-repeat;
            background-size: 2.7rem;
            vertical-align: middle;
        }
        .ico_chart_bg_01 {
            background: url({{isset($proj->configs->jizi->jizi_picture) ? uploads_url($proj->configs->jizi->jizi_picture):'/vendor/jizi/images/picture12_list.png'}}) no-repeat;
            background-size: 2.7rem;
        }
        .ico_chart_00 { background-position: 0 0; }
        .ico_chart_01 { background-position: -.9rem 0; }
        .ico_chart_02 { background-position: -1.8rem 0; }
        .ico_chart_03 { background-position: 0 -.9rem; }
        .ico_chart_04 { background-position: -.9rem -.9rem; }
        .ico_chart_05 { background-position: -1.8rem -.9rem; }
        .ico_chart_06 { background-position: 0 -1.8rem; }
        .ico_chart_07 { background-position: -.9rem -1.8rem; }
        .ico_chart_08 { background-position: -1.8rem -1.8rem; }
        .ico_chart_09 { background-position: 0 -2.7rem; }
        .ico_chart_10 { background-position: -.9rem -2.7rem; }
        .ico_chart_11 { background-position: -1.8rem -2.7rem; }
        @else
         .list_fz_sty li.current span{
            color: {{isset($proj->configs->jizi->jizi_sfcolor)? $proj->configs->jizi->jizi_sfcolor : '#0A58BC'}}
         }
        @endif
    </style>
</head>
<body>
<div class="box">
    <div><img src="{{isset($proj->configs->jizi->jizi_img) ?  uploads_url($proj->configs->jizi->jizi_img) : '/vendor/jizi/images/topbg.jpg'}}" class="wp100"/></div>
    <div class="pl25 pr25">
        <div class="bg2 pa15 cor_f lh20 mb20 mt10">&emsp;&emsp;
            @if($isZhuli != 1)
                您的好友"{{$playerInfo['info_wx_nickname']}}"正在参与"{{$proj->name}}"活动，赶快为TA助力吧！
            @else
                恭喜您，已为好友"{{$playerInfo['info_wx_nickname']}}"助力成功！你也赶快报名参加吧！
            @endif
        </div>

        <dl class="mt20 tac">
            <dt class="mr10 di vm"><a href="javascript:void(0)"><img src="{{$playerInfo['info_wx_headimg']}}" class="img1"/></a></dt>
            <dd class="cor_4 lh1 tal di vm">
                <p>昵称: {{$playerInfo['info_wx_nickname']}}</p>
                <p>编号: {{$proj->configs->jizi->jizi_pre}}{{$playerInfo['ticket_no']}}</p>
            </dd>
        </dl>

        @include('zhuanti::public_jizi._'.$proj->configs->jizi->font_type)

        @if($isZhuli != 1)
        <p class="mar4"><a href="javascript:void(0)" id="shareBtn" data-content="{{$proj->configs->jizi->jizi_pre}}{{$playerInfo['ticket_no']}}" class="btn_1 btn_zt_com">为{{$playerInfo['info_wx_sex']}}助力</a></p>
        @else
        <p class="mar4"><a href="javascript:void(0)" class="btn_1 btn_1_un">为{{$playerInfo['info_wx_sex']}}助力成功</a></p>
        @endif
        <p class="mt5 tac cor_f">已有{{$jizi['_friends']}}位好友为{{$playerInfo['info_wx_sex']}}助力</p>
        <p class="mar4 mt20"><a href="{{$proj->path}}/baoming" class="btn_1 btn_zt_com">我也要参加</a></p>
        <div class="bg2 pa15 cor_f lh20 mb20 mt25">
            {!! htmlspecialchars_decode($proj->rules) !!}
        </div>

        <div class="pb30">
            <p class="tac"><span class="tle_2">网友中奖名单</span></p>
            <table class="table_1 mt10">
                <colgroup>
                    <col style="width: 20%" />
                    <col style="width: 45%" />
                    <col style="width: 35%" />
                </colgroup>
                <thead>
                <tr>
                    <th>序号</th>
                    <th>昵称</th>
                    <th>奖品</th>
                </tr>
                </thead>
                <tbody id="winsTbody">

                </tbody>

            </table>
            <div class="mt25 mb25 page_box" style="display: none" id="pageBox">
                <a class="page_item" href="javascript:void(0)" data-type="1">首页</a>
                <a class="page_item" href="javascript:void(0)" data-type="2">上一页</a>
                <a class="page_item" href="javascript:void(0)" data-type="0" id="currentPage">1</a>
                <a class="page_item" href="javascript:void(0)" data-type="3">下一页</a>
                <a class="page_item" href="javascript:void(0)" data-type="4">末页</a>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="wxShareConfig" data-title="{{$proj->configs->share_title}}" data-share="{{$proj->configs->share_desc}}" data-link="{{$proj->path}}/{{$playerInfo['md5key']}}" data-img="{{ isset($proj->configs->share_image) ? '/uploads/'.$proj->configs->share_image : '' }}" data-url="{{route('wechat.jssdk_config', [], false)}}">
<input type="hidden" id="project" data-path="{{$proj->path}}">
<input type="hidden" id="pageHtml" value="player">
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.0.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/ztjs/common.js?v=1"></script>
<script type="text/javascript" src="/vendor/prizes/js/prizes_common.1.0.js"></script>
<script type="text/javascript" src="/vendor/jizi/js/jizi.js"></script>
<script type="text/template" id="playerTemp">
    <i class="btn_colse_bomb_box"></i>
    <p class="mt10 fz14 cor_6">系统已自动复制编号“{{$proj->configs->jizi->jizi_pre}}{{$playerInfo['ticket_no']}}”，长按识别下方二维码，在公众号底部长按输入框，选择粘贴-回复，即可助力</p>
    <p class="mt20 tac"><img src="{{ isset($proj->configs->jizi->wechat_img) ? uploads_url( $proj->configs->jizi->wechat_img ) : (isset($proj->configs->jizi->wechat_img2) ? uploads_url( $proj->configs->jizi->wechat_img2 ) : '') }}" class="img2" /></p>
</script>
</body>
</html>