@extends('vendor.zhuanti.public_vote.layout')

@section('content')
<div class="box">
 <div><img src="{{ uploads_url($proj->configs->vote->vote_img) }}" class="max_wp100"></div>
  <div class="pa10">

    <!--搜索框-->
    <div class="mt5 cf">
      <form method="get" action="{{ $proj->path }}" class="cf topsearch" id="topsearch">
      <div class="fl wp80">
          <div class="re">
            <input type="text" class="search_ipt" name="keyword" value="{{Request::get('keyword')}}" placeholder="请输入编号或姓名">
            <i class="ico_search_pos"></i>
          </div>
      </div>
      <div class="ov"><a href="javascript:void(0);" onclick="document.getElementById('topsearch').submit();" class="search_btn_sty"  style="{{isset($proj->configs->vote->vote_bgcolor) ? 'color: white;background-color:'.$proj->configs->vote->vote_btcolor : ''}}">搜索</a></div>
      </form>
    </div>
    <!--ico_search_pos-->

    <!--排序-->
    <div class="sorting_methods_area cf">
      @foreach($orderBtns as $k=>$v)
      <a href="{{ $proj->path }}?orderby={{$k}}" {{ $rawOrderBy == $k ? 'class=current style=color:white;background-color:'.(isset($proj->configs->vote->vote_btcolor) ? $proj->configs->vote->vote_btcolor : '' ) : '' }} >{{$v}}</a>
      @endforeach
    </div>

    <!--内容-->
    <div class="mt10">
      <ul class="fl_dib  ml-10">
        @foreach($players as $player)
        <li class="wp50">
          <div class="competing_photos_sty" >
            @foreach($myform as $form)
              @if($form->type == 'upload')
            <img src="{{ uploads_url($player->{"info_$form->field"} ) }}"  class="max_wp100 jumpDetail" tick="{{$player->ticket_no}}">
              @endif
            @endforeach
            <div class="pa5">
              <div class="jumpDetail" tick="{{$player->ticket_no}}">
                @foreach($myform as $form)
                  @if($form->type != 'upload')
                <p class="mt5 lh14 cor_3">{{ $form->name }}:<span class="ml5">{{  $player->{"info_$form->field"} }}</span></p>
                  @endif
                @endforeach
                <div class="mt10 flex_box lh14" >
                  <p class="item">编号:<span class="ml5">{{$players_pre.$player->ticket_no}}</span></p>
                  <p class="tar"><i class="ico_give_like mr5"></i><span class="cor_4">{{$player->vote1}}</span></p>
                </div>
              </div>
              <!--按钮-->
              <div class="mt10 zhuli"  data-num="@jsonattr($player->ticket_no)"><a href="javascript:showVoteDialog(@jsonattr($player->ticket_no))"  style="{{isset($proj->configs->vote->vote_bgcolor) ? 'background-color: '.$proj->configs->vote->vote_bgcolor : ''}}" class="give_like_btn">{{isset($proj->configs->vote->vote_btword) ? $proj->configs->vote->vote_btword : '支持 ta' }}</a></div>
            </div>

          </div>
        </li>
        @endforeach
      </ul>
    </div>
    <!--内容-->

    @include('zhuanti::public_vote._pager')

  </div><!--pa10-->

  <!--底部菜单栏-->
@include('zhuanti::public_vote._nav')


  <!--为TA点赞投票-弹窗-->
@include('zhuanti::public_vote._pop')


</div><!--box-->

<script type="text/javascript" src="{{ URL::asset('public_vote') }}/js/bombbox.1.0.js"></script>
<script>
  $('.jumpDetail').click(function(){
      window.location.href = $('#projPath').val() + '/detail?id='+$(this).attr('tick');
  })
</script>
@include('zhuanti::public_vote._share')
@endsection