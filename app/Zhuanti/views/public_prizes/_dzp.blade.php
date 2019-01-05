<div class="re pt5">
    <div class="mt35 fz16 lh18 tac"></div>
    <div class="lottery_box" id="lottery">
        <div class="pan">
            <img class="img_pan" src="/vendor/prizes/images/pan.png" />
            <div class="round">
                @foreach($proj->configs->base_form_prizes as $k=>$v)
                    <span class="rotate"><span class="slyder_prize" data-type="{{$v->type}}">{{$v->name}}</span><br/><img src="/vendor/prizes/images/{{$v->type==2?'ico_smiling_face':'ico_gift'}}.png" class="img{{$k%2==0?2:1}}"/></span>
                @endforeach
            </div>
        </div>
        <div class="pointer_box drawBtn">
            <img class="img_pointer" src="/vendor/prizes/images/pointer2.png"/>
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