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
    @if($status==2)
      <p class="pt10 pb20 pl20 pr20 lh16 cor_4 tal wenan">恭喜你成功为好友<span class="cor_5">{{$playerInfo['info_wx_nickname']}}</span>砍掉<span class="cor_5">{{$price}}元</span>！好友离获得奖品又近了一步，你也快来参加吧！</p>
      <a href="{{$proj->path}}" class="help_bargain_btn">我也要参加</a>
    @elseif($status == 1)
      <p class="pt10 pb20 pl20 pr20 lh16 cor_4 tal wenan">你的好友<span class="cor_5">{{$playerInfo['info_wx_nickname']}}</span>正在参与<span class="cor_5">“{{$proj->name}}“</span>活动，
        最先砍到<span class="cor_5">{{$proj->configs->bargain->bargain_price}}元</span>的前<span class="cor_5">{{$proj->configs->bargain->goods_count}}名</span>可获得<span class="cor_5">{{$proj->configs->bargain->goods_name}}</span>一份，赶快帮TA砍价吧！</p>
      <a id="zhuliBtn" data-content="{{$proj->configs->bargain->prefix}}{{$playerInfo['ticket_no']}}" href="javascript:void(0);" class="help_bargain_btn">帮TA砍价</a>
    @else
      <p class="pt10 pb20 pl20 pr20 lh16 cor_4 tal wenan">你的好友<span class="cor_5">{{$playerInfo['info_wx_nickname']}}</span>已成功在前<span class="cor_5">{{$proj->configs->bargain->goods_count}}名</span>砍到0元，获得<span class="cor_5">{{$proj->configs->bargain->goods_name}}</span>一份，你也快来参加吧！</p>
        <a href="{{$proj->path}}" class="help_bargain_btn">我也要参加</a>
    @endif
  </div>

  <div class="pl10 pr10">

    <!--砍价纪录-->
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


    <!--砍价排名-->
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
<div class="bombBoxWrap dn">
  <div class="bombBox">
    <div class="boxContent">
      <p class="mt10 fwb fz16 cor_5">温馨提示</p>
      <p class="mt10 lh16 cor_1">
        @if(isset($proj->configs->bargain->guidetext) && !empty($proj->configs->bargain->guidetext))
          {!! str_replace('PLAYER', '<span class="cor_5">“'.$proj->configs->bargain->prefix.$playerInfo['ticket_no'].'”</span>', $proj->configs->bargain->guidetext) !!}
        @else
        系统已自动复制编号<span class="cor_5">“{{$proj->configs->bargain->prefix}}{{$playerInfo['ticket_no']}}”</span>长按识别下方二维码，在公众号底部输入框长按并粘贴-回复，即可砍价
        @endif
      </p>
      <div class="mt10" style="max-height: 200px;overflow-y:auto;">
        @if(isset($proj->configs->bargain->wechat_img1))
        <p class="mb10"><img src="{{uploads_url($proj->configs->bargain->wechat_img1)}}" class="wp60"></p>
        @endif
        @if(isset($proj->configs->bargain->wechat_img2))
        <p class="mb10"><img src="{{uploads_url($proj->configs->bargain->wechat_img2)}}" class="wp60"></p>
        @endif
      </div>
    </div>
    <span class="boxCloseBtn"><img src="/vendor/bargain/images/ico_close.png" class="max_wp100"/></span>
  </div>
  <div class="bombBoxBg"></div>
</div>
<input type="hidden" id="project" data-path="{{$proj->path}}" data-player="{{$playerInfo['ticket_no']}}">
<input type="hidden" id="wxShareConfig" data-title="{{$proj->configs->share_title}}" data-share="{{$proj->configs->share_desc}}" data-link="{{$proj->path}}/{{$playerInfo['md5key']}}" data-img="{{ isset($proj->configs->share_image) ? '/uploads/'.$proj->configs->share_image : '' }}" data-url="{{route('wechat.jssdk_config', [], false)}}">
<input type="hidden" id="page" value="player">
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.0.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/zepto/zepto.min.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/ztjs/common.js?v=1"></script>
<script type="text/javascript" src="/js/clipboard.min.js"></script>
<script type="text/javascript" src="/vendor/bargain/js/bargain.js"></script>
</body>
</html>