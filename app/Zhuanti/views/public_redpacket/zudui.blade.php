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
                    <img id="uPoster" src="/vendor/redpacket/images/poster.png" class="img1 vm"/>
                    <span id="uName" class="fz16 ml10 vm"></span>
                </p>
                <p id="hbStusMsg" class="mt20 fz16 tac">再邀请<span class="mar2 cor_4">0位</span>好友即可拆开红包</p>
                <p id="drawHbMsg" class="fz16 tac">最多可得<span class="mar2 cor_4">{{$proj->configs->hongbao->virtual_money}}元</span>现金大红包哦~</p>
                <div class="mt20">
                    <ul class="fl_dib ml-15 package_list" id="packageList">
                        @for($i=0;$i<$proj->configs->hongbao->hb_zl_count;$i++)
                        <li>
                            <div class="re" style="display: none;">
                                <p class="prize_money_pos"></p>
                                <span class="head_portrait_pos"><img src=""/></span>
                            </div>
                            <img src="/vendor/redpacket/images/ico_03.png" class="wp100">
                        </li>
                        @endfor
                    </ul>
                </div>
            </div><!--内容区域-->
            <!--按钮-->
            <div class="mt30 tac dn"><a href="javascript:void(0);" class="btn_04" id="statusBtn">红包已领取</a></div>
            <div class="mt15 tac dn"><a href="javascript:void(0);" class="btn_01" id="resetBtn">我要再次发起红包</a></div>
            <div class="mt30 tac dn"><a href="javascript:void(0);" class="btn_02 btn_kin2" id="drawBtn">马上领取红包</a></div>
            <div class="mt30 tac dn"><a href="javascript:void(0);" class="btn_02 btn_kin2" id="shareBtn">邀请好友帮拆红包</a></div>
            <div class="mt15 tac"><a href="javascript:void(0);" class="btn_01 btn_kin" id="myRedpacket">我的红包</a></div>
        </div><!--pl20 pr20-->
        <div class="content_bg_pos"><img src="/vendor/redpacket/images/banner_03.jpg" class="wp100" onclick="return false;"/></div>
    </div>
    <div class="fixed_hint_sty dn" id="popAlert"><p class="ell"></p></div>
    <div class="bomb_box1 dn">
        <div class="bomb_box_text tac">
            <i class="btn_colse_bomb_box"></i>
            <div class="bomb_fz_bg">
                <p class="mt5 fz18 cor_5">恭喜你！获得</p>
                <p class="mt5 fz16 cor_5"><span class="fz32 fwb mr10" id="redpacketMoney"></span>元</p>
                <p class="mt10 fz16 cor_6">已存入钱包</p>
            </div>
            <p class="tie_h_1"></p>
        </div>
    </div>
    <div class="bomb_box2 dn">
        <div class="bomb_box_text tac">
            <i class="btn_colse_bomb_box"></i>
            <div class="bomb_fz_bg">
                <p class="mt5"><i class="no_prizes_face"></i></p>
                <p class="mt10 fz18 lh24">很遗憾！你来晚了，红包被领完了</p>
            </div>
            <p class="tie_h_1"></p>
        </div>
    </div>
    <div class="bomb_box3 dn">
        <div class="bomb_box_text tac">
            <i class="btn_colse_bomb_box"></i>
            <div class="pa20 bg1 bar_2">
                <p class="fz16 lh24 cor_f">
                    听说要先输入口令才能领取，<br/>识别下方二维码，<br/>回复口令“{{$proj->configs->hongbao->word}}”<br/>就可领取红包哦~
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
    <div class="bomb_box5 dn">
        <div class="bomb_box_text tac">
            <i class="btn_colse_bomb_box"></i>
            <div class="pa20 bg1 bar_2 cor_f tal lh4">
                <div class="fz18 tac" style="line-height: 200px;">
                    还没有红包
                </div>
            </div>
        </div>
    </div>
    <div class="bomb_box6 dn">
        <div class="bomb_box_text_2 tac">
            <i class="btn_colse_bomb_box"></i>
            <div class="pa20 bg1 bar_2">
                <div class="scroll_height_area tal">
                    <p class="fz16 cor_7 tac">共<span class="fz32" id="totalSpan">0</span>元</p>
                    <ul id="hbLog"></ul>
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