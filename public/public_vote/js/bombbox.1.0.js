/*
* bombbox.1.0.js
* © 2015 by zhouzuchuan wangjinyao
zepto 手机触屏版 弹窗插件
*/

$(function(){
    $.fn.bombbox = function(options) {
        return this.each(function() {
          var s = $.extend({}, bombboxDefault, options || {});
          $.bombbox($(this), options); 
        });
    };

    $.bombbox = function(elements, options) {
        if (!elements) {
            return; 
        }
        var s = $.extend({}, bombboxDefault, options || {});

        var wrap = '<div class="bomb_box_wrap" id="bomb_box_wrap"><div class="bomb_box"></div></div>'; //添加弹窗结构

        if ($(".bomb_box_wrap").size()) {
            $(".bomb_box_wrap").show();
        } else {
            $("body").append(wrap); //摆放位置 防止多次家长
        }

        if (typeof(elements) === "object") {
            elements.show();
        } else {
            elements = $(elements);
        }

        $.o = {
            s: s,
            chief: elements //当前
        };

        $(".bomb_box").empty().append(elements); //装载自定义弹窗层

        if ($.isFunction(s.onshow)){  //是否为函数 , 是返回真
            s.onshow()
        }

        $.bombbox.show(); //加载弹窗是一些工作

        //关闭弹窗 这里可删除
        $('.btn_close').click(function () {
            $.bombbox.hide2();
            return false;
        });
    };

    $.extend($.bombbox, {
        show: function(){
            //弹窗显示时操作
            var this_height,
                wh,
                mt;
            $('.bomb_box').height('');
            this_height = $('.bomb_box').height();
            wh = $(window).height()
            mt = this_height < wh ? -this_height/2 : -wh/2;
            $('.bomb_box').removeClass('animation_2').addClass('animation_1').css({"height":this_height,"margin-top":mt}); //打开时动画
            $('.box').css({ 'height' : wh , 'overflow' : 'hidden' }); //禁止页面滚动
        },
        hide2: function(){
            if ($.o.chief && $('.bomb_box_wrap').size() && $('.bomb_box').size() && $('.bomb_box_wrap').css("display") !== "none") {
                $('.bomb_box').removeClass('animation_1').addClass('animation_2'); //关闭时动画
                $.o.chief.hide().appendTo($("body")); //复制自定义弹窗层
                $(".bomb_box_wrap").fadeOut("fast", function() { //删掉盒子
                    $(this).remove();
                    if ($.isFunction($.o.s.onclose)){ //关闭时加载
                        $.o.s.onclose()
                    }
                });
                $('.box').css({ 'height' : '', 'overflow' : '' }); //修正页面滚动
            }
            return false;
        }
    });

    var bombboxDefault = { //我在想，要不要添加打开关闭时的接口，为了加这两个玩意，原本很简单的插件格式，需要多次分拆，需要吗？

        onshow: $.noop, //弹窗显示后触发事件
        onclose:$.noop
    };

});