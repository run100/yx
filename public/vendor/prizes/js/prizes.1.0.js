$(function() {
    window.requestAnimFrame = (function () {
        return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame || function (callback) {
                window.setTimeout(callback, 1000 / 60)
            }
    })();
    //分享
    setJSAPI();
    //活动规则
    $(".title_fz_sty").click(function() {
        $.kingLayer.alert({allContent:$('#hdgzTemp').html()})
    });
    /*分享*/
    $("#shareBtn").click(function() {
        $(".share").show();
    });
    $(".ico_ok_sty").click(function() {
        $(".share").hide();
    });
    //中奖名单
    var fenye = new Fenye($('#project').data('path')+'/wins');
    fenye.getData(1, false);
    $('.page_item').click(function(){
        var type = parseInt($(this).data('type'));
        if(type>0) {
            fenye.getData(type, false);
        }
    });
    //大转盘进行封装
    var drawType = $('#project').data('type');
    var wheel;
    switch (drawType) {
        case 'dzp':
            wheel = new WheelAdventures('#lottery');
            break;
        case 'jgg':
            wheel = new Jgg('#lottery');
            break;
        case 'fp':
            wheel = new Fp('#lottery');
            break;
        case 'zjd':
            wheel = new Zjd('#lottery');
            break;
        case 'ggk':
            wheel = new Eraser("lottery",{
                maskImg : '/vendor/prizes/images/mark.png',
                maskId : 'lottery'
            });
            break;
    }

    var isDraw = false;
    $(".drawBtn").click(function () {
        var sTime = $('#drawInfo').data('stime');
        var eTime = $('#drawInfo').data('etime');
        var cTime = new Date().format('yyyy-MM-dd hh:mm:ss');
        if (sTime>cTime) {
            $.kingLayer.alert({allContent: '<div class="bomb_box_text_2 re"><div class="mt10 tac"><i class="ico_sad_face"></i></div><p class="mt10 fz14 lh16 tac cor_6">活动还没开始！</p><div class="mt20 tac"><a href="javascript:void(0);" class="btn_1 btn_close">确定</a></div></div>'});
            return;
        }
        if ( eTime < cTime) {
            $.kingLayer.alert({allContent: '<div class="bomb_box_text_2 re"><div class="mt10 tac"><i class="ico_sad_face"></i></div><p class="mt10 fz14 lh16 tac cor_6">活动已结束！</p><div class="mt20 tac"><a href="javascript:void(0);" class="btn_1 btn_close">确定</a></div></div>'});
            return;
        }
        if(drawType=='ggk'){
            var _projectData = $('#project').data();
            if(_projectData.id != undefined && _projectData.id == 239){
                var _isWs = $(document.getElementById("wsTemp").innerHTML).find('input[value=""]').length;
                var _zjCount = $('#zjCount').val();
                if (_projectData.id != undefined && _isWs !=undefined && _zjCount !=undefined && _projectData.id == 239 && _zjCount >= 1 && _isWs != 0) {
                    return $.kingLayer.alert({allContent: '<div class="bomb_box_text_2 re"><div class="mt10 tac"><i class="ico_sad_face"></i></div><p class="mt10 fz14 lh16 tac cor_6">请先完善领奖信息再继续刮奖哦！</p><div class="mt20 tac"><a href="javascript:void(0);" class="btn_1 btn_close">确定</a></div></div>'});
                }
            }
        }
        var $Btn = $(this);
        var drawCount = parseInt($('#drawCount').html());
        if(drawCount<=0) {
            var notDrawCountMsg = $('#project').data('tips') == 'day' ? '您今天的次数已用完，请明天再来！' : '您的抽奖机会已用完，感谢参与！';
            $.kingLayer.alert({allContent: '<div class="bomb_box_text_2 re"><div class="mt10 tac"><i class="ico_sad_face"></i></div><p class="mt10 fz14 lh16 tac cor_6">'+notDrawCountMsg+'</p><div class="mt20 tac"><a href="javascript:void(0);" class="btn_1 btn_close">确定</a></div></div>'});
            return;
        }
        if (isDraw || wheel.isRun) {
            return;
        }
        isDraw = true;
        switch (drawType) {
            case 'jgg':
                wheel.start();
                break;
        }
        $.post($('#project').data('path')+'/draw', function (res) {
            isDraw = false;
            if (res.code == 0) {
                switch (drawType) {
                    case 'dzp':
                        wheel.start(res.data.point);
                        break;
                    case 'jgg':
                        wheel.prize = res.data.point - 1;
                        break;
                    case 'fp':
                        wheel.start($Btn.index(), res.data.point);
                        break;
                    case 'zjd':
                        //wheel.start($Btn, res.data);
                        $Btn.hide();
                        wheel.isRun = false;
                        wheel.prize = res.data;
                        break;
                    case 'ggk':
                        $(".prizes_btn_pos").hide();
                        wheel.setPrize(res.data);
                        break;
                }

                if (res.data.status == 1) {
                    fenye.getData(1, false);
                    var record = '<tr><td><span class="pl10">获得</span></td><td><div>'+res.data.name+'</div></td><td><span class="cor_2">'+ new Date().format("yyyy/MM/dd hh:mm")+'</span></td></tr>';
                    $('#zjRecordTable').append(record);
                    var zjCount = parseInt($('#zjCount').val());
                    $('#zjCount').val(++zjCount);
                }
                $('#drawCount').html(res.data.draw_count);
            } else {
                $.kingLayer.alert({title: '未知00错误', content: '请刷新再试'});
            }
        });
    });
    //完善信息
    if($('#project').data('ws') != 'N') {
        $('#wsInfo').click(function () {
            $.kingLayer.alert({allContent: $('#wsTemp').html()});
        });
        $(document).on('click', '#commitBtn', function(){
            $('#errorMsg').html('请您如实填写真实信息');
            //let alertStyle = '<div class="bomb_box_text_2 re"><div class="mt10 tac"><i class="ico_sad_face"></i></div><p class="mt10 fz14 lh16 tac cor_6">MSG_CONTENT</p><div class="mt20 tac"><a href="javascript:void(0);" class="btn_1 btn_close">确定</a></div></div>';
            var form = $.kingForm.create('wsForm', function(){
                $('#errorMsg').html('提交成功');
                location.reload();
            }, undefined, undefined, undefined, function(msg){
                $('#errorMsg').html(msg);
            });
            form.ajaxCommit();
        });
    }
    //我的中奖记录
    $('#zjBtn').click(function(){
        var zjCount = parseInt($('#zjCount').val());
        if(zjCount<=0){
            $.kingLayer.alert({allContent:$('#notZjRecords').html()});
        } else {
            $.kingLayer.alert({allContent:$('#zjRecordTemp').html()});
        }
    });
    //我的助力记录
    var isGetZhuli = false;
    $('#zlMoreBtn').click(function(){
        if(isGetZhuli) {
            return;
        }
        isGetZhuli = true;
        var zhuliPage = parseInt($('#zlMoreBtn').data('page'));
        $.get($('#project').data('path')+'/zhulis', {page:zhuliPage}, function(res){
            $('#zlMoreBtn').data('page', ++zhuliPage);
            if(res.data.length > 0) {
                var body = '';
                for(var i in res.data){
                    var f = eval('('+res.data[i]+')');
                    body += '<tr><td><span>好友</span><span class="cor_4 mar1">'+f.name+'</span><span>参与了抽奖</span></td></tr>';
                }
                $('#zlTable tbody').append(body);
            }
            if(res.data.length < 10) {
                $('#zlMoreBtn').hide();
            }
            isGetZhuli = false;
        })
    });
})