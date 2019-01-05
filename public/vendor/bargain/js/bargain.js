$(function(){
    var page = $('#page').val();
    setJSAPI();
    //获取排名
    var fenye = new Fenye($('#project').data('path')+'/rakings');
    fenye.getData(1);
    $('.page_item').click(function(){
        var type = parseInt($(this).data('type'));
        if(type>0) {
            fenye.getData(type);
        }
    });
    switch(page){
        case 'start':
            var projectPath = $('#project').data('path');
            var startObj = {url:projectPath+'/reg'};
            $('.want_bargain_btn').click(function(){
                kingAjax(startObj, function(){
                    location.href = projectPath;
                },function(res){
                    layer.open({skin:'msg', content:res.msg, time:2})
                });
            });
            break;
        case 'index':
            var browser={
                versions:function(){
                    var u = navigator.userAgent, app = navigator.appVersion;
                    return {
                        trident: u.indexOf('Trident') > -1, //IE内核
                        presto: u.indexOf('Presto') > -1, //opera内核
                        webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
                        gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1,//火狐内核
                        mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
                        ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
                        android: u.indexOf('Android') > -1 || u.indexOf('Adr') > -1, //android终端
                        iPhone: u.indexOf('iPhone') > -1 , //是否为iPhone或者QQHD浏览器
                        iPad: u.indexOf('iPad') > -1, //是否iPad
                        webApp: u.indexOf('Safari') == -1, //是否web应该程序，没有头部与底部
                        weixin: u.indexOf('MicroMessenger') > -1, //是否微信 （2015-01-22新增）
                        qq: u.match(/\sQQ/i) == " qq" //是否QQ
                    };
                }(),
                language:(navigator.browserLanguage || navigator.language).toLowerCase()
            };
            //判断是否ios
            if(browser.versions.ios){
                $("#wxInfoBtn").click(function() {
                    $(".bombBoxWrap").eq(0).show();
                    $('.bombBoxWrap').eq(0).css({height:$(window).height(),'position':'absolute'});
                    $('.bombBox').eq(0).css({'top':'180px'});
                    $('.box').css({height:$(window).height(),'overflow' : 'hidden'});
                });
                $('#wxValidateBtn').click(function () {
                    var _data = $(this).data();
                    if(_data.perfect==1){
                        return alert('请先完善兑奖信息');
                    }
                    $(".bombBoxWrap").eq(1).show();
                    $('.bombBoxWrap').eq(1).css({height:$(window).height(),'position':'absolute'});
                    $('.bombBox').eq(1).css({'top':'30px'});
                    $('.box').css({height:$(window).height(),'overflow' : 'hidden'});
                });

                $('#wxExchangeBtn').click(function () {
                    var _data = $(this).data();
                    var _prizeName = '';
                    if(_data.perfect==1){
                        return alert('请先完善兑奖信息');
                    }
                    if(_data.prizeName!=undefined && _data.prizeName!=''){
                        _prizeName = _data.prizeName;
                    }
                    $(".bombBoxWrap").eq(2).find('#exchangePrize').text(_prizeName);
                    $(".bombBoxWrap").eq(2).show();
                    $('.bombBoxWrap').eq(2).css({height:$(window).height(),'position':'absolute'});
                    $('.bombBox').eq(2).css({'top':'180px'});
                    $('.box').css({height:$(window).height(),'overflow' : 'hidden'});
                });


                $(".bombBoxWrap").find(".boxCloseBtn").click(function() {
                    $(this).parents(".bombBoxWrap").hide();
                    $('.bombBoxWrap').css({height:''});
                    $('.box').css({height:'','overflow' : ''});
                });
                // $(".bombBoxWrap").find(".boxCloseBtn").click(function() {
                //     $(this).parents(".bombBoxWrap").hide();
                //     $('.bombBoxWrap').css({height:''});
                //     $('.box').css({height:'','overflow' : ''});
                //     $('#tipInfo').html('');
                // });
            }else { //安卓等其他
                $("#wxInfoBtn").click(function() {
                    $(".bombBoxWrap").eq(0).show();
                });
                $('#wxValidateBtn').click(function () {
                    var _data = $(this).data();
                    if(_data.perfect==1){
                        return alert('请先完善兑奖信息');
                    }
                    $(".bombBoxWrap").eq(1).show();
                });

                $('#wxExchangeBtn').click(function () {
                    var _data = $(this).data();
                    var _prizeName = '';
                    if(_data.perfect==1){
                        return alert('请先完善兑奖信息');
                    }
                    if(_data.prizeName!=undefined && _data.prizeName!=''){
                        _prizeName = _data.prizeName;
                    }
                    $(".bombBoxWrap").eq(2).find('#exchangePrize').text(_prizeName);
                    $(".bombBoxWrap").eq(2).show();
                });

                $(".bombBoxWrap").find(".boxCloseBtn").click(function() {
                    $(this).parents(".bombBoxWrap").hide();
                });
                // $(".bombBoxWrap").find(".boxCloseBtn").click(function() {
                //     $(this).parents(".bombBoxWrap").hide();
                //     $('#tipInfo').html('');
                // });
            }
            // 分享
            $('#invitationBtn').click(function(){$('.share').show();});
            $(".share").click(function() {$(".share").hide();});
            //获取更多助力记录
            var isGetZhuli = false;
            $('#zlMoreBtn').click(function(){
                if(isGetZhuli) {
                    return;
                }
                isGetZhuli = true;
                var zhuliPage = parseInt($('#zlMoreBtn').data('page'));
                $.get($('#project').data('path')+'/zhulis', {page:zhuliPage,player:$('#project').data('player')}, function(res){
                    $('#zlMoreBtn').data('page', ++zhuliPage);
                    if(res.data.length > 0) {
                        var body = '';
                        for(var i in res.data){
                            var f = eval('('+res.data[i]+')');
                            body += '<p class="pt10 mar2 lh14 cor_1 tal"><span class="cor_2 fr">'+f.date+'</span><span class="cor_5">'+f.name+'</span> 砍掉了<span class="cor_5">￥'+f.price+'</span></p>';
                        }
                        $('#zlTable').append(body);
                    }
                    if(res.data.length < 10) {
                        $('#zlMoreBtn').hide();
                    }
                    isGetZhuli = false;
                })
            });
            //完善信息
            $('#commitBtn').click(function(){
                $('#tipInfo').html('');
                $.kingForm.create('commitInfoForm',function (res) {
                    $('#tipInfo').html(res.msg);
                    setTimeout(function(){
                        $('#tipInfo').html('');
                        if(browser.versions.ios){
                            $(".bombBoxWrap").hide();
                            $('.bombBoxWrap').css({height:''});
                            $('.box').css({height:'','overflow' : ''});
                        }else{
                            $(".bombBoxWrap").hide();
                        }
                        location.reload();
                    }, 2000);
                }, function(){
                    $('#tipInfo').html(res.msg);
                }, undefined, undefined, function(error){
                    $('#tipInfo').html(error);
                }).ajaxCommit();
            });

            $('#commitValidateBtn').click(function(){
                $('#msg').html('');
                $.kingForm.create('commitValidateForm',function (res) {
                    $('#msg').html(res.msg);
                    setTimeout(function(){
                        $('#msg').html('');
                        if(browser.versions.ios){
                            $(".bombBoxWrap").eq(1).hide();
                            $('.bombBoxWrap').eq(1).css({height:''});
                            $('.box').css({height:'','overflow' : ''});
                        }else{
                            $(".bombBoxWrap").eq(1).hide();
                        }
                        location.reload();
                    }, 2000);
                }, function(res){
                    $('#msg').html(res.msg);
                }, undefined, undefined, function(error){
                    $('#msg').html(error);
                }).ajaxCommit();
            });

            $('#commitExchangeBtn').click(function(){
                $('.msg').html('');
                $.kingForm.create('commitExchangeForm',function (res) {
                    $('.msg').html(res.msg);
                    setTimeout(function(){
                        $('.msg').html('');
                        if(browser.versions.ios){
                            $(".bombBoxWrap").eq(2).hide();
                            $('.bombBoxWrap').eq(2).css({height:''});
                            $('.box').css({height:'','overflow' : ''});
                        }else{
                            $(".bombBoxWrap").eq(2).hide();
                        }
                        location.reload();
                    }, 2000);
                }, function(res){
                    $('.msg').html(res.msg);
                }, undefined, undefined, function(error){
                    $('.msg').html(error);
                }).ajaxCommit();
            });


            break;
        case 'player':
            new Clipboard('#zhuliBtn', {
                text: function (trigger) {
                    return $('#zhuliBtn').data('content');
                }
            });
            $("#zhuliBtn").click(function () {
                $(".bombBoxWrap").show();
            });

            $(".bombBoxWrap").find(".boxCloseBtn").click(function () {
                $(this).parents(".bombBoxWrap").hide();
            });
            //获取更多助力记录
            var isGetZhuli = false;
            $('#zlMoreBtn').click(function(){
                if(isGetZhuli) {
                    return;
                }
                isGetZhuli = true;
                var zhuliPage = parseInt($('#zlMoreBtn').data('page'));
                $.get($('#project').data('path')+'/zhulis', {page:zhuliPage,player:$('#project').data('player')}, function(res){
                    $('#zlMoreBtn').data('page', ++zhuliPage);
                    if(res.data.length > 0) {
                        var body = '';
                        for(var i in res.data){
                            var f = eval('('+res.data[i]+')');
                            body += '<p class="pt10 mar2 lh14 cor_1 tal"><span class="cor_2 fr">'+f.date+'</span><span class="cor_5">'+f.name+'</span>砍掉了<span class="cor_5">￥'+f.price+'</span></p>';
                        }
                        $('#zlTable').append(body);
                    }
                    if(res.data.length < 10) {
                        $('#zlMoreBtn').hide();
                    }
                    isGetZhuli = false;
                })
            });
            break;
    }
});
function Fenye(url){
    this.currentPage = 1;
    this.hasPrevPage = false;
    this.hasNextPage = true;
    this.erCommit = false;
    this.url = url;
    var that = this;
    this.getData = function (type){
        if(!that.erCommit) {
            that.erCommit = true;
            var page = 1;
            switch (type) {
                case 2://上一页
                    if (!that.hasPrevPage) {
                        that.erCommit = false;
                        return false;
                    }
                    page = that.currentPage - 1;
                    break;
                case 3://下一页
                    if (!that.hasNextPage) {
                        that.erCommit = false;
                        return false;
                    }
                    page = that.currentPage + 1;
                    break;
                case 4://最后一页
                    page = -1;
                    break;
            }
            $.get(that.url, {page: page}, function (res) {
                var data = res.data.data;
                var cPage = parseInt(res.data.page);
                if (data.length > 0) {
                    $('#winsTbody').html('');
                    for (var i in data) {
                        var obj = eval('('+data[i]+')');
                        var rank = (cPage-1)*20+parseInt(i)+1;
                        obj.price = obj.price/100;
                        var tr = '<tr><td>'+rank+'</td><td style="text-align: left;"><div class="win-name"><span class="img_tab mr10"><img src="'+obj.poster+'" class="img_tab"></span><span>'+obj.name+'</span></div></td><td style="color: #DD3E38">￥'+obj.price+'</td></tr>';
                        $('#winsTbody').append(tr);
                    }
                    that.currentPage = cPage;
                    $('#currentPage').html(cPage);
                    that.hasNextPage = data.length == 20;
                    that.hasPrevPage = res.data.page > 1;
                    if (cPage == 1 && data.length < 20) {
                        $('#pageBox').hide();
                    } else {
                        $('#pageBox').show();
                    }
                }
                that.erCommit = false;
            });
        }
    }
}