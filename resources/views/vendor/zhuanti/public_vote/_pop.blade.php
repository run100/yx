<!--为TA点赞投票-弹窗-->
<div class="bomb_box2 bombbox-vote dn" id = "bomb_box2_1">
    <div class="bomb_box_text">
        <div class="boxContent tac" style="@if($proj->id != 81)height: 300px;@else height: 150px; @endif">
            <i class="btn_colse_bomb_box"></i>
            <p class="lh18 cor_3 fz13">
                @if($proj->id ==81)
                系统已自动复制了编号“{{$players_pre}}<span id="copy_vote_num"><span class="voteno">&nbsp;</span></span>”,在微信中搜索公众号“万家爸妈学堂”进入后,在底部长按输入框，选择粘贴-回复，即可点赞成功
                @else
                系统已自动复制了编号“{{$players_pre}}<span id="copy_vote_num"><span class="voteno">&nbsp;</span></span>”,长按识别下方二维码，在公众号底部长按输入框，选择粘贴-回复，即可投票
                @endif
            </p>
            @if($proj->id != 81)
            <div class="mt5" style="height: 220px; overflow-y:auto">
                <div class="mb15 tac">
                    <img src="{{ isset($proj->configs->vote->wechat_img) ? uploads_url($proj->configs->vote->wechat_img) : '' }}" class="img1">
                </div>
                @if(isset($proj->configs->vote->wechat_img2))
                <div class="mb15 tac">
                    <img src="{{ isset($proj->configs->vote->wechat_img2) ? uploads_url($proj->configs->vote->wechat_img2) : '' }}" class="img1">
                </div>
                @endif
            </div>
            @endif
            <p class="mt10 fz14 lh14 cor_4 tac">活动期间每个公众号
                {{$proj->configs->vote->limit_daily>0 ? '每人每天可投'.$proj->configs->vote->limit_daily.'票':'' }}
                {{$proj->configs->vote->limit_all>0 ? '整个活动可投'.$proj->configs->vote->limit_all.'票':'' }}
            </p>
        </div>
    </div>
</div>

<!--为TA点赞时间未开始时提醒框-->
<div class="bomb_box2 bombbox-vote dn" id = "bomb_box2_2">
    <div class="bomb_box_text">
        <i class="btn_colse_bomb_box"></i>
        <p class="lh18 cor_3">当前时间不在投票时段内！请在规定时间内投票<br>投票开始时间：{{$proj->configs->vote->stime}}<br>投票结束时间：{{$proj->configs->vote->etime}}<br></p>
    </div>
</div>

<input type="hidden" value="{{strtotime($proj->configs->vote->stime)}}" id="begin_time">
<input type="hidden" value="{{strtotime($proj->configs->vote->etime)}}" id="end_time">
<script>
    var clipboard = new Clipboard('.zhuli', {
        text: function(e) {
            var num = '{{$players_pre}}'+$(e).data('num').replace(/"/g, '')
            return num;
        }
    });

    clipboard.on('success', function(e) {

    });

    clipboard.on('error', function(e) {
        alert('sorry～ 自动复制失败请手动复制');
    });
</script>
<script type="text/javascript">
    function showVoteDialog(voteid)
    {
        var begin_time = $('#begin_time').val();
        var end_time = $('#end_time').val();
        var current_time = new Date().getTime();

        if(current_time/1000 > begin_time && current_time/1000 < end_time) {
            var $vote = $('#bomb_box2_1');
            $vote.find('.voteno').text(voteid);
            $("#bomb_box2_1").bombbox();
        }else{
            $("#bomb_box2_2").bombbox();
        }
    }
    $(function() {
        $(".btn_colse_bomb_box").tap(function(){
            $.bombbox.hide2();
        })
        $('#search_btn_sty').click(function () {
            $('#topsearch').submit();
        });
    })

</script>