@extends('vendor.zhuanti.public_vote.layout')

@section('content')
<div class="box">
  <div><img src="{{ uploads_url($proj->configs->vote->vote_img) }}" class="max_wp100"></div>
  <div class="pa10">
    <!--填写信息区域-->
    <div class="mt5 bgf">
      <table class="rank_table">
        <colgroup>
          <col style="width: 30%;" />
          <col style="width: 40%" />
          <col style="width: 30%;" />
        </colgroup>
        <tr>
          <td style="{{isset($proj->configs->vote->vote_bgcolor) ? 'color: '.$proj->configs->vote->vote_bgcolor : ''}}">排名</td>
          @foreach($myform as $form)
            @if($form->type == 'name')
              <td style="{{isset($proj->configs->vote->vote_bgcolor) ? 'color: '.$proj->configs->vote->vote_bgcolor : ''}}">姓名</td>
            @endif
          @endforeach
          <td style="{{isset($proj->configs->vote->vote_bgcolor) ? 'color: '.$proj->configs->vote->vote_bgcolor : ''}}">票数</td>
        </tr>
        @foreach($players as $player)
        <tr>
          <td>{{$ranks[$player->id]}}</td>
          @foreach($myform as $form)
            @if($form->type == 'name')
            <td>{{  $player->{"info_$form->field"} ? $player->{"info_$form->field"} : $player->info_name }}</td>
            @endif
          @endforeach
          <td>{{$player->vote1}}</td>
        </tr>
        @endforeach
      </table>
    </div><!--填写信息区域-->

    <!--页码-->
    @include('zhuanti::public_vote._pager')

  </div><!--pa10-->

  <!--底部菜单栏-->
  @include('zhuanti::public_vote._nav')


</div><!--box-->
@include('zhuanti::public_vote._share')
@endsection