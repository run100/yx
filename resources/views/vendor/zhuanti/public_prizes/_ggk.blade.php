<div class="re pt5">
    <p class="mt35 fz16 lh18 tac cor_6"> </p>
    <p class="mb10 fz16 lh18 tac cor_6"> </p>
    <span class="title_fz_sty btn_zt_com hdgz-head">活动规则</span>
</div>

<div class="par1">
    <div id="lottery_h">
        <div class="fz0 lh-1"><img src="/vendor/prizes/images/ico_top_bg.png" class="wp100" onclick="return false;"/></div>
        <div class="pa10 bgf">
            <div id="lottery" style="width:100%;height:138px;">
                <div class="pa5 hp100 bg2 box_pack" id="ggkContent">

                </div>
            </div>
        </div>
        <div class="fz0 lh-1"><img src="/vendor/prizes/images/ico_bottom_bg.png" class="wp100" onclick="return false;"/></div>
    </div>
    <div class="prizes_btn_pos box_pack"><a href="javascript:void(0);" class="btn_4 drawBtn">开始刮奖</a></div>
</div>

<p class="lottery_num font_color">您{{$proj->configs->draw->limit_day_count>0 ? '今天' : ''}}还有<span class="fwb mar1 cor_4 btn_color" id="drawCount">{{$drawCount}}</span>次抽奖机会</p>
@if($proj->configs->draw->is_zhuli == 'Y')
<div class="par1"><a id="shareBtn" href="javascript:void(0);" class="btn_3 btn_zt_com">邀请好友参与</a></div>
@endif
<div class="mt20 par1"><a id="zjBtn" href="javascript:void(0);" class="btn_3 btn_zt_com">我的中奖纪录</a></div>
@if($proj->configs->draw->player_info_type != 'N')
    <p class="mt25 lh16 tac"><a id="wsInfo" href="javascript:void(0)" class="fz14 tdu cor_4 btn_color">完善领奖信息</a></p>
@endif