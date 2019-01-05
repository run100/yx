<div class="re pt5">
    <div class="mt35 fz16 lh18 tac"></div>
    <div class="card_area pl20 pr20">
        <ul class="fl_1 cf" id="lottery">
            @foreach($proj->configs->base_form_prizes as $k=>$v)
                <li class="item_backcard drawBtn" data-type="{{$v->type}}" data-name="{{$v->name}}" data-index="{{$k+1}}">
                    <div class="img_par frontface mousein flip">
                        <img src="/vendor/prizes/images/frontface_card.png" class="top_img open_img"/>
                        <div class="card_in box_pack">
                            <p><img src="/vendor/prizes/images/ico_bf_1.png" class="ico_logo_card"/></p>
                            <p class="mt5 fz14 lh16"><span class="prize_fz">{{$v->name}}</span></p>
                        </div>
                    </div>
                    <div class="backface">
                        <div class="img_par mousein flip">
                            <img src="/vendor/prizes/images/backface_card.png" class="top_img"/>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        <p class="tac"><a href="javascript:void(0);" class="btn_backface"></a></p>
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