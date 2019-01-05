function Fenye(url){
    this.currentPage = 1;
    this.hasPrevPage = false;
    this.hasNextPage = true;
    this.erCommit = false;
    this.url = url;
    var that = this;
    this.getData = function (type, isShowNum){
        if(typeof isShowNum == "undefined") {
            isShowNum = true;
        }
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
                if (data.length > 0) {
                    $('#winsTbody').html('');
                    for (var i=0;i< data.length;i++) {
                        var obj = eval("(" + data[i] + ")");
                        var num = res.data.total_count-(res.data.page-1)*20-i;
                        var tr = '<tr>'+(isShowNum? '<td>' + (num > 9 ? num : '0' + num) + '</td>' : '')+'<td>' + obj.name + '</td><td>' + obj.prize + '</td></tr>';
                        $('#winsTbody').append(tr);
                    }
                    that.currentPage = res.data.page;
                    $('#currentPage').html(res.data.page);
                    that.hasNextPage = data.length == 20;
                    that.hasPrevPage = res.data.page > 1;
                    if (res.data.page == 1 && data.length < 20) {
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
function Shuffle(array) {
    var m = array.length,
        t, i;
    while (m) {
        i = Math.floor(Math.random() * m--);
        t = array[m];
        array[m] = array[i];
        array[i] = t;
    }
    return array;
}

function WheelAdventures(conId, success, fail) {
    this.$con = $(conId);
    this.$rotates = this.$con.find('.rotate');
    this.$round = this.$con.find('.round');
    this.$outter = this.$con.find('.pan');
    this.$inner = this.$con.find('.point_box');
    this.len = this.$rotates.length;
    this.totalDeg = 360*5;
    this.steps = [];
    this.isRun = false;
    this.now = 0;
    this.maxI = 0;
    var that = this;
    //设置大转盘的高度
    this.$con.height(this.$con.width());

    var rotate_out = this.$con.width();
    var rotate_in = 262/338*rotate_out;
    var angles = 360/this.len/2;
    this.$round.css({
        width: rotate_in,
        height: rotate_in,
        top: ( rotate_out - rotate_in ) / 2,
        left: ( rotate_out - rotate_in ) / 2
    });
    for ( var i = 1 ; i <= this.len ; i++ ){
        //this.$rotates.eq(i-1).find('span').html(this.$rotates.eq(i-1).find('span').html().substring(0,6));
        this.$rotates.eq(i-1).css({
            transform: 'rotate(' + (360 / this.len * (i-1) + angles) + 'deg)',
            //三角余弦函数求第三边
            width: Math.sqrt( Math.pow(rotate_in/2,2) * 2 - 2 * rotate_in/2 * rotate_in/2 * Math.cos( 2*Math.PI/360*(360 / this.len) ) ),
            //圆弧上点的坐标
            left: rotate_in / 2 + Math.sin( 2*Math.PI/360*(360 / this.len * (i-1) ) ) * rotate_in / 2 ,
            top: rotate_in / 2 - Math.cos( 2*Math.PI/360*(360 / this.len * (i-1) ) ) * rotate_in / 2
        });
    }

    this.pushSteps = function() {
        var t = Math.sqrt(2*that.totalDeg/0.01);
        var v = 0.01 * t;
        for (; that.maxI<t; that.maxI++) {
            that.steps.push((2 * v * that.maxI - 0.01 * that.maxI * that.maxI) / 2);
        }
        that.steps.push(that.totalDeg);
    };

    this.step = function() {
        that.$outter.css({transform:'rotate(' + that.steps[that.now++] + 'deg)'});
        that.$inner.css({transform:'rotate(' + that.steps[that.now++] + 'deg)'});
        if (that.now<that.steps.length) {
            requestAnimFrame(that.step);
        } else {
            //中奖逻辑
            setTimeout(function () {
                var type = parseInt(that.$rotates.eq(that.deg - 1).find('span').data('type'));
                if(type != 2){
                    var several_text = that.$rotates.eq(that.deg - 1).find('span').html();
                    if(typeof success != 'undefined'){
                        success(several_text);
                    } else {
                        $.kingLayer.alert({allContent: $('#zjTemp').html().replace('PRIZES_NAME', several_text)});
                    }
                }else {
                    if(typeof fail != 'undefined'){
                        fail();
                    }else {
                        $.kingLayer.alert({allContent: '<div class="bomb_box_text_2 re"><i class="btn_colse_bomb_box_01 btn_close"></i><div class="ico_title_01"></div><div class="mt10 tac"><i class="ico_sad_face"></i></div><p class="mt10 fz14 lh16 tac cor_6">哎呀，没中！摆好姿势再来一次</p><div class="mt20 tac"><a href="javascript:void(0);" class="btn_1 btn_close">再来一次</a></div></div>'});
                    }
                }
                that.isRun = false;
            }, 200)
        }
    };

    this.start = function(deg) {
        if(that.isRun) {
            return ;
        }
        that.isRun = true;
        //每次重新启动重新配置
        that.now = 0;
        that.maxI = 0;
        that.totalDeg = deg>0 ? 360 * 5 + parseInt( (that.len-deg) * (360 / that.len) + (360 / that.len) / 2 ) : 360*5;
        that.deg = deg;
        that.steps = [];
        that.pushSteps();
        requestAnimFrame(that.step);
    };
}

function Jgg(id) {
    this.index = -1; //当前转动到哪个位置，起点位置
    this.timer = 0;    //setTimeout的ID，用clearTimeout清除
    this.speed = 100;   //初始转动速度
    this.times = 0;    //转动次数
    this.cycle = 50;   //转动基本次数：即至少需要转动多少次再进入抽奖环节
    this.prize = -1;   //中奖位置
    this.isRun = false;
    this.obj = $(id);
    this.count = this.obj.find('.award').length;  //总共有多少个位置
    this.obj.find('.award-'+this.index).parents('.content_area').addClass('active');
    this.onEnd = function () {
        var that = this;
        setTimeout(function () {
            var type = parseInt(that.obj.find('.award-'+that.prize).data('type'));
            if(type != 2){
                var several_text = that.obj.find('.award-'+that.prize).find('.fz_sty').html();
                $.kingLayer.alert({allContent: $('#zjTemp').html().replace('PRIZES_NAME', several_text)});
            }else {
                $.kingLayer.alert({allContent: '<div class="bomb_box_text_2 re"><i class="btn_colse_bomb_box_01 btn_close"></i><div class="ico_title_01"></div><div class="mt10 tac"><i class="ico_sad_face"></i></div><p class="mt10 fz14 lh16 tac cor_6">哎呀，没中！摆好姿势再来一次</p><div class="mt20 tac"><a href="javascript:void(0);" class="btn_1 btn_close">再来一次</a></div></div>'});
            }
            that.isRun = false;
            that.prize = -1;
            that.times = 0;
            that.cycle = 50;
            that.isRun = false;
            that.speed = 100;
        }, 200);
        clearTimeout(this.timer);
    };
    this.start = function () {
        var that = this;
        if(!that.isRun){
            that.isRun = true;
            var roll = function(){
                that.times++;
                var index = that.index;
                that.obj.find(".award-"+index).parents('.content_area').removeClass("active");
                index++;
                if (index>that.count-1) {
                    index = 0;
                }
                that.obj.find(".award-"+index).parents('.content_area').addClass('active');
                that.index = index;
                if(that.times > that.cycle+10 && that.prize == that.index) {
                    that.onEnd();
                } else {
                    if(that.times < that.cycle) {
                        that.speed -= 10;
                    } else if(that.times == that.cycle) {
                        if(that.prize == -1) {
                            that.cycle += 10; //奖品没设置成功加10圈
                        }
                    } else {
                        if (that.timers > that.cycle+10 && ((that.prize == 0 && that.index == 7) || that.prize == that.index + 1)) {
                            that.speed += 110;
                        } else {
                            that.speed += 20;
                        }
                    }
                    if(that.speed < 40) {
                        that.speed = 40;
                    }
                    that.timer = setTimeout(roll, that.speed);
                }
            };
            roll();
        }
    }
}

function Fp(id){
    this.obj = $(id);
    this.id = id;
    this.isRun = false;
    this.allPrizes = [];
    var that = this;
    $(this.id+' .item_backcard').css({'height':$(this.id+' .item_backcard .frontface').height()});
    $(this.id+' .item_backcard').each(function(i){
        that.allPrizes[i] = {name:$(this).data('name'), type:$(this).data('type'), index:parseInt($(this).data('index'))};
    });
    this.start = function(liIndex, point) {
        if(that.isRun) {
            return;
        }
        that.isRun = true;
        that.allPrizes = Shuffle(that.allPrizes);
        var sIndex = 0;
        var prize;
        for(var p=0;p<that.allPrizes.length;p++) {
            if(that.allPrizes[p].index == point) {
                var $sel = $(this.id+' .item_backcard:eq('+liIndex+')');
                prize = that.allPrizes[p];
                $sel.data('type', prize.type);
                $sel.data('name', prize.name);
                $sel.find('.prize_fz').html(prize.name);
                $sel.find('.img_par').removeClass('flip');
                $sel.addClass('active');
                $sel.find('.top_img').attr('src', '/vendor/prizes/images/frontface_card_01.png');
                $sel.find(".ico_logo_card").attr('src','/vendor/prizes/images/ico_bf_2.png');
            } else if(sIndex!=liIndex){
                $(this.id+' .item_backcard:eq('+sIndex+')').find('.prize_fz').html(that.allPrizes[p].name);
                sIndex++;
            }
            if(sIndex == liIndex) {
                sIndex++;
            }
        }
        setTimeout(function(){
            $(that.id+" .item_backcard").each(function() {
                if(!$(this).hasClass("active")) {
                    $(this).find(".img_par").removeClass("flip");
                }
            });
        }, 800);
        setTimeout(function(){
            if(prize.type != 2){
                $.kingLayer.alert({allContent: $('#zjTemp').html().replace('PRIZES_NAME', prize.name),success:that.reStart});
            }else {
                $.kingLayer.alert({success:that.reStart,allContent: '<div class="bomb_box_text_2 re"><i class="btn_colse_bomb_box_01 btn_close"></i><div class="ico_title_01"></div><div class="mt10 tac"><i class="ico_sad_face"></i></div><p class="mt10 fz14 lh16 tac cor_6">哎呀，没中！摆好姿势再来一次</p><div class="mt20 tac"><a href="javascript:void(0);" class="btn_1 btn_close">再来一次</a></div></div>'});
            }
        }, 2300);
    };
    this.reStart = function(layer){
        $(that.id+" .item_backcard").each(function() {
            $(this).find(".img_par").addClass("flip");
            if($(this).hasClass("active")) {
                $(this).removeClass('active');
                $(this).find('.top_img').attr('src', '/vendor/prizes/images/backface_card.png');
                $(this).find('.open_img').attr('src', '/vendor/prizes/images/frontface_card.png');
                $(this).find('.ico_logo_card').attr('src', '/vendor/prizes/images/ico_bf_1.png');
            }
        });
        layer.close();
        that.isRun = false;
    }
}

function Zjd(id) {
    this.obj = $(id);
    this.id = id;
    this.isRun = false;
    this.prize = {point:-1};
    this.count = 0;
    this.hammerX = $(".hammer_sty_pos")[0].offsetLeft;//获取锤子的横坐标
    this.hammerY = $(".hammer_sty_pos")[0].offsetTop; //获取锤子的纵坐标
    var that = this;
    this.eggBounce = function(){
        var $eggNum = $(that.id+" ul li .init");
        $eggNum.eq(that.count).addClass("jump");
        $eggNum.eq(that.count).parents("li").siblings().find("img").removeClass("jump");
        that.count++;
        if(that.count >= $eggNum.length) { //如果大于彩蛋数，那么count就归零，这样形成循环
            that.count = 0;
        }
    };
    this.init = function(){
        that.eggBounceTimer = setInterval(that.eggBounce, 400);
    };
    this.init();
    $(this.id+' .eggBtn').click(function(){
        if(that.prize.point == -1) {
            return;
        }
        if(that.isRun){
            return;
        }
        that.isRun = true;
        that.start($(this), that.prize);
    });
    this.start = function($btn, prize){

        $(that.id+" ul li").find("img").removeClass("init jump");
        clearInterval(that.eggBounceTimer); //点击砸蛋后，清除蛋跳动的动画
        var  endPosX = $btn.position().left-(that.hammerX-40);
        var  endPosY = $btn.position().top-(that.hammerY+30);
        $(".hammer_sty_pos").css({ //锤子到指定的蛋上面，敲碎菜单
            '-webkit-transition': '-webkit-transform '+.5+'s ease-in',
            'transition': 'transform ' +.5+'s ease-in',
            '-webkit-transform': '-webkit-translate3d('+endPosX +'px,'+endPosY+'px,0px)',
            'transform': 'translate3d('+endPosX+'px,'+endPosY+'px,0px)'
        });
        setTimeout(function() {
            $(".ico_hammer").addClass("zd_chuizi");
            setTimeout(function() { //蛋开裂
                $btn.find("img").attr("src","/vendor/prizes/images/egg_01.png");
                setTimeout(function() { //蛋炸开
                    $btn.find("img").attr("src","/vendor/prizes/images/egg_02.png");
                    $(".dish_img").attr("src","/vendor/prizes/images/ico_dish_01.png"); //刚蛋炸开的时候 装蛋的盘子也改变
                    $(".hammer_sty_pos").css({ //锤子敲开后，回到原位
                        '-webkit-transition': '-webkit-transform '+.5+'s ease-in',
                        'transition': 'transform ' +.5+'s ease-in',
                        '-webkit-transform': '-webkit-translate3d('+0 +'px,'+0+'px,0px)',
                        'transform': 'translate3d('+0+'px,'+0+'px,0px)'
                    });

                    setTimeout(function() {
                        if(prize.status != 0){
                            $.kingLayer.alert({allContent: $('#zjTemp').html().replace('PRIZES_NAME', prize.name),success:that.reStart});
                        }else {
                            $.kingLayer.alert({success:that.reStart,allContent: '<div class="bomb_box_text_2 re"><i class="btn_colse_bomb_box_01 btn_close"></i><div class="ico_title_01"></div><div class="mt10 tac"><i class="ico_sad_face"></i></div><p class="mt10 fz14 lh16 tac cor_6">哎呀，没中！摆好姿势再来一次</p><div class="mt20 tac"><a href="javascript:void(0);" class="btn_1 btn_close">再来一次</a></div></div>'});
                        }
                    },800);

                },300);
            },300);
        },600);

    };
    this.reStart = function(layer){
        $(".dish_img").attr("src","/vendor/prizes/images/ico_dish_00.png");
        $(".egg_location img").attr("src","/vendor/prizes/images/egg_00.png");
        $(that.id+" ul li").find("img").addClass("init");
        $(".hammer_sty_pos").removeAttr('style');
        $(".ico_hammer").removeClass("zd_chuizi");
        layer.close();
        that.count=0;
        that.prize = {point:-1};
        that.isRun = false;
        that.init();
        $(that.id+' .drawBtn').show();
    }
}
var Eraser = (function(window,undefined){
    var canvas = document.createElement("canvas"),ctx = canvas.getContext("2d"),start = 'touchmove',move = 'mousemove',end = 'mouseup',param,x,y,oLeft,oTop,oWidth,oHeight,
        defaults = {
            maskImg : '',
            maskId : '',
            maskColor : '#bbb',
            limit : 80, //控制刮掉百分之多少，即可开奖
            size : 25,
            once : 1,
            isRun:false,
            prize:null,
            reStart: function(layer, thatParam){
                $('#'+thatParam.maskId).parent().find('canvas').remove();
                thatParam.once = 1;
                var target = document.getElementById(thatParam.maskId);
                canvas = document.createElement("canvas");
                ctx = canvas.getContext("2d");
                oLeft = target.offsetLeft;oTop = target.offsetTop;oWidth = target.offsetWidth;oHeight = target.offsetHeight;
                setStyle(canvas,{
                    position : "absolute",
                    top : oTop + 'px',
                    left : oLeft + 'px'
                });
                canvas.width = oWidth;canvas.height = oHeight;
                generateMask();
                target.parentNode.appendChild(canvas);
                addListeners();
                $('#ggkContent').html('');
                $(".prizes_btn_pos").show();
                layer.close();
            },
            setPrize: function(prize){
                this.prize = prize;
                if(prize.status != 0) {
                    $('#ggkContent').html('<p class="fz18 lh20 cor_4">恭喜您</p><p class="mt15 fz18 lh20 cor_4">刮开<span class="text_val">'+prize.name+'</span></p>');
                } else {
                    $('#ggkContent').html('<p class="fz18 lh20 cor_4">很遗憾</p><p class="mt15 fz18 lh20 cor_4">您没有中奖</span></p>');
                }
            },
            callback : function(){
                var prize = this.prize;
                var that = this;
                var success = function(layer){
                    that.reStart(layer, that);
                }
                if(prize.status != 0){
                    $.kingLayer.alert({allContent: $('#zjTemp').html().replace('PRIZES_NAME', prize.name),success:success});
                }else {
                    $.kingLayer.alert({success:success,allContent: '<div class="bomb_box_text_2 re"><i class="btn_colse_bomb_box_01 btn_close"></i><div class="ico_title_01"></div><div class="mt10 tac"><i class="ico_sad_face"></i></div><p class="mt10 fz14 lh16 tac cor_6">哎呀，没中！摆好姿势再来一次</p><div class="mt20 tac"><a href="javascript:void(0);" class="btn_1 btn_close">再来一次</a></div></div>'});
                }
            }
        };
    return function(maskId,params){
        var lotteryHeight = $("#lottery_h").height();
        $(".prizes_btn_pos").css({
            "height":lotteryHeight+'px',
            "marginTop":-lotteryHeight+'px'
        });
        param = extend({},params,defaults),target = document.getElementById(maskId);
        oLeft = target.offsetLeft;oTop = target.offsetTop;oWidth = target.offsetWidth;oHeight = target.offsetHeight;
        setStyle(canvas,{
            position : "absolute",
            top : oTop + 'px',
            left : oLeft + 'px'
        });
        canvas.width = oWidth;canvas.height = oHeight;
        generateMask();
        target.parentNode.appendChild(canvas);
        addListeners();
        return param;
    }

    function generateMask(){
        if(param.maskImg){
            var im = new Image();
            im.src = param.maskImg;
            im.onload = function(){
                ctx.drawImage(im,0,0,canvas.width,canvas.height);
                ctx.globalCompositeOperation = 'destination-out';
            }
        }else{
            ctx.fillStyle = param.maskColor;
            ctx.fillRect(0,0,canvas.width,canvas.height);
            ctx.globalCompositeOperation = 'destination-out';
        }
        ctx.lineJoin = 'round';
        ctx.lineWidth = param.size;
        ctx.strokeStyle = param.maskColor;
    }

    function onStart(e){
        e.preventDefault();


        var touch = e.touches[0];

        x = touch.pageX - oLeft;
        y = touch.pageY - oTop;

        ctx.beginPath();
        ctx.arc(x,y,param.size/2,0,2*Math.PI,true);
        ctx.closePath();
        ctx.fill();
        ctx.stroke();
        check();
        //canvas.addEventListener(move,onMove,false);
    }

    function onMove(e){
        ctx.beginPath();
        ctx.moveTo(x,y);
        ctx.lineTo(e.pageX - oLeft,e.pageY - oTop);
        x = e.pageX - oLeft;y = e.pageY - oTop;
        ctx.closePath();
        ctx.stroke();
        check();
    }

    function onEnd(){
        canvas.removeEventListener(move,onMove);
    }

    function check(){
        var data = ctx.getImageData(0,0,canvas.width,canvas.height).data,k=0;
        for(var i=0,len=data.length;i<len;i+=4){
            data[i]===0 && data[i+1]===0 && data[i+2]===0 && data[i+3]===0 && k++;
        }
        var f = 100*k/(canvas.width*canvas.height);
        if(f>=param.limit){
            if(param.once == 1) {
                param.once = 0;
                param.callback();
            }
            ctx.clearRect(0,0,canvas.width,canvas.height);

        }
    }

    function addListeners(){
        canvas.addEventListener(start,onStart,false);
    }

    function extend(res,ex,orig){
        for(var key in orig){
            res[key] = ex[key] ? ex[key] : orig[key];
        }
        return res;
    }

    function setStyle(target,setting){
        for(var key in setting){
            target.style[key] = setting[key];
        }
    }
})(window);