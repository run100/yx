$.kajax = {
    commits:{},
    get:function(url, data, success, fail){
        this._sendRequest(url, data, success, fail, 'get', 'application/json; charset=utf-8', true);
    },
    post:function(url, data, success, fail){
        this._sendRequest(url, data, success, fail, 'post', 'application/x-www-form-urlencoded', true);
    },
    delete:function(url, success, fail){
        this._sendRequest(url, {}, success, fail, 'delete', false, false);
    },
    postForm:function(url, data, success, fail){
        this._sendRequest(url, data, success, fail, 'post', false, false);
    },
    _sendRequest: function(url, data, success, fail, method, contentType, processData){
        var that = this;
        if (that.commits.hasOwnProperty(url) && that.commits[url]) {
            return false;
        }
        that.commits[url] = true;
        $.ajax({
            type: method,
            url: url,
            data:  data,
            async: true,
            processData:processData,
            contentType:contentType,
            beforeSend: function () {

            },
            error: function (request) {
                that.commits[url] = false;
            },
            success: function (res) {
                switch (res.code) {
                    case 302:
                        window.location.href = res.data.url;
                        break;
                    case 0:
                        if(success != null && typeof success != undefined){
                            success(res);
                        }
                        break;
                    case 1:
                        if(fail != null && typeof fail != undefined){
                            fail(res);
                        }
                        break;
                    case 10014:
                        location.href = $('#zhuanti').data('path') + '/login_start?redirectUrl='+encodeURIComponent(window.location.href);
                        break;
                }
                that.commits[url] = false;
            }
        });
    },
    _success:function(){

    },
    _fail:function(){

    }
};
function getPar(par){
    var local_url = document.location.href;
    var get = local_url.indexOf(par +"=");
    if(get == -1){
        return '';
    }
    var get_par = local_url.slice(par.length + get + 1);
    var nextPar = get_par.indexOf("&");
    if(nextPar != -1){
        get_par = get_par.slice(0, nextPar);
    }
    return get_par;
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
    $.getJSON($wx.data('url')+'?use_url='+encodeURIComponent(window.location), function (res) {
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