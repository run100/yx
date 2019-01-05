<!DOCTYPE html>
<html>
<head lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{$proj->name}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <link rel="stylesheet" type="text/css" media="screen" href="http://365jia.cn/css/m/mobile-basic.css" />
    <link rel="stylesheet" href="/vendor/redpacket/style/css.css" type="text/css" />
    <style>
        .btn_kin {
            background: {{isset($proj->configs->hongbao->btn_bg_color) && !empty($proj->configs->hongbao->btn_bg_color) ? $proj->configs->hongbao->btn_bg_color : 'linear-gradient(#FEE702, #FDB900)'}};
            color: {{isset($proj->configs->hongbao->btn_txt_color) && !empty($proj->configs->hongbao->btn_txt_color) ? $proj->configs->hongbao->btn_txt_color : '#8f0f07'}};
        }
        .btn_kin2 {
            background: {{isset($proj->configs->hongbao->btn_bg_color) && !empty($proj->configs->hongbao->btn_bg_color) ? $proj->configs->hongbao->btn_bg_color : 'linear-gradient(#FEE702, #FDB900)'}};
            color: {{isset($proj->configs->hongbao->btn_txt_color) && !empty($proj->configs->hongbao->btn_txt_color) && $proj->configs->hongbao->btn_txt_color!=='#8f0f07' ? $proj->configs->hongbao->btn_txt_color : '#FF0000'}};
        }
        body {
            background: {{isset($proj->configs->hongbao->bg_color) && !empty($proj->configs->hongbao->bg_color) ? $proj->configs->hongbao->bg_color : '#E41D0D'}};
        }
    </style>
</head>
<body>
<div class="box">
    <div class="re ov">
        <a id="ruleBtn" href="javascript:void(0)" class="active_rule_pos">活动规则</a>
        <img src="{{isset($proj->configs->hongbao->img) && !empty($proj->configs->hongbao->img) ? uploads_url($proj->configs->hongbao->img) : '/vendor/redpacket/images/banner_00.jpg'}}" class="wp100" onclick="return false;"/>
    </div>
    <div class="re mb20">
        <div class="pl20 pr20">
            <!--中奖消息-->
            <div class="pl35 pr35">
                <div class="winning_news_sty">
                    <ul class="notice-list" id="bannerUl">
                        @foreach($wins as $win)
                            @php
                                $win = wj_json_decode($win);
                                $l = mb_strlen($win['n']) - 4;
                                if ($l > 0) {
                                    $win['n'] = mb_substr($win['n'], 0, 2).($l >= 5 ? '*****' : str_repeat('*', $l)).mb_substr($win['n'], -2);
                                }
                            @endphp
                            <li><span class="mr10">{{$win['n']}}</span>刚刚领取了{{bcdiv($win['m'], 100, 2)}}元红包！</li>
                        @endforeach
                    </ul>
                </div>
            </div><!--中奖消息-->
            <!--内容区域-->
            <div class="mt15 content_redact_area">
                <p class="tac">
                    <img id="uPoster" src="{{$player['poster']}}" class="img1 vm"/>
                    <span id="uName" class="fz16 ml10 vm">{{$player['name']}}</span>
                </p>
                @if($money>0)
                    <p id="hbStusMsg" class="mt20 fz16 tac">恭喜你帮好友{{$player['name']}}拆了<span class="mar2 cor_4">{{$money}}元</span>！</p>
                    @if ($player['sy_count'] <= 0)
                        <p id="drawHbMsg" class="fz16 tac">好友已邀请完成，可拆红包了</p>
                    @else
                        <p id="drawHbMsg" class="fz16 tac">再邀请<span class="mar2 cor_4">{{$player['sy_count']}}位</span>好友即可拆开红包</p>
                    @endif
                @else
                    <p id="hbStusMsg" class="mt20 fz16 tac">你的好友<span class="mar2 cor_4">{{$player['name']}}</span>请你帮TA拆开红包</p>
                    <p id="drawHbMsg" class="fz16 tac">TA的口令是{{$proj->configs->hongbao->ply_pre}}{{$player['ticket_no']}}哦~</p>
                @endif
                <div class="mt20">
                    <ul class="fl_dib ml-15 package_list" id="packageList">
                        @for($i=0;$i<$proj->configs->hongbao->hb_zl_count;$i++)
                            <li>
                                @if(isset($player['zls'][$i]))
                                    <div class="re">
                                        <p class="prize_money_pos">{{$player['zls'][$i]['m']}} 元</p>
                                        <span class="head_portrait_pos"><img src="{{$player['zls'][$i]['p']}}"/></span>
                                    </div>
                                    <img src="/vendor/redpacket/images/ico_04.png" class="wp100">
                                @else
                                    <img src="/vendor/redpacket/images/ico_03.png" class="wp100">
                                @endif
                            </li>
                        @endfor
                    </ul>
                </div>
            </div><!--内容区域-->
            @if($money > 0)
                <div class="mt30 tac"><a href="javascript:void(0);" class="cor_7 fz16 tdu" id="shareBtn">邀请好友帮拆红包</a></div>
            @else
                <div class="mt30 tac"><a href="javascript:void(0);" class="btn_02 btn_kin2" data-clipboard-text="{{$proj->configs->hongbao->ply_pre}}{{$player['ticket_no']}}" id="zlBtn">帮TA拆红包</a></div>
            @endif
            <div class="mt15 tac"><a href="{{$proj->path}}" class="btn_01 btn_kin">我也要拆红包</a></div>
            <p class="mt10 fz16 tac cor_f">听说红包是现金大红包哦~</p>
        </div><!--pl20 pr20-->
        <div class="content_bg_pos"><img src="/vendor/redpacket/images/banner_03.jpg" class="wp100" onclick="return false;"/></div>
    </div>
    <div class="fixed_hint_sty dn" id="popAlert"><p class="ell"></p></div>
    <div class="bomb_box3 dn">
        <div class="bomb_box_text tac">
            <i class="btn_colse_bomb_box"></i>
            <div class="pa20 bg1 bar_2">
                <p class="fz16 lh24 cor_f">
                    听说要先输入口令才能帮拆红包，识别下方二维码，粘贴-回复口令“{{$proj->configs->hongbao->ply_pre}}{{$player['ticket_no']}}”<br/>就可拆开红包哦~
                </p>
                <div class="mt15 mb10"><img src="{{uploads_url($proj->configs->hongbao->qrcode)}}" class="img3"/></div>
            </div>
        </div>
    </div>
    <div class="bomb_box4 dn">
        <div class="bomb_box_text_2 tac">
            <i class="btn_colse_bomb_box"></i>
            <div class="pa20 bg1 bar_2">
                <div class="scroll_height_area tal">
                    <h2 class="fz18 fwb tac mb10">活动规则</h2>
                    {!! htmlspecialchars_decode($proj->rules) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="share" style="display: none;">
        <div class="sharePic">
            <div><img src="/vendor/redpacket/images/ico_tac_share.png" class="wp100"></div>
            <div class="tac"><img src="/vendor/redpacket/images/ico_ok_hide.png" class="hide_share"/></div>
        </div>
        <div class="shareBg"></div>
    </div>
</div><!--box-->
@include('zhuanti::public_redpacket._comjs')
</body>
</html>