if (!String.prototype.trim) {
    String.prototype.trim = function () {
        return this.replace(/(^[\s\n\t]+|[\s\n\t]+$)/g, "");
    }
}
if (!Date.prototype.format) {
    Date.prototype.format = function (fmt) {
        var o = {
            "M+": this.getMonth() + 1, //月份
            "d+": this.getDate(), //日
            "h+": this.getHours(), //小时
            "m+": this.getMinutes(), //分
            "s+": this.getSeconds(), //秒
            "q+": Math.floor((this.getMonth() + 3) / 3), //季度
            "S": this.getMilliseconds() //毫秒
        };
        if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
        for (var k in o)
            if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        return fmt;
    };
}
$.kingLayer = {
    kingLayerId: 0,
    alert: function (obj) {
        if (obj.hasOwnProperty('allContent')) {
            var time = obj.hasOwnProperty('time') ? obj.time : 0;
            var kingId = 'kingLayerId' + (++this.kingLayerId);
            var errHtml = '<div class="bomb_box_wrap" id="' + kingId + '"><div class="bomb_box"><div style="transform-origin: 0px 0px 0px; opacity: 1; transform: scale(1, 1);">' + obj.allContent + '</div></div></div>';
        } else {
            var msg = '';
            if (obj.hasOwnProperty('allMsg')) {
                msg = obj.allMsg;
            } else {
                if (obj.hasOwnProperty('title') && obj.title != '') msg += '<p class="fz16 cor_5 tac">' + obj.title + '</p>';
                if (obj.hasOwnProperty('content') && obj.content != '') msg += '<p class="mt10 fz14">' + obj.content + '</p>';
                if (obj.hasOwnProperty('remark') && obj.remark != '') msg += '<p class="mt10 fz12 cor_2">' + obj.remark + '</p>';
            }
            var showBtn = true;
            if (obj.hasOwnProperty('showBtn')) showBtn = obj.showBtn;
            var btnStyle = showBtn ? '' : 'display:none';
            var textStyle = 'tac';
            if (obj.hasOwnProperty('text_style')) textStyle = obj.text_style;
            var time = obj.hasOwnProperty('time') ? obj.time : 0;
            var kingId = 'kingLayerId' + (++this.kingLayerId);
            var errHtml = '<div class="bomb_box_wrap" id="' + kingId + '"><div class="bomb_box"><div style="transform-origin: 0px 0px 0px; opacity: 1; transform: scale(1, 1);"><div class="bomb_box_text ' + textStyle + '">' + msg + '<p class="mt20 tac"><a style="' + btnStyle + '" href="javascript:void(0);" class="btn_2 commit-btn">知道了</a></p></div></div></div></div>';
        }
        $('body').append(errHtml);
        var this_height, wh, mt;
        $('#' + kingId + ' .bomb_box').height('');
        this_height = $('#' + kingId + ' .bomb_box').height();
        wh = $(window).height();
        mt = this_height < wh ? -this_height / 2 : -wh / 2;
        $('#' + kingId + ' .bomb_box').removeClass('animation_2').addClass('animation_1').css({
            "height": this_height,
            "margin-top": mt
        }); //打开时动画
        $('.box').css({'height': wh, 'overflow': 'hidden'});
        var newLayer = this.layer(kingId);
        var timer = null;
        if (time > 0) {
            timer = setInterval(function () {
                if (obj.hasOwnProperty('success')) {
                    obj.success(newLayer);
                } else {
                    newLayer.close();
                }
                clearInterval(timer);
            }, obj.time);
        }
        $('#' + kingId + ' .btn_close').on('click', function () {
            if (timer != null) {
                clearInterval(timer);
            }
            if (obj.hasOwnProperty('success')) {
                obj.success(newLayer);
            } else {
                newLayer.close();
            }
        });
        $('#' + kingId + ' .commit-btn').on('click', function () {
            if (timer != null) {
                clearInterval(timer);
            }
            if (obj.hasOwnProperty('success')) {
                obj.success(newLayer);
            } else {
                newLayer.close();
            }
        });
        return newLayer;
    },
    layer(id){
        var Layer = function (id) {
            id = '#' + id;
            this.close = function () {
                $(id + ' .bomb_box').removeClass('animation_1').addClass('animation_2'); //关闭时动画
                $('.box').css({'height': '', 'overflow': ''}); //修正页面滚动
                var timer = setInterval(function () {
                    $(id).remove();
                    clearInterval(timer);
                }, 300);

            }
        };
        return new Layer(id);
    }
};
$.kingForm = {
    erCommits: {},
    formId: '',
    data: {},
    ajax: {},
    alertStyle: null,
    vfail: null,
    validateFail: function (msg) {
        if (this.vfail == null) {
            this.alert(msg, 2000);
        } else {
            this.vfail(msg);
        }
    },
    alert: function (msg, time, success, showBtn) {
        if (this.alertStyle == null) {
            showBtn = typeof showBtn == 'undefined' ? true : false;
            if (typeof success == 'undefined') {
                var obj = {title: '温馨提示', content: msg, showBtn: showBtn, time: time};
            } else {
                var obj = {title: '温馨提示', content: msg, showBtn: showBtn, time: time, success: success};
            }
            $.kingLayer.alert(obj);
        } else {
            var allContent = this.alertStyle.replace('MSG_CONTENT', msg);
            if (typeof success == 'undefined') {
                var obj = {allContent: allContent, time: time};
            } else {
                var obj = {allContent: allContent, time: time, success: success};
            }
            $.kingLayer.alert(obj);
        }
    },
    create: function (formId, success, fail, data, alertStyle, vfail) {
        var that = this;
        this.data = {};
        this.formId = '#' + formId;
        if (typeof vfail !== 'undefined') {
            this.vfail = vfail;
        }
        if (typeof data == 'undefined') {
            var data = $(this.formId).serializeArray();
            for (var i in data) {
                this.data[data[i]['name']] = data[i]['value'].trim();
            }
        } else {
            this.data = data;
        }
        if (typeof alertStyle != 'undefined') {
            this.alertStyle = alertStyle;
        }
        this.ajax = {
            type: $(this.formId).attr('method'),
            url: $(this.formId).attr('action'),
            data: this.data,
            dataType: 'json',
            success: function (data) {
                that.erCommits[that.formId] = false;
                switch (data.code) {
                    case 0://成功
                        if (typeof success !== 'undefined') {
                            success(data);
                        } else if (typeof data.data.url !== 'undefined' && data.data.url != '') {
                            that.alert(data.msg, 1500, function () {
                                window.location.href = data.data.url;
                            }, false);
                        }
                        break;
                    case 1://失败
                        if (typeof fail !== 'undefined') {
                            fail(data);
                        } else {
                            that.alert(data.msg, 1500);
                        }
                        break;
                    case 10014://失败
                        location.reload();
                        break;
                }
            },
            error: function () {
                that.erCommits[that.formId] = false;
                $.kingLayer.alert({title: '网络错误', content: '<span class="cor_6">网络错误</span>，请重试'});
            }
        };
        if (!this.erCommits.hasOwnProperty(this.formId)) {
            this.erCommits[this.formId] = false;
        }
        return this;
    },
    validate: function () {
        for (var n in this.data) {
            //获取验证信息
            var filter = $(this.formId + ' [name=' + n + ']').attr('king-filter');
            if (filter != '') {
                var filters = filter.split('|');
                for (var f in filters) {
                    switch (filters[f]) {
                        case 'required':
                            if (this.data[n] == '') {
                                this.validateFail('请输入' + $(this.formId + ' [name=' + n + ']').attr('king-label'));
                                return false;
                            }
                            break;
                        case 'phone':
                            var reg = /^[1][0-9]{10}$/;
                            if (!reg.test(this.data[n])) {
                                this.validateFail($(this.formId + ' [name=' + n + ']').attr('king-label') + '格式不正确');
                                return false;
                            }
                            break;
                        case 'email':
                            var reg = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
                            if (!reg.test(this.data[n])) {
                                this.validateFail($(this.formId + ' [name=' + n + ']').attr('king-label') + '必须是邮箱');
                                return false;
                            }
                            break;
                        case 'address':
                            if (this.data[n].length > 50) {
                                this.validateFail($(this.formId + ' [name=' + n + ']').attr('king-label') + '长度不能超过50');
                                return false;
                            }
                            break;
                    }
                }
            }
        }
        return true;
    },
    ajaxCommit: function () {
        if (this.validate()) {
            if (!this.erCommits[this.formId]) {
                this.erCommits[this.formId] = true;
                $.ajax(this.ajax);
            }
        }
        return false;
    }
};
function setJSAPI(successFuc) {
    var $wx = $('#wxShareConfig');
    var domain = window.location.protocol + '//' + window.location.host;
    var wjWeixinShareOptions = {
        title: $wx.data('title'),
        desc: $wx.data('share'),
        link: domain + $wx.data('link'),
        imgUrl: domain + $wx.data('img'),
        success: function () {
            if (typeof successFuc !== 'undefined') {
                successFuc();
            }
        },
        cancel: function () {
        }
    };
    $.getJSON($wx.data('url'), function (res) {
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
function getChromeVersion() {
    return {
        versions: function () {
            var u = navigator.userAgent, app = navigator.appVersion;
            return {
                trident: u.indexOf('Trident') > -1, //IE内核
                presto: u.indexOf('Presto') > -1, //opera内核
                webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
                gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1,//火狐内核
                mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
                ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
                android: u.indexOf('Android') > -1 || u.indexOf('Adr') > -1, //android终端
                iPhone: u.indexOf('iPhone') > -1, //是否为iPhone或者QQHD浏览器
                iPad: u.indexOf('iPad') > -1, //是否iPad
                webApp: u.indexOf('Safari') == -1, //是否web应该程序，没有头部与底部
                weixin: u.indexOf('MicroMessenger') > -1, //是否微信 （2015-01-22新增）
                qq: u.match(/\sQQ/i) == " qq" //是否QQ
            };
        }(),
        language: (navigator.browserLanguage || navigator.language).toLowerCase()
    };
}
function listenInputMoney(id, isInt) {
    $(id).keyup(function () {
        var v = $(this).val();
        if (/^0+$/.test(v)) {
            txt = 0;
        } else if (/^0+[1-9]+$/.test(v)) {
            var reg = v.match(/^0+/);
            if (reg != null) {
                txt = v.substr(reg[0].length);
            }

        } else {
            if (isInt) {
                var reg = v.match(/\d+/);
            } else {
                var reg = v.match(/\d+\.?\d{0,2}/);
            }
            var txt = '';
            if (reg != null) {
                txt = reg[0];
            } else {
                txt = '';
            }
        }
        $(this).val(txt);
    }).change(function () {
        $(this).keypress();
        var v = $(this).val();
        if (/\.$/.test(v)) {
            $(this).val(v.substr(0, v.length - 1));
        }
    });
}

function kingAjax(obj, success, fail) {
    if (!obj.hasOwnProperty('isCommit')) {
        obj.isCommit = false;
    }
    if (obj.isCommit) {
        return;
    }
    obj.isCommit = true;
    var type = obj.hasOwnProperty('type') ? obj.type : 'POST';
    var data = obj.hasOwnProperty('data') ? obj.data : '';
    $.ajax({
        type: type,
        url: obj.url,
        data: data,
        dataType: 'json',
        success: function (res) {
            if (res.code == 10014) {
                location.reload();
            } else if (res.code == 0) {
                if (typeof success != 'undefined') {
                    success(res);
                }
            } else {
                if (typeof fail != 'undefined') {
                    fail(res);
                }
            }
            obj.isCommit = false;
        },
        error: function () {
            obj.isCommit = false;
        }
    });
}
(function() {
    var _hmt = _hmt || [];
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?26095012d467586b2d582e39b320fb1a";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
})();