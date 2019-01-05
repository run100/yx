<input type="hidden" id="zhuanti" data-path="{{$proj->path}}" data-stime="{{$proj->configs->hongbao->stime}}" data-etime="{{$proj->configs->hongbao->etime}}" data-zlcount="{{$proj->configs->hongbao->hb_zl_count}}">
<input type="hidden" id="page" value="{{$path}}">
<input type="hidden" id="wxShareConfig" data-title="{{$wxshare['title']}}" data-share="{{$wxshare['share']}}" data-link="{{$wxshare['link']}}" data-img="{{$wxshare['img']}}" data-url="{{$wxshare['url']}}">
<script type="text/javascript" src="/vendor/zhuanti/zepto/zepto.min.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/ztjs/bombbox.1.0.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/js-cookie/src/js.cookie.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/mathjs/math.min.js"></script>
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.0.js"></script>
<script type="text/javascript" src="/js/clipboard.min.js"></script>
<script type="text/javascript" src="/vendor/zhuanti/ztjs/common.2.0.js"></script>
<script type="text/javascript" src="/vendor/redpacket/js/init.js?v=2"></script>