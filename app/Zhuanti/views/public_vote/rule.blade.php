@extends('vendor.zhuanti.public_vote.layout')
@include('zhuanti::public_vote._share')
@section('content')
<div class="box">
  <div><img src="{{ uploads_url($proj->configs->vote->vote_img) }}" class="max_wp100"></div>
  <div class="pa10">
    <!--填写信息区域-->
    <div class="mt5 pa15 b1 bgf">
      <p class="fz18 lh18 cor_3 fwb tac">活动规则</p>
      <div class="mt15 fz14 lh20 cor_3">
        {!! htmlspecialchars_decode($proj->rules) !!}
      </div>
    </div><!--填写信息区域-->
  </div><!--pa10-->

  <!--底部菜单栏-->
  @include('zhuanti::public_vote._nav')


</div><!--box-->
@include('zhuanti::public_vote._share')
@endsection