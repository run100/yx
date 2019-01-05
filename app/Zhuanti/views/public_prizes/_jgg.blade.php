<div class="re pt5">
    <div class="mt35 fz16 lh18 tac"></div>
    <div class="outside_area">
        <div class="table_area" id="lottery">
            <table class="reward_table">
                <colgroup>
                    <col  style="width:33.33%;"/>
                    <col  style="width:33.33%;"/>
                    <col  style="width:33.33%;"/>
                </colgroup>
                <tr>
                    <td>
                        <div class="content_area">
                            <div class="vertical_align award award-0" data-type="{{$proj->configs->base_form_prizes[0]->type}}"><img src="/vendor/prizes/images/{{$proj->configs->base_form_prizes[0]->type==2?'ico_smiling_face':'ico_prizes_00'}}.png"><p class="fz_sty">{{$proj->configs->base_form_prizes[0]->name}}</p></div>
                        </div>
                    </td>
                    <td>
                        <div class="content_area">
                            <div class="vertical_align award award-1" data-type="{{$proj->configs->base_form_prizes[1]->type}}"><img src="/vendor/prizes/images/{{$proj->configs->base_form_prizes[1]->type==2?'ico_smiling_face':'ico_prizes_00'}}.png"><p class="fz_sty">{{$proj->configs->base_form_prizes[1]->name}}</p></div>
                        </div>
                    </td>
                    <td>
                        <div class="content_area">
                            <div class="vertical_align award award-2" data-type="{{$proj->configs->base_form_prizes[2]->type}}"><img src="/vendor/prizes/images/{{$proj->configs->base_form_prizes[2]->type==2?'ico_smiling_face':'ico_prizes_00'}}.png"><p class="fz_sty">{{$proj->configs->base_form_prizes[2]->name}}</p></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="content_area">
                            <div class="vertical_align award award-7" data-type="{{$proj->configs->base_form_prizes[7]->type}}"><img src="/vendor/prizes/images/{{$proj->configs->base_form_prizes[7]->type==2?'ico_smiling_face':'ico_prizes_00'}}.png"><p class="fz_sty">{{$proj->configs->base_form_prizes[7]->name}}</p></div>
                        </div>
                    </td>
                    <td>
                        <div class="content_area bg1">
                            <div class="vertical_align rotate_btn drawBtn"></div>
                        </div>
                    </td>
                    <td>
                        <div class="content_area">
                            <div class="vertical_align award award-3" data-type="{{$proj->configs->base_form_prizes[3]->type}}"><img src="/vendor/prizes/images/{{$proj->configs->base_form_prizes[3]->type==2?'ico_smiling_face':'ico_prizes_00'}}.png"><p class="fz_sty">{{$proj->configs->base_form_prizes[3]->name}}</p></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="content_area">
                            <div class="vertical_align  award award-6" data-type="{{$proj->configs->base_form_prizes[6]->type}}"><img src="/vendor/prizes/images/{{$proj->configs->base_form_prizes[6]->type==2?'ico_smiling_face':'ico_prizes_00'}}.png"><p class="fz_sty">{{$proj->configs->base_form_prizes[6]->name}}</p></div>
                        </div>
                    </td>
                    <td>
                        <div class="content_area">
                            <div class="vertical_align  award award-5" data-type="{{$proj->configs->base_form_prizes[5]->type}}"><img src="/vendor/prizes/images/{{$proj->configs->base_form_prizes[5]->type==2?'ico_smiling_face':'ico_prizes_00'}}.png"><p class="fz_sty">{{$proj->configs->base_form_prizes[5]->name}}</p></div>
                        </div>
                    </td>
                    <td>
                        <div class="content_area">
                            <div class="vertical_align  award award-4" data-type="{{$proj->configs->base_form_prizes[4]->type}}"><img src="/vendor/prizes/images/{{$proj->configs->base_form_prizes[4]->type==2?'ico_smiling_face':'ico_prizes_00'}}.png"><p class="fz_sty">{{$proj->configs->base_form_prizes[4]->name}}</p></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <img src="/vendor/prizes/images/squared_up_prizes.png" class="wp100" />
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