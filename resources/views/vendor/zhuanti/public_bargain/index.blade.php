<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
  <link rel="stylesheet" type="text/css" media="screen" href="/vendor/bargain/style/mobile-basic.css" />
  <link rel="stylesheet" type="text/css" href="/vendor/bargain/style/css.css">
  <title>{{$proj->name}}</title>
</head>
<body>

<div class="box">
  <img src="{{isset($proj->configs->bargain->img) ? uploads_url($proj->configs->bargain->img) : '/vendor/bargain/images/banner1.jpg'}}" class="max_wp100">

  <div class="banner">
    <div class="pl10 pr10">
      <div class="con_zhans_area flex_box">
        <p class="item cor_4 lh16">原价:<span class="tdl fz14">￥{{$proj->configs->bargain->goods_price}}</span>
          <span class="ml15">当前价格:<span class="fz14 cor_5">￥{{$currentPrice}}</span></span></p>
        <p class="tar lh16 cor_4">目前剩余：<span class="fz14 cor_5">{{$syCount}}</span>份</p>
      </div>
    </div>
  </div>

  <div class="tac mt10">
    <div class="img_bor1 b1"><img src="{{$playerInfo['info_wx_headimg']}}" class="img_bor1"></div>
    <p class="pt5 pb5 cor_4">{{$playerInfo['info_wx_nickname']}}</p>
    @if($isBargain)
      @if(isset($playerInfo['info_is_exchange']) && isset($playerInfo['info_exchange_time']) && $playerInfo['info_is_exchange']=='Y')
        <p class="pt10 pb20 pl20 pr20 lh16 cor_4 tal wenan" style="text-align: center">恭喜你！成功兑换<span class="cor_5">{{$proj->configs->bargain->goods_name}}</span>一份，感谢参与！</p>
      @else
      <p class="pt10 pb20 pl20 pr20 lh16 cor_4 tal wenan">恭喜你！成功在前<span class="cor_5">{{$proj->configs->bargain->goods_count}}名</span>砍到<span class="cor_5">{{$proj->configs->bargain->bargain_price}}元</span>哦！获得<span class="cor_5">{{$proj->configs->bargain->goods_name}}</span>一份，赶快<span class="cor_5">“完善兑奖信息”</span>来兑换吧！</p>
      @endif
      <p><a id="wxInfoBtn" href="javascript:void(0);" class="help_bargain_btn">完善兑奖信息</a></p>

      @if(isset($playerInfo['info_is_exchange']) && isset($playerInfo['info_exchange_time']) && $playerInfo['info_is_exchange']=='Y')
        <p class="mb10 mt10">
          <a href="javascript:void(0);" class="help_bargain_btn" style="width:142px;height: auto;background-color: #CCCCCC;">
            <span class="lh3">已兑奖成功</span>
          </a>
        </p>
        <p class="cor_5">兑奖时间：{{$playerInfo['info_exchange_time']}}</p>
      @endif

      @if(isset($playerInfo['info_is_validate']) && isset($playerInfo['info_validate_time']))
        @if(!isset($playerInfo['info_is_validate']) || $playerInfo['info_is_validate']==0)
        <p class="mb10 mt10"><a data-perfect="{{ (!isset($playerInfo['info_phone']) || !isset($playerInfo['info_name']))?1:0 }}" href="javascript:void(0);" id="wxValidateBtn" class="help_bargain_btn" style="width:142px">我要兑奖</a></p>
        @else
        <p class="mb10 mt10"><a href="javascript:void(0);" class="help_bargain_btn" style="width:142px;height: auto;background-color: #CCCCCC;"><span class="lh3">已兑奖成功</span><span class="db wwb fz12 fwn lh1">兑奖时间：{{$playerInfo['info_validate_time']}}</span></a></p>
        @endif
      @endif

      @if($exchange_status == 1 && isset($playerInfo['info_is_exchange']) && isset($playerInfo['info_exchange_time']) && $playerInfo['info_is_exchange']=='N')
            <p class="mb10 mt10"><a data-prize-name="{{$exchange_prize_name}}" data-perfect="{{ $perfect }}" href="javascript:void(0);" id="wxExchangeBtn" class="help_bargain_btn" style="width:142px">我要兑奖</a></p>
      @endif

    @else
      <p class="pt10 pb20 pl20 pr20 lh16 cor_4 tal wenan">你正在参与<span class="cor_5">“{{$proj->name}}”</span>活动，赶快邀请好友为你砍价吧，最先砍到<span class="cor_5">{{$proj->configs->bargain->bargain_price}}元</span>的前<span class="cor_5">{{$proj->configs->bargain->goods_count}}名</span>可获得<span class="cor_5">{{$proj->configs->bargain->goods_name}}</span>一份。</p>
      <a id="invitationBtn" href="javascript:void(0);" class="help_bargain_btn">邀请好友砍价</a>
    @endif
  </div>

  <div class="pl10 pr10">

    <div class="single_area cor_1 mt10">
      <h3 class="mb5">砍价记录</h3>
      <div id="zlTable" style="padding-bottom: 15px;">
        @forelse($zhulis as $v)
          @php
            $v = wj_json_decode($v);
          @endphp
          <p class="pt10 mar2 lh14 cor_1 tal"><span class="cor_2 fr">{{$v['date']}}</span><span class="cor_5">{{$v['name']}}</span> 砍掉了<span class="cor_5">￥{{$v['price']}}</span></p>
        @empty
          <p class="pt25 pb20 cor_1 lh14">你还没有好友帮你砍价哦！</p>
        @endforelse
      </div>
      @if(count($zhulis) == 10)
        <p class="mt10 fz14 tac"><a id="zlMoreBtn" href="javascript:void(0)" class="tdu cor_4" data-page="1">加载更多&gt;</a></p>
      @endif
    </div>

    <!--活动规则介绍-->
    <div class="single_area cor_1 mt10">
      <h3>活动规则</h3>
      {!! htmlspecialchars_decode($proj->rules) !!}
    </div>


    <div class="single_area cor_1 mt10">
      <h3>砍价排名</h3>

      <div class="mt5 pb15">
        <table class="rank_table" border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
          <thead>
          <tr class="bg_2">
            <td style="width: 30%">排名</td>
            <td style="width: 45%">昵称</td>
            <td style="width: 25%">价格</td>
          </tr>
          </thead>
          <tbody id="winsTbody">

          </tbody>
        </table>
      </div>
    </div>

  </div>

  <div id="pageBox" class="tac mt10 mb20 dn">
    <div class="page cor_7 bg_3 di pa10">
      <span data-type="1" class="cor_8 page_item">首页</span><span class="mar2">|</span>
      <span data-type="2" class="cor_8 page_item">上一页</span><span class="mar2">|</span>
      <span class="cor_7">第</span>
      <span id="currentPage">1</span>
      <span class="cor_7">页</span><span class="mar2">|</span>
      <span data-type="3" class="cor_8 page_item">下一页</span><span class="mar2">|</span>
      <span data-type="4" class="cor_8 page_item">末页</span>
    </div>
  </div>
</div>

<!--弹窗--砍价-->
<div class="bombBoxWrap" style="display: none;">
  <div class="bombBox">
    <div class="boxContent">
      <p class="mt10 fwb fz16 cor_5">完善兑奖信息</p>
      <form id="commitInfoForm" action="{{$proj->path}}/commit_info" method="post">
      <div>
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
            <p class="mt15"><input type="text" name="info_{{$form->field}}" value="{{isset($playerInfo['info_'.$form->field]) ? $playerInfo['info_'.$form->field]:''}}" king-label="{{$form->name}}" king-filter="{{implode('|',$validate)}}" class="ipt_1" placeholder="{{$form->name}}" /></p>
          @endif
        @endforeach
      </div>
      <p class="mt10 fz12 cor_5" ><span class="boxCloseBtn"><img src="/vendor/bargain/images/ico_close.png" class="max_wp100"/></span></p>
      <p id="tipInfo" class="mt10 fz12 cor_5"></p>
      <div class="pt10"><a id="commitBtn" href="javascript:void(0);" class="tac_btn">提交</a></div>
      </form>
    </div>
  </div>
  <div class="bombBoxBg"></div>
</div>

<!--弹窗--我要兑换-->
<div class="bombBoxWrap" style="display: none;">
  <div class="bombBox">
    <div class="boxContent">
      <form id="commitValidateForm" action="{{$proj->path}}/validate" method="post">
        <div class="fz14 tac cor_9 lh4">
          <p class="">恭喜您获得11月11日当天</p>
          <p class="cor_5">芜湖方特光棍节夜场女性专享免费门票两张</p>
          <p>(仅限女性使用)</p>
        </div>
        <div class="fz14 mt10 tal lh4">
          <p class="">领取时间（过时不候）</p>
          <p class="fwb">2018年11月9日-11月11日09:30-16:00</p>
          <p class="">领取地点</p>
          <p class="fwb">芜湖方特梦幻王国游客中心</p>
        </div>
        <div class="fz14 tac lh4 mt15">
          <p class="fwb">由芜湖方特旅游区工作人员</p>
          <p class="fwb">输入核销码领取免费门票</p>
          <p class="mt15"><input type="text" name="code" king-label="核销码" king-filter="required|code"  class="ipt_1 tac" placeholder="输入核销码" /></p>
        </div>
        <div class="pt10"><a href="javascript:void(0);" id="commitValidateBtn" class="help_bargain_btn">确认领取</a></div>
      </form>
      <p id="msg" class="fz14 cor_5 mt10"></p>
      <div id="validateTime" style="display: none">
        <p class="mt10 fwb fz14 cor_5">竞奖未开始</p>
        <p class="cor_9 mt5">兑奖时间为：</p>
        <p class="cor_9 mt5">11月9日-11月11日(9:30-16:00)</p>
      </div>
    </div>
    <span class="boxCloseBtn"><img src="/vendor/bargain/images/ico_close.png" class="max_wp100"/></span>

  </div>
  <div class="bombBoxBg"></div>
</div>

<div class="bombBoxWrap" style="display: none;">
  <div class="bombBox">
    <div class="boxContent" >
      <form id="commitExchangeForm" action="{{$proj->path}}/exchange" method="post">
        <div class="fz14 tac cor_9 lh4">
          <p class="mt30 fwb fz14 cor_5">恭喜您，获得</p>
          <input type="hidden" name="code" value="{{$exchange_code}}" king-label="核销码" king-filter="required|code" >
          <p class="cor_1 mt20 fz12" id="exchangePrize">{{$exchange_prize_name}}</p>
        </div>
        <div class="pt30 mb10"><a href="javascript:void(0);" id="commitExchangeBtn" class="help_bargain_btn">确认领取</a></div>
      </form>
      <p class="msg" class="fz14 cor_5 mt10"></p>
    </div>
    <span class="boxCloseBtn"><img src="/vendor/bargain/images/ico_close.png" class="max_wp100"/></span>

  </div>
  <div class="bombBoxBg"></div>
</div>

<div class="share dn">
  <div class="sharePic">
    <img src="/vendor/bargain/images/ico_share_tac.png" class="max_wp100">
  </div>
  <div class="shareBg"></div>
</div>
<input type="hidden" id="project" data-path="{{$proj->path}}" data-player="{{$playerInfo['ticket_no']}}">
<input type="hidden" id="page" value="index">
<input type="hidden" id="wxShareConfig" data-title="{{$proj->configs->share_title}}" data-share="{{$proj->configs->share_desc}}" data-link="{{$proj->path}}/{{$playerInfo['md5key']}}" data-img="{{ isset($proj->configs->share_image) ? '/uploads/'.$proj->configs->share_image : '' }}" data-url="{{route('wechat.jssdk_config', [], false)}}">
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.0.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/zepto/zepto.min.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/ztjs/common.js?v=2"></script>
<script type="text/javascript" src="/vendor/bargain/js/bargain.js?v=8"></script>
</body>
</html>