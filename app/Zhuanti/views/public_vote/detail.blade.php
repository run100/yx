@extends('vendor.zhuanti.public_vote.layout')

@section('content')
<div class="box">
  <div class="pa10">
    <!--填写信息区域-->
    <div class="mt5 pa15 b1 bgf">
      <div class="flex_box fz14 lh14">
        @foreach($myform as $form)
          @if($form->field == 'name' && isset($player->info_name) && !empty($player->info_name))
          <p class="item cor_2">{{$form->name}}：<span class="mr10 cor_3">{{$player->info_name}}</span></p>
          @endif
        @endforeach
        <p class="cor_2">编号：<span class="mr10 cor_3">{{$players_pre.$player->ticket_no}}</span></p>

      </div>

      <!--类别排名-->
      <div class="mt30 details_type_rank flex_box">
        <div class="item">
          <div class="lh14"><i class="ico_give_like"></i><span class="di ml5 cor_4 fz16 lh16 fwb">{{$player->vote1}}</span></div>
          <p class="mt10 lh14">当前票数</p>
        </div>
        <div class="item bl1">
          <div class="lh14">第<span class="di mar1 cor_4 fz16 lh16 fwb">{{$ranks->rownum}}</span>名</div>
          <p class="mt10 lh14">当前排名</p>
        </div>
      </div><!--类别排名-->

      @foreach($myform as $form)
        @if( !in_array($form->type, ['text', 'upload', 'rich', 'uploads', 'video', 'name']))
          @if ($form->type == 'select')
            @foreach ($form->options->select->options as $op)
              @if( $op->key == $player->{"info_$form->field"})
                <p class="mt10 fz14 cor_2">{{$form->name}}：<span class="mr10 cor_3">{{$op->key == $player->{"info_$form->field"} ? $op->name : ''}}</span></p>
              @endif
            @endforeach
          @else
            <p class="mt10 fz14 cor_2">{{$form->name}}：<span class="mr10 cor_3">{{$player->{"info_$form->field"} ? $player->{"info_$form->field"} : ''}}</span></p>
          @endif
        @endif
      @endforeach

      <div class="mt5">
        @foreach($myform as $form)
          @if( in_array($form->type, ['text', 'rich']))
        <p class="fz14 lh14 cor_2">{{$form->name}}：</p>
        <p class="mt10 fz14 lh18 cor_3">{{ $player->{"info_$form->field"}  }}</p>
          @endif
        @endforeach

        @foreach($myform as $form)
          @if( $form->type == 'upload')
          <div class="mt15"><img src="{{ uploads_url($player->{"info_$form->field"} ) }}" class="max_wp100"></div>
          @endif
        @endforeach
      </div>

      <!--按钮-->
      <div class="mt30 cf ml-15">
        <div class="wp50 fl zhuli"  data-num="@jsonattr($player->ticket_no)"><a href="javascript:showVoteDialog(@jsonattr($player->ticket_no))" class="btn_2" style="{{isset($proj->configs->vote->vote_bgcolor) ? 'background-color:'.$proj->configs->vote->vote_bgcolor.';border:1px solid '.$proj->configs->vote->vote_bgcolor : ''}}">{{isset($proj->configs->vote->vote_btword) ? $proj->configs->vote->vote_btword : '支持 ta' }}</a></div>
        <div class="wp50 fl"><a href="javascript:;" id="share" class="btn_3" style="border: 1px solid {{$proj->configs->vote->vote_bgcolor}}; color: {{$proj->configs->vote->vote_bgcolor}}">{{isset($proj->configs->vote->vote_regword) ? $proj->configs->vote->vote_regword : '我也要参加' }}</a></div>
      </div>

    </div><!--填写信息区域-->
  </div><!--pa10-->

  <!--底部菜单栏-->
  @include('zhuanti::public_vote._nav')


  <!--分享-->
  <div class="share" style="display: none;">
    <div class="sharePic">
      <img src="images/ico_share_tac.png" style="width: 268px; height: 158px;"/>
    </div>
    <div class="hide_ok_pos"><img src="images/hide_ok.png" class="max_wp100"></div>
    <div class="shareBg"></div>
  </div>

  @include('zhuanti::public_vote._pop')

</div><!--box-->
@include('zhuanti::public_vote._detail_share')
<script type="text/javascript" src="{{ URL::asset('public_vote') }}/js/bombbox.1.0.js"></script>
<script type="text/javascript">
  $(function() {
    /*分享*/
    (function() {
        $('#share').click(function () {
            $(".share").show();
        });
      $(".hide_ok_pos").click(function() {
        $(".share").hide();
      });
    } ());

  })
</script>
@endsection