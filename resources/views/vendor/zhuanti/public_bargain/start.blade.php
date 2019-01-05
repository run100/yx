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
        <p class="item cor_4 lh16">原价：<span class="cor_5 fz14">￥{{$proj->configs->bargain->goods_price}}</span></p>
        <p class="tar lh16 cor_4">目前剩余：<span class="fz14 cor_5">{{$syCount}}</span>份</p>
      </div>

      <p class="pt10 pb20 pl20 pr10 lh18 cor_4 wenan">{{$proj->configs->bargain->copywriting}}原价<span class="cor_5">{{$proj->configs->bargain->goods_price}}元</span>的<span class="cor_5">{{$proj->configs->bargain->goods_name}}</span>，最先砍到<span class="cor_5">{{$proj->configs->bargain->bargain_price}}元</span>的前<span class="cor_5">{{$proj->configs->bargain->goods_count}}名</span>可{{$proj->id==231?'半价购买 ':'免费获取'}}。</p>
    </div>
  </div>


  <div class="tac"><a href="javascript:void(0);" class="want_bargain_btn">我要砍价</a></div>

  <div class="pl10 pr10">
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
</body>
<input type="hidden" id="project" data-path="{{$proj->path}}">
<input type="hidden" id="wxShareConfig" data-title="{{$proj->configs->share_title}}" data-share="{{$proj->configs->share_desc}}" data-link="{{$proj->path}}" data-img="{{ isset($proj->configs->share_image) ? '/uploads/'.$proj->configs->share_image : '' }}" data-url="{{route('wechat.jssdk_config', [], false)}}">
<input type="hidden" id="page" value="start">
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.0.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/zepto/zepto.min.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/ztjs/common.js?v=1"></script>
<script type="text/javascript" src="/vendor/zhuanti/layer/layer_mobile/layer.js"></script>
<script type="text/javascript" src="/vendor/bargain/js/bargain.js"></script>
</html>