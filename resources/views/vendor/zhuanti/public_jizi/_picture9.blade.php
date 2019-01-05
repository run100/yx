<div class="mt10 mb30">
    <ul class="chart_the_list fl_dib">
        @foreach($fonts as $k=>$v)
        <li @if(isset($jizi[$v->key]) && $jizi[$v->key] > 0 )class="current"@endif><i class="ico_chart_bg ico_chart_0{{$k}}"></i><em>×{{isset($jizi[$v->key]) ? $jizi[$v->key] : 0 }}</em></li>
        @endforeach
    </ul>
</div>