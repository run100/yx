<div class="re pt5">
    <div class="mt35 fz16 lh18 tac"></div>
    <div class="pl10 pr10">
        <div class="re" id="lottery">
            <ul class="egg_list_sty">
                <li class="egg_location egg_location_00 eggBtn"><img src="/vendor/prizes/images/egg_00.png" class="wp100 init"></li>
                <li class="egg_location egg_location_01 eggBtn"><img src="/vendor/prizes/images/egg_00.png" class="wp100 init"></li>
                <li class="egg_location egg_location_02 eggBtn"><img src="/vendor/prizes/images/egg_00.png" class="wp100 init"></li>
                <li class="egg_location egg_location_03 eggBtn"><img src="/vendor/prizes/images/egg_00.png" class="wp100 init"></li>
                <li class="egg_location egg_location_04 eggBtn"><img src="/vendor/prizes/images/egg_00.png" class="wp100 init"></li>
            </ul>
            <span class="hammer_sty_pos"><i class="ico_hammer"></i></span>
            <img src="/vendor/prizes/images/ico_dish_00.png" class="wp100 dish_img"/>
            <div class="btn_show drawBtn"><a href="javascript:void(0);" class="btn_sty">点击砸蛋</a></div>
        </div>
    </div>
    <span class="title_fz_sty btn_zt_com hdgz-head">活动规则</span>
    <p class="lottery_num font_color">您{{$proj->configs->draw->limit_day_count>0 ? '今天' : ''}}还有<span class="fwb mar1 cor_4 btn_color" id="drawCount">{{$drawCount}}</span>次抽奖机会</p>
    @if($proj->configs->draw->is_zhuli == 'Y')
    <div class="par1"><a id="shareBtn" href="javascript:void(0);" class="btn_3 btn_zt_com">邀请好友参与</a></div>
    @endif
    <div class="mt20 par1"><a id="zjBtn" href="javascript:void(0);" class="btn_3 btn_zt_com">我的中奖纪录</a></div>
    @if($proj->configs->draw->player_info_type != 'N')
        <p class="mt25 lh16 tac"><a id="wsInfo" href="javascript:void(0)" class="fz14 tdu cor_4 btn_color">完善领奖信息</a></p>
    @endif
</div>