<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.0.js"></script>
<script>
    function setJSAPI(){
        //var path = "http://{{Request::server('HTTP_HOST')}}{{ Route::currentRouteName() =='pvote.detail' ? $proj->path.'/detail?id='.$player->ticket_no : $proj->path  }}";
        var wjWeixinShareOptions = {
                title: "我是{{ $player->info_name }},编号{{$players_pre.$player->ticket_no}},我正在参加{{$proj->name }}活动，请大家投我一票",
                desc: "{{$proj->configs->share_desc}}",
                link: "http://{{Request::server('HTTP_HOST')}}{{ Route::currentRouteName() =='pvote.detail' ? $proj->path.'/detail?id='.$player->ticket_no : $proj->path  }}",
                imgUrl: "{{ uploads_url($player->info_img) }}",
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