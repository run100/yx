$(function () {
    var pageHtml = $('#pageHtml').val();
    switch (pageHtml) {
        case 'baoming':
            $('#bmBtn').on('click', function () {
                $.kingForm.create('regForm').ajaxCommit();
            });
            setJSAPI();
            break;
        case 'index':
            var fenye = new Fenye($('#project').data('path')+'/wins');
            fenye.getData(1);
            setJSAPI();
            $(".bombBoxWrap").find(".btn_2,.boxCloseBtn").click(function() {
                $(this).parents(".bombBoxWrap").hide();
            });
            $('#shareBtn').click(function(){$('#shareAlert').show()});
            $("#shareAlert").click(function(){$(".share").hide()});
            var isCommit = false;
            $('#drawBtn').click(function(){
                if ($('#isPrize').val() == '0') {
                    $.kingLayer.alert({title:'未抽奖', content:'您还未集满，快邀请好友为你助力吧！'});
                } else {
                    location.href = $('#project').data('path')+'/prize_index';
                }
            });
            $('.page_item').click(function(){
                var type = parseInt($(this).data('type'));
                if(type>0) {
                    fenye.getData(type);
                }
            });
            break;
        case 'player':
            var fenye = new Fenye($('#project').data('path')+'/wins');
            new Clipboard('#shareBtn', {
                text: function(trigger) {
                    return $('#shareBtn').data('content');
                }
            });
            var layer;
            $('#shareBtn').click(function(){
                layer = $.kingLayer.alert({showBtn:false,text_style:'tal',allMsg:$('#playerTemp').html()});
            });
            $('body').on('click', '.btn_colse_bomb_box', function(){
                if(layer) {
                    layer.close();
                }
            });
            fenye.getData(1);
            $('.page_item').click(function(){
                var type = parseInt($(this).data('type'));
                if(type>0) {
                    fenye.getData(type);
                }
            });
            setJSAPI();
            break;
    }
});