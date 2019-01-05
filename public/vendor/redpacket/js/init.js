$(function(){
    //百度统计
    var _hmt = _hmt || [];
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?26095012d467586b2d582e39b320fb1a";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);

    //提示浮层
    var msgs = {
        hd_end :'本活动已结束，感谢参与！',
        hd_not_start : '活动还未开始，请稍后',
        limit_count: '机会已用完！',
        err:'未知错误'
    };
    function popAlert(msg, time) {
        if (typeof time == 'undefined') {
            time = 3000;
        }
        $('#popAlert p').html(msg);
        $('#popAlert').show();
        setTimeout(function(){
            $('#popAlert').hide();
        }, time);
    }

    var page = $('#page').val();
    switch (page) {
        case 'index':
            //微信分享
            setJSAPI();
            var hongbao = $.extend($('#zhuanti').data(), {
                user:null,
                getCookieKey:function(){
                    return 'js:'+ this.path.substring(1) + ':login';
                },
                getUrl:function(url){
                    return this.path+url;
                },
                updateUserInfo:function(){
                    $('#uPoster').attr('src', this.user.poster);
                    $('#uName').html(this.user.name);
                    $('#totalSpan').html(this.user.total);
                    $('#jhSpan').html(this.user.limit - this.user.count);
                    $('#hbLog').html('');
                    for(var i in this.user.wins) {
                        $('#hbLog').append('<li class="cf mt10"><span class="fl">'+this.user.wins[i].t+'</span><span class="fr cor_7">'+this.user.wins[i].m+'元</span></li>');
                    }
                    if (this.user.status == -1) {
                        new Clipboard('#drawBtn');
                    } else {
                        $('#waP').hide();
                        $('#cbP').show();

                    }
                },
                alert:function(i){
                    $('.bomb_box'+i).bombbox();
                    $(".btn_colse_bomb_box").tap(function(){
                        $.bombbox.hide2();
                    })
                },
                _init:function(){
                    var that = this;
                    if (Cookies.get(that.getCookieKey()) != 1) {
                        $.kajax.get(that.getUrl('/login_status'), function(){
                            that._getUser();
                        });
                    } else {
                        that._getUser();
                    }

                    //领取红包
                    $('#drawBtn').click(function(){
                        //check时间
                        var cTime = new Date().getTime();
                        var sTime = new Date(that.stime.replace(/-/g,'/')).getTime();
                        var eTime = new Date(that.etime.replace(/-/g,'/')).getTime();
                        if (cTime<sTime) {
                            popAlert(msgs.hd_not_start);
                            return;
                        }
                        if (cTime>=eTime) {
                            popAlert(msgs.hd_end);
                            return;
                        }
                        if (that.user && that.user.status!=-1) {
                            if (that.user.count >= that.user.limit) {
                                popAlert(msgs.limit_count);
                                return;
                            }
                            $.kajax.post(that.getUrl('/draw_redpacket'), {time: (new Date()).getTime()}, function (res) {
                                $('#redpacketMoney').html(res.data.money);
                                that.user.count++;
                                that.user.total = Math.floor(math.add(that.user.total, res.data.money)*100)/100;
                                var wins = [];
                                wins[0] = {t:(new Date().format('yyyy/MM/dd hh:mm')), m:res.data.money};
                                for (var i in that.user.wins) {
                                    wins[(i+1)] = that.user.wins[i];
                                }
                                that.user.wins = wins;
                                that.updateUserInfo();
                                that.alert(1);
                            }, function (res) {
                                that.alert(2);
                                that.user.count++;
                                $('#jhSpan').html(that.user.limit - that.user.count);
                            });
                        } else {
                            that.alert(3);
                        }
                    });

                    //我的红包
                    $('#myRedpacket').click(function(){
                        if (that.user && that.user.wins.length>0) {
                            that.alert(6);
                        }else {
                            that.alert(5);
                        }
                    });
                },
                _getUser:function(){
                    var that = this;
                    $.kajax.get(that.getUrl('/user'), null, function(res){
                        that.user = res.data;
                        that.updateUserInfo();
                    });
                }
            });
            break;
        case 'zudui':
            var hongbao = $.extend($('#zhuanti').data(), {
                user:null,
                getCookieKey:function(){
                    return 'js:'+ this.path.substring(1) + ':login';
                },
                getUrl:function(url){
                    return this.path+url;
                },
                updateBtn:function(){
                    if (this.user.reset == 1) {
                        if (this.user.wins.length == this.user.count){
                            $('#hbStusMsg').html('领取红包成功').addClass('cor_4');
                            $('#statusBtn').html('红包已领取').parent().show();
                            if (this.user.count< this.user.limit) {
                                $('#resetBtn').parent().show();
                            }
                        } else {
                            $('#hbStusMsg').html('领取红包失败').removeClass('cor_4');
                            $('#statusBtn').html('领取失败').parent().show();
                        }
                        $('#drawHbMsg').html('');
                        $('#drawBtn').parent().hide();
                        $('#shareBtn').parent().hide();
                    } else if (this.user.zls.length >= this.zlcount) {
                        $('#hbStusMsg').html('好友帮拆完成！快去领取红包吧').addClass('cor_4');
                        $('#drawHbMsg').html('');
                        $('#drawBtn').parent().show();
                        $('#shareBtn').parent().hide();
                        $('#statusBtn').parent().hide();
                        $('#resetBtn').parent().hide();

                    } else {
                        $('#hbStusMsg').html('再邀<span class="mar2 cor_4">'+(this.zlcount-this.user.zls.length)+'位</span>好友即可拆开红包').removeClass('cor_4');
                        $('#shareBtn').parent().show();
                        $('#drawBtn').parent().hide();
                        $('#statusBtn').parent().hide();
                        $('#resetBtn').parent().hide();
                    }
                },
                updateUserInfo:function(){
                    $('#uPoster').attr('src', this.user.poster);
                    $('#uName').html(this.user.name);
                    $('#totalSpan').html(this.user.total);
                    if (this.user.zls.length > 0) {
                        for (var i in this.user.zls) {
                            var $li = $('#packageList li:eq(' + i + ')');
                            $li.find('.prize_money_pos').html(this.user.zls[i].m + '元');
                            $li.find('.head_portrait_pos img').attr('src', this.user.zls[i].p);
                            $li.find('.wp100').attr('src', '/vendor/redpacket/images/ico_04.png');
                            $li.find('div').show();
                        }
                    } else {
                        $('#packageList li').each(function(){
                            $(this).find('div').hide();
                            $(this).find('.wp100').attr('src', '/vendor/redpacket/images/ico_03.png');
                        });
                    }
                    this.updateBtn();
                    $('#hbLog').html('');
                    for(var i in this.user.wins) {
                        $('#hbLog').append('<li class="cf mt10"><span  class="fl">'+this.user.wins[i].t+'</span><span class="fr cor_7">'+this.user.wins[i].m+'元</span></li>');
                    }
                },
                alert:function(i){
                    $('.bomb_box'+i).bombbox();
                    $(".btn_colse_bomb_box").tap(function(){
                        $.bombbox.hide2();
                    })
                },
                _init:function(){
                    var that = this;
                    if (Cookies.get(that.getCookieKey()) != 1) {
                        $.kajax.get(that.getUrl('/login_status'), function(){
                            that._getUser();
                        });
                    } else {
                        that._getUser();
                    }
                    //领取红包
                    $('#drawBtn').click(function(){
                        if (!that._checkDate()) {
                            return;
                        }
                        if (that.user && that.user.status!=-1) {
                            if (that.user.count >= that.user.limit) {
                                popAlert(msgs.limit_count);
                                return;
                            }
                            $.kajax.post(that.getUrl('/draw_redpacket'), {time: (new Date()).getTime()}, function (res) {
                                $('#redpacketMoney').html(res.data.money);
                                that.user.count++;
                                that.user.total = math.add(that.user.total, res.data.money);
                                var wins = [];
                                wins[0] = {t:(new Date().format('yyyy/MM/dd hh:mm')), m:res.data.money};
                                for (var i=0;i<that.user.wins.length;i++) {
                                    wins[(i+1)] = that.user.wins[i];
                                }
                                that.user.wins = wins;
                                that.user.reset = 1;
                                that.updateUserInfo();
                                that.alert(1);
                            }, function (res) {
                                that.alert(2);
                                that.user.count++;
                                that.user.reset = 1;
                                that.updateUserInfo();
                            });
                        } else {
                            that.alert(3);
                        }
                    });

                    //我的红包
                    $('#myRedpacket').click(function(){
                        if (that.user && that.user.wins.length>0) {
                            that.alert(6);
                        }else {
                            that.alert(5);
                        }
                    });

                    //我要再次发起红包
                    $('#resetBtn').click(function(){
                        if (!that._checkDate()) {
                            return;
                        }
                        $.kajax.post(that.getUrl('/reset_team'), {time: (new Date()).getTime()}, function(res){
                            that.user.reset = 0;
                            that.user.zls = [];
                            that.updateUserInfo();
                        }, function(){
                            popAlert(msgs.err);
                        })
                    });
                },
                _getUser:function(){
                    var that = this;
                    $.kajax.get(that.getUrl('/user'), null, function(res){
                        that.user = res.data;
                        $('#wxShareConfig').data('link', that.getUrl('/'+res.data.plyid));
                        setJSAPI();
                        that.updateUserInfo();
                    });
                },
                _checkDate:function(){
                    //check时间
                    var that = this;
                    var cTime = new Date().getTime();
                    var sTime = new Date(that.stime.replace(/-/g,'/')).getTime();
                    var eTime = new Date(that.etime.replace(/-/g,'/')).getTime();
                    if (cTime<sTime) {
                        popAlert(msgs.hd_not_start);
                        return false;
                    }
                    if (cTime>=eTime) {
                        popAlert(msgs.hd_end);
                        return false;
                    }
                    return true;
                }
            });
            break;
        case 'player':
            //微信分享
            setJSAPI();
            var hongbao = $.extend($('#zhuanti').data(), {
                alert:function(i){
                    $('.bomb_box'+i).bombbox();
                    $(".btn_colse_bomb_box").tap(function(){
                        $.bombbox.hide2();
                    })
                },
                _init:function(){
                    $('#zlBtn').click(function(){
                        hongbao.alert(3);
                    });
                    new Clipboard('#zlBtn');
                },
            });
            break;
        default:
            return false;
    }
    hongbao._init();
    //处理底部图片问题
    $("body").css({'paddingBottom':($(".fixed_bottom_img").height()+5)+'px'});
    //活动规则
    $('#ruleBtn').click(function(){hongbao.alert(4);});
    //邀请
    $('#shareBtn').click(function(){$(".share").show();});
    $(".hide_share").click(function() {$(".share").hide();});
    //轮播
    setInterval(function(){
        $(".notice-list").css({"transform":"translate3d(0px, "+(-$(".notice-list li").height())+"px, 0px)", "transition":"all 1s"});
        setTimeout(function(){
            $(".notice-list li:first").appendTo($(".notice-list"));
            $(".notice-list").css({"transform":"translate3d(0,0,0)","transition":"all 0s"});
        },1000);
    }, 2000);
});