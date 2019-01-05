<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.0.js"></script>
<script>
    function setJSAPI(){
        var wjWeixinShareOptions = {
                title: "{{$proj->configs->share_title}}",
                desc: "{{$proj->configs->share_desc}}",
                link: "http://{{Request::server('HTTP_HOST')}}{{ Route::currentRouteName() =='pvote.detail' ? $proj->path.'/detail?id='.$player->ticket_no : $proj->path  }}",
                imgUrl: "{{ isset($proj->configs->share_image) ? uploads_url( $proj->configs->share_image ) : '' }}",
                success: function() {
                    //分享成功,可加统计逻辑
                },
                cancel: function() {
                    //分享被取消
                }
            }
        ;

        $.getJSON({!! json_encode(route('wechat.jssdk_config', [], false)) !!}, function (res) {
            if (res.code !== 0) {
                return;
            }
            wx.config(res.data);
            wx.ready(function () {
                if (wjWeixinShareOptions !== false) {
                    wx.onMenuShareTimeline(wjWeixinShareOptions);
                    wx.onMenuShareQZone(wjWeixinShareOptions);
                    wx.onMenuShareQQ(wjWeixinShareOptions);
                    wx.onMenuShareWeibo(wjWeixinShareOptions);
                    wx.onMenuShareAppMessage(wjWeixinShareOptions);
                }

            });
        });
    }
    setJSAPI();
</script>