<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{$proj->name }}</title>
    <meta name="description" content="{{$proj->name }}"/>
    <meta name="keywords" content="{{$proj->name }}"/>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
    @if($proj->id==239)<link rel="stylesheet" href="/vendor/prizes/style/style.css?v=1" type="text/css"/>@endif
    <link rel="stylesheet" type="text/css" media="screen" href="http://365jia.cn/css/m/mobile-basic.css" />
    <link rel="stylesheet" href="/vendor/prizes/style/css.css" type="text/css"/>
    <style>
        .bg1{
            background: {{isset($proj->configs->draw->bg_color)? $proj->configs->draw->bg_color : '#FFCA00'}}
        }
        .btn_zt_com{
            background-color: {{isset($proj->configs->draw->btn_color)? $proj->configs->draw->btn_color : '#FF4F1E'}};
            color: {{isset($proj->configs->draw->btn_font_color)? $proj->configs->draw->btn_font_color : '#fff'}}
        }
        .btn_color{
            color: {{isset($proj->configs->draw->btn_color)? $proj->configs->draw->btn_color : '#FF4F1E'}}
        }
        .font_color{
            color: {{isset($proj->configs->draw->bg_font_color)? $proj->configs->draw->bg_font_color : '#793C31'}}
        }
    </style>
</head>
<body>
<div class="box bg1">
    <div><img src="{{isset($proj->configs->draw->img) ? uploads_url($proj->configs->draw->img) : '/vendor/prizes/images/banner_00.jpg'}}" class="wp100"></div>

    @include('zhuanti::public_prizes._'.$proj->configs->draw->draw_type)

    @if($proj->configs->draw->is_zhuli == 'Y')
    <div class="mt45">
        <div><img src="/vendor/prizes/images/ico_title_02.png" class="wp100"></div>
        <div class="mt5 pa15">
            <div class="edit_msg_area">
                <table class="rank_table_02" id="zlTable">
                    <colgroup>
                        <col style="width: 100%" />
                    </colgroup>
                    <tbody>
                    @forelse($zlRecords as $zl)
                        @php
                        $zl = wj_json_decode($zl);
                        @endphp
                    <tr>
                        <td><span>好友</span><span class="cor_4 mar1">{{$zl['name']}}</span><span>参与了抽奖</span></td>
                    </tr>
                    @empty
                    <tr><td style="text-align: center;color: #AAAAAA;">您还没有邀请记录</td></tr>
                    @endforelse
                    </tbody>
                </table>
                @if(count($zlRecords) == 10)
                <p class="mt10 fz14 tac"><a id="zlMoreBtn" href="javascript:void(0)" class="tdu cor_4" data-page="1">加载更多&gt;</a></p>
                @endif
            </div>
        </div>
    </div>
    @endif
    <div class="mt30">
        <div><img src="/vendor/prizes/images/ico_title_00.png" class="wp100"></div>
        <div class="mt5 pa15">
            <div class="edit_msg_area">
                <table class="rank_table">
                    <thead>
                    <tr>
                        <th><span>昵称</span></th>
                        <th><span>奖品</span></th>
                    </tr>
                    </thead>
                    <tbody id="winsTbody">

                    </tbody>
                </table>
                <div class="pt15 page_box dn" id="pageBox">
                    <a class="page_item" href="javascript:void(0)" data-type="1">首页</a>
                    <a class="page_item" href="javascript:void(0)" data-type="2">上一页</a>
                    <a class="page_item" href="javascript:void(0)" data-type="0" id="currentPage">1</a>
                    <a class="page_item" href="javascript:void(0)" data-type="3">下一页</a>
                    <a class="page_item" href="javascript:void(0)" data-type="4">末页</a>
                </div>
            </div>
        </div>
    </div>
    <p class="fz14 tal cor_6 font_color" style="margin-left: 15px">*中奖名单按照最新中奖排序</p>


    <div class="share dn">
        <div class="sharePic">
            <div><img src="/vendor/prizes/images/ico_tac_share.png" class="wp100"></div>
            <div class="mt35 pt10 tac"><i class="ico_ok_sty"></i></div>
        </div>
        <div class="shareBg"></div>
    </div>
    @if($proj->id==239)
    <div class="bottom_nav">
        <div class="flex_box">
            <a href="{{$proj->path}}" class="item">
                <i class="bottom_nav_1"></i>
                <span>抽奖页</span>
            </a>
            <a href="{{$proj->path}}/rule" class="item">
                <i class="bottom_nav_2"></i>
                <span>比赛明细</span>
            </a>
        </div>
    </div>
    @endif
</div>
<input type="hidden" id="project" data-tips="{{$proj->configs->draw->limit_day_count>0 ? 'day' : 'all'}}" data-path="{{$proj->path}}" data-ws="{{$proj->configs->draw->player_info_type}}" data-type="{{$proj->configs->draw->draw_type}}" data-id="{{$proj->id}}">
<input type="hidden" id="zjCount" value="{{count($zjRecords)}}">
<input type="hidden" id="wxShareConfig" data-title="{{$proj->configs->share_title}}" data-share="{{$proj->configs->share_desc}}" data-link="{{$proj->configs->draw->is_attention == 'Y' ? $proj->path.'/'.$playerKey : ($proj->configs->draw->is_zhuli == 'Y' ? $proj->path.'?friendid='.$playerKey : $proj->path)}}" data-img="{{ isset($proj->configs->share_image) ? '/uploads/'.$proj->configs->share_image : '' }}" data-url="{{route('wechat.jssdk_config', [], false)}}">
<input type="hidden" id="drawInfo" data-stime="{{$proj->configs->draw->stime}}" data-etime="{{$proj->configs->draw->etime}}">
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.0.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/zepto/zepto.min.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/ztjs/common.js?v=1"></script>
<script type="text/javascript" src="/vendor/prizes/js/prizes_common.1.0.js"></script>
<script type="text/javascript" src="/vendor/prizes/js/prizes.1.0.js?v=3"></script>
<script type="text/template" id="zjTemp">
        <div class="bomb_box_text re">
            <i class="btn_colse_bomb_box btn_close"></i>
            <div class="tac"><i class="ico_smiling_face"></i></div>
            <p class="mt10 fz14 lh16 tac"><span class="di cor_6 mr10">获得</span><span class="several_text cor_4">PRIZES_NAME</span></p>
            @if($proj->configs->draw->player_info_type != 'N')
            <p class="mt10 fz14 lh16 tac">请完善您的领奖信息</p>
            @endif
            <div class="mt10 tac"><a href="javascript:void(0);" class="btn_1 btn_close">再来一次</a></div>
        </div>
</script>
<script type="text/template" id="hdgzTemp">
    <div class="bomb_box_text_3 re">
        <i class="btn_colse_bomb_box_01 btn_close"></i>
        <h1 class="title_area_sty">活动规则</h1>
        <div class="tac_scroll_area">
            <div class="mt20 tal">
                {!!htmlspecialchars_decode($proj->rules)!!}
            </div>
        </div>
    </div>
</script>
@if($proj->configs->draw->player_info_type != 'N')
<script type="text/template" id="wsTemp">
    <form method="POST" id="wsForm" action="{{ $proj->path }}/wsinfo">
        <div class="bomb_box_text_3 re">
            <i class="btn_colse_bomb_box_01 btn_close"></i>
            <h1 class="title_area_sty">完善领奖信息</h1>
            <div class="mt10">
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
                        @switch($form->type)
                            @case('address')
                            <div class="pt15"><textarea name="info_{{$form->field}}" king-label="{{$form->name}}" king-filter="{{implode('|',$validate)}}" class="textarea_01" placeholder="地址">{{isset($player['info_'.$form->field]) ? $player['info_'.$form->field] : ''}}</textarea></div>
                            @break
                            @default
                            <div class="pt15"><input type="text" class="ipt_01" placeholder="{{$form->name}}" name="info_{{$form->field}}" maxlength="18" king-label="{{$form->name}}" king-filter="{{implode('|',$validate)}}" value="{{isset($player['info_'.$form->field]) ? $player['info_'.$form->field] : ''}}"></div>
                        @endswitch
                    @endif
                @endforeach
                <p id="errorMsg" class="pt10 fz14 lh16 cor_6">请您如实填写真实信息</p>
                <div class="pt30"><a id="commitBtn" href="javascript:void(0);" class="btn_1">提交信息</a></div>
                <p class="pt20 fz14 lh16 cor_2">此信息仅用于领奖，请放心填写</p>
            </div>
        </div>
    </form>
</script>
@endif
<script type="text/template" id="notZjRecords">
    <div class="bomb_box_text_3 re">
        <i class="btn_colse_bomb_box_01 btn_close"></i>
        <h1 class="title_area_sty">我的中奖纪录</h1>
        <div class="tac_scroll_area">
            <div class="mt35 pt30"><i class="mt25 ico_sad_face_01"></i></div>
            <p class="mt25 fz14 lh16 cor_2">您还没有中奖纪录</p>
        </div>
    </div>
</script>
<div class="dn" id="zjRecordTemp">
    <div class="bomb_box_text_3 re" style="padding: 0 0 30px;">
        <i class="btn_colse_bomb_box_01 btn_close"></i>
        <h1 class="title_area_sty">我的中奖纪录</h1>
        <div class="tac_scroll_area">
            <div class="mt20 tal">
                <table class="rank_table_01" id="zjRecordTable">
                    <colgroup>
                        <col style="width: 18%" />
                        <col style="width: 40%" />
                        <col style="width: 42%" />
                    </colgroup>
                    @foreach($zjRecords as $v)
                        @php
                        $v = wj_json_decode($v);
                        @endphp
                    <tr>
                        <td><span class="pl10">获得</span></td>
                        <td><div>{{$v['prize']}}</div></td>
                        <td><span class="cor_2">{{$v['date']}}</span></td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>