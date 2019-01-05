@extends('zhuanti::common.normal')

@lconfig([
    'is_mobile' => true,
    'is_clean'  => true,

    //定义模板参数默认值
    'js_wxsdk'  => false,   //加载微信JS-SDK
    'js_cookie' => false,   //是否加载js-cookie
])

{{--
    WEUI 是一个仿微信原生界面的 UI 框架；用于快速搭建单页面应用。
    参考文档:
        说明文档 https://github.com/Tencent/weui/wiki
        视觉标准 https://github.com/weui/weui-design

    @section('title')
        定义单页面默认标题; 此标题可被 template 中的 title 属性覆盖

    @section('pages')
        定义了各界面模板; 每个 template 是一个独立界面。
        id 属性定义了前端routing地址，如 id=tpl_list1 定义的界面，可以用 http://domain/path?foo=bar#list1 访问
        title 属性定义了导航栏标题内容

    @section('weixin_share')
        需开启 js_wxsdk;
        定义了一个 JSON 结构体，用于配置用于分享的文案和链接，JSON 结构如下:
        {
            title: "标题",
            desc: "摘要",
            link: "链接",
            imgUrl: "图片链接",
            success: function() {
                //分享成功回调
            },
            cancel: function() {
                //取消分享回调
            }
        }

    完整的例子可参考 zhuanti::examples/weui.blade.php

    注意事项:
    1. 已引入 Zepto 库，不建议再引入巨大的 jQuery
    2. template 中也可以写 script 标签，是在每次进入时执行；从前一个页面返回时不重复执行
--}}

@section('styles')
    <link rel="stylesheet" href="https://res.wx.qq.com/open/libs/weui/1.1.2/weui.min.css"/>
    <link rel="stylesheet" href="{{asset('/vendor/zhuanti/weui/dist/pages/pages.css')}}"/>
    <style type="text/css">
        .weui-icon_toast {
            margin-top: 18px;
            margin-bottom: 8px;
        }
        .weui-icon_toast:before {
            color: #FFFFFF;
            font-size: 55px;
        }
        .hidden {
            display: none;
        }
    </style>
@endsection

@section('body')
    <div class="weui-toptips weui-toptips_warn js_tooltips">错误提示</div>

    <div class="container" id="container"></div>

    <template id="comp_toast">
        <div class="wj-weui-toast" style="display:none;">
            <div class="weui-mask_transparent"></div>
            <div class="weui-toast">
                <i class="weui-icon-success-no-circle weui-icon_toast"></i>
                <p class="weui-toast__content">已完成</p>
            </div>
        </div>
    </template>

    <template id="comp_loading">
        <div class="wj-weui-loading" style="display:none;">
            <div class="weui-mask_transparent"></div>
            <div class="weui-toast">
                <i class="weui-loading weui-icon_toast"></i>
                <p class="weui-toast__content">数据加载中</p>
            </div>
        </div>
    </template>


    <template id="comp_dialog">
        <div class="wj-weui-dialog" class="js_dialog" style="display: none;">
            <div class="weui-mask"></div>
            <div class="weui-dialog">
                <div class="weui-dialog__hd"><strong class="weui-dialog__title">弹窗标题</strong></div>
                <div class="weui-dialog__bd">弹窗内容，告知当前状态、信息和解决方法，描述文字尽量控制在三行内</div>
                <div class="weui-dialog__ft">
                </div>
            </div>
        </div>
    </template>


    <template id="comp_actionsheet">
        <div class="wj-weui-actionsheet">
            <div class="weui-mask" style="display: none"></div>
            <div class="weui-actionsheet">
                <div class="weui-actionsheet__title">
                    <p class="weui-actionsheet__title-text">这是一个标题，可以为一行或者两行。</p>
                </div>
                <div class="weui-actionsheet__menu">
                </div>
                <div class="weui-actionsheet__action">
                    <div class="weui-actionsheet__cell" data-cancel="1">取消</div>
                </div>
            </div>
        </div>
    </template>

    @section('pages')

        <template id="tpl_home" title="首页">
            <div class="page">
                <div class="page__hd">
                    <h1 class="page__title">
                        <img src="/vendor/zhuanti/weui/dist/pages/images/logo.png" alt="WeUI" height="21px" />
                    </h1>
                    <p class="page__desc">WeUI 是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信内网页和微信小程序量身设计，令用户的使用感知更加统一。</p>
                </div>
                <div class="page__bd page__bd_spacing">
                    <ul>
                        <li>
                            <div class="weui-flex js_item" data-id="layers">
                                <p class="weui-flex__item">Item1</p>
                                <img src="/vendor/zhuanti/weui/dist/pages/images/icon_nav_z-index.png" alt="">
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="page__ft">
                    <a href="javascript:home()"><img src="/vendor/zhuanti/weui/dist/pages/images/icon_footer.png" /></a>
                </div>
            </div>
        </template>

    @show
@endsection

@section('scripts')

    @if($__layout['js_wxsdk'])
    <script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.0.js"></script>
    @endif

    <script type="text/javascript" src="/vendor/zhuanti/zepto/zepto.min.js"></script>
    <script type="text/javascript" src="https://res.wx.qq.com/open/libs/weuijs/1.1.3/weui.min.js"></script>

    @if(!$__layout['is_clean'] && $__layout['js_sweetalert'])
        <script type="text/javascript" src="/vendor/zhuanti/sweetalert/dist/sweetalert.min.js"></script>
    @endif

    <script type="text/javascript">
        function current_page()
        {
            return $('#container .page.js_show:last');
        }

        document.addEventListener('touchstart',function(){},false);

        $(function () {
            var pageManager = {
                $container: $('#container'),
                _pageStack: [],
                _configs: [],
                _pageAppend: function(){},
                _defaultPage: null,
                _pageIndex: 1,
                originTitle: document.title,
                setDefault: function (defaultPage) {
                    this._defaultPage = this._find('name', defaultPage);
                    return this;
                },
                setPageAppend: function (pageAppend) {
                    this._pageAppend = pageAppend;
                    return this;
                },
                init: function () {
                    var self = this;

                    $(window).on('hashchange', function () {
                        var state = history.state || {};
                        var url = location.hash.indexOf('#') === 0 ? location.hash : '#';
                        var page = self._find('url', url) || self._defaultPage;
                        if (state._pageIndex <= self._pageIndex || self._findInStack(url)) {
                            self._back(page);
                        } else {
                            self._go(page);
                        }
                    });

                    if (history.state && history.state._pageIndex) {
                        this._pageIndex = history.state._pageIndex;
                    }

                    this._pageIndex--;

                    var url = location.hash.indexOf('#') === 0 ? location.hash : '#';
                    var page = self._find('url', url) || self._defaultPage;
                    this._go(page, false);
                    return this;
                },
                push: function (config) {
                    this._configs.push(config);
                    return this;
                },
                go: function (to) {
                    var config = this._find('name', to);
                    if (!config) {
                        return;
                    }
                    location.hash = config.url;
                },
                _go: function (config) {
                    this._pageIndex ++;

                    history.replaceState && history.replaceState({_pageIndex: this._pageIndex}, '', location.href);

                    var html = $(config.template).html();
                    var $html = $(html).addClass(config.name);
                    $html.addClass('js_show');
                    this.$container.append($html);
                    this._pageAppend.call(this, $html);
                    this._pageStack.push({
                        config: config,
                        dom: $html
                    });

                    if (!config.isBind) {
                        this._bind(config);
                    }

                    document.title = config.title;

                    return this;
                },
                back: function () {
                    history.back();
                },
                _back: function (config) {
                    this._pageIndex --;

                    var stack = this._pageStack.pop();
                    if (!stack) {
                        return;
                    }

                    var url = location.hash.indexOf('#') === 0 ? location.hash : '#';
                    var found = this._findInStack(url);
                    if (!found) {
                        current_page().removeClass('js_show');

                        var html = $(config.template).html();
                        var $html = $(html).addClass('js_show').addClass(config.name);
                        $html.insertBefore(stack.dom);

                        if (!config.isBind) {
                            this._bind(config);
                        }

                        this._pageStack.push(found = {
                            config: config,
                            dom: $html
                        });
                    }

                    var doms = [];
                    var node = found.dom;
                    while (node.next().size()) {
                        doms.push(node = node.next());
                    }

                    for (var i = 0; i < doms.length; i++) {
                        if (i > 0) {
                            this._pageStack.pop();
                        }

                        doms[i].remove();
                    }

                    document.title = config.title;
                    return this;
                },
                _findInStack: function (url) {
                    var found = null;
                    for(var i = 0, len = this._pageStack.length; i < len; i++){
                        var stack = this._pageStack[i];
                        if (stack.config.url === url) {
                            found = stack;
                            break;
                        }
                    }
                    return found;
                },
                _find: function (key, value) {
                    var page = null;
                    for (var i = 0, len = this._configs.length; i < len; i++) {
                        if (this._configs[i][key] === value) {
                            page = this._configs[i];
                            break;
                        }
                    }
                    return page;
                },
                _bind: function (page) {
                    var events = page.events || {};
                    for (var t in events) {
                        for (var type in events[t]) {
                            this.$container.on(type, t, events[t][type]);
                        }
                    }
                    page.isBind = true;
                }
            };

            function fastClick(){
                var supportTouch = function(){
                    try {
                        document.createEvent("TouchEvent");
                        return true;
                    } catch (e) {
                        return false;
                    }
                }();
                var _old$On = $.fn.on;

                $.fn.on = function(){
                    if(/click/.test(arguments[0]) && typeof arguments[1] === 'function' && supportTouch){ // 只扩展支持touch的当前元素的click事件
                        var touchStartY, callback = arguments[1];
                        _old$On.apply(this, ['touchstart', function(e){
                            touchStartY = e.changedTouches[0].clientY;
                        }]);
                        _old$On.apply(this, ['touchend', function(e){
                            if (Math.abs(e.changedTouches[0].clientY - touchStartY) > 10) return;

                            e.preventDefault();
                            callback.apply(this, [e]);
                        }]);
                    }else{
                        _old$On.apply(this, arguments);
                    }
                    return this;
                };
            }
            function androidInputBugFix(){
                if (/Android/gi.test(navigator.userAgent)) {
                    window.addEventListener('resize', function () {
                        if (document.activeElement.tagName.toUpperCase() === 'INPUT' || document.activeElement.tagName.toUpperCase() === 'TEXTAREA') {
                            window.setTimeout(function () {
                                document.activeElement.scrollIntoViewIfNeeded();
                            }, 0);
                        }
                    })
                }
            }
            function setJSAPI(){
                var wjWeixinShareOptions = @section('weixin_share')
                    false
                @show
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
            function setPageManager(){
                var pages = {}, tpls = $('template');
                var winH = $(window).height();

                for (var i = 0, len = tpls.length; i < len; ++i) {
                    var tpl = tpls[i], name = tpl.id.replace(/tpl_/, '');
                    pages[name] = {
                        name: name,
                        url: '#' + name,
                        template: '#' + tpl.id,
                        title: tpl.title || pageManager.originTitle
                    };
                }
                pages.home.url = '#';

                for (var page in pages) {
                    pageManager.push(pages[page]);
                }
                pageManager
                    .setPageAppend(function($html){
                        var $foot = $html.find('.page__ft');
                        if($foot.length < 1) return;

                        if($foot.position().top + $foot.height() < winH){
                            $foot.addClass('j_bottom');
                        }else{
                            $foot.removeClass('j_bottom');
                        }
                    })
                    .setDefault('home')
                    .init();
            }

            function init(){
                fastClick();
                androidInputBugFix();

                @if($__layout['js_wxsdk'])
                setJSAPI();
                @endif

                setPageManager();

                window.pageManager = pageManager;
                window.home = function(){
                    location.hash = '';
                };
            }
            init();
        });


        /**
         * Toast 消息
         * weui_toast('muhaha')
         * weui_toast('muhaha', 'success')
         * weui_toast('muhaha', 'warn', 5000)
         *
         * @param msg       Toast 信息
         * @param icon      图标: info(默认)  warn   error   success   waiting
         * @param duration  持续时间(ms)
         */
        function weui_toast(msg, icon, duration)
        {
            if (duration === undefined) {
                duration = 2000;
            }

            if (icon === undefined) {
                icon = 'info-circle';
            } else if (icon === 'success') {
                icon = 'success-no-circle';
            } else if (icon === 'error') {
                icon = 'cancel';
            } else if (icon === 'info') {
                icon = 'info-circle';
            }

            var $toast = $($('#comp_toast').html()).appendTo('#container .page:last');

            $toast.find('.weui-icon_toast').attr('class', 'weui-icon_toast').addClass('weui-icon-' + icon);
            $toast.find('.weui-toast__content').text(msg);
            $toast.fadeIn(100);
            setTimeout(function () {
                $toast.fadeOut(100, function() {
                    $toast.remove();
                });
            }, duration);

            return $toast;
        }

        /**
         * Loading
         * weui_loading('数据加载中...')
         * weui_loading(false)   //关闭
         *
         * @param msg Loading 信息
         */
        function weui_loading(msg)
        {
            if (msg === undefined) {
                msg = '数据加载中...';
            }

            if (msg === false) {
                var $loading = $('#container .page .wj-weui-loading');
                $loading.fadeOut(100, function() {
                    $loading.remove();
                });
                return;
            }

            var $loading = $($('#comp_loading').html()).appendTo('#container .page:last');

            $loading.find('.weui-toast__content').text(msg);
            $loading.fadeIn(100);
            return $loading;
        }

        /**
         * Alert
         *
         * weui_alert("muhaha");
         * weui_alert("muhaha", {buttons:1});
         * weui_alert("muhaha", {buttons:2});
         * weui_alert("title", "muhaha");
         * weui_alert("muhaha", {"title": "title", "okText": "购买", "cancelText": "取消", "onOk": function() {console.log(123);}})
         *
         */
        function weui_alert()
        {
            if (arguments.length === 1) {
                if (typeof arguments[0] === 'string') {
                    return weui_alert({
                        'title': '提示',
                        'msg': arguments[0]
                    });
                } else {
                    var opts = arguments[0];
                    opts.buttons === undefined && (opts.buttons = 1);
                    if (opts.buttons === 1) {
                        opts.okText === undefined && (opts.okText = '知道了');
                        opts.cancelText === undefined && (opts.cancelText =  false);
                    } else {
                        opts.okText === undefined && (opts.okText = '确认');
                        opts.cancelText === undefined && (opts.cancelText = '取消');
                    }

                    var $alert = $($('#comp_dialog').html()).appendTo('#container .page:last');
                    $alert.find('.weui-dialog__title').text(opts['title']);
                    $alert.find('.weui-dialog__bd').text(opts['msg']);
                    $alert.fadeIn(100);

                    if (opts.cancelText !== false) {
                        $alert.find('.weui-dialog__ft').append('<a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_default">'+opts.cancelText+'</a>');
                    }

                    if (opts.okText !== false) {
                        $alert.find('.weui-dialog__ft').append('<a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary">'+opts.okText+'</a>');
                    }

                    $alert.find('.weui-dialog__btn_default').click(function() {
                        $alert.fadeOut(100, function() {
                            $alert.remove();
                        });

                        if (opts.onCancel) {
                            opts.onCancel();
                        }
                    });

                    $alert.find('.weui-dialog__btn_primary').click(function() {
                        $alert.fadeOut(100, function() {
                            $alert.remove();
                        });

                        if (opts.onOk) {
                            opts.onOk();
                        }
                    });

                    return $alert;
                }
            } else if (arguments.length === 2) {
                if (typeof arguments[0] === 'string' && typeof arguments[1] === 'string') {
                    return weui_alert({
                        'title': arguments[0],
                        'msg': arguments[1]
                    });
                } else if (typeof arguments[0] === 'string' && typeof arguments[1] === 'object') {
                    arguments[1].msg = arguments[0];
                    return weui_alert(arguments[1]);
                } else if (typeof arguments[0] === 'string' && typeof arguments[1] === 'function') {
                    return weui_alert({
                        'title': '提示',
                        'buttons': 1,
                        'msg': arguments[0],
                        'onOk': arguments[1]
                    });
                }
            }
        }

        /**
         *
         * ActionSheet
         *
         * weui_actionsheet('请选择性别:', {'M': '男', 'W': '女'}, function(v) {console.log(v);})
         * weui_actionsheet('请选择性别:', [{key: 'M', name: '男'}, {key: 'W', name: '女'}], function(v) {console.log(v);})
         *
         * @param title     标题
         * @param items     选项(可以是数组，也可以是带 key 的 object，但 object 不保证显示顺序)
         * @param callback  选择回调
         */
        function weui_actionsheet(title, items, callback)
        {
            var $actionsheet = $($('#comp_actionsheet').html()).appendTo('#container .page:last');
            $actionsheet.find('.weui-actionsheet__title-text').text(title);

            if (!$.isArray(items)) {
                var parsedItems = [];
                for (var i in items) {
                    parsedItems.push({
                        key: i,
                        name: items[i]
                    });
                }
                items = parsedItems;
            }

            for (var i in items) {
                $actionsheet.find('.weui-actionsheet__menu').append('<div class="weui-actionsheet__cell" data-value="'+items[i].key+'">'+items[i].name+'</div>');
            }

            $actionsheet.on('click', '.weui-actionsheet__cell', function() {
                $actionsheet.find('.weui-actionsheet').removeClass('weui-actionsheet_toggle');
                $actionsheet.find('.weui-mask').fadeOut(200, function() {
                    $actionsheet.remove();
                });

                if ($(this).data('cancel')) {
                    return;
                }

                callback($(this).data('value'));
            });


            $actionsheet.find('.weui-actionsheet').addClass('weui-actionsheet_toggle');
            $actionsheet.find('.weui-mask').fadeIn(200);
        }
    </script>
@endsection