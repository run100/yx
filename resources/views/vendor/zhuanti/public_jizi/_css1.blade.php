<div class="mt35">
    <ul class="fl_dib list_fz_sty cf">
        @foreach($fonts as $v)
        <li @if(isset($jizi[$v->key]) && $jizi[$v->key] > 0 )class="current"@endif><span>{{$v->name}}</span><em>×{{isset($jizi[$v->key]) ? $jizi[$v->key] : 0 }}</em></li>
        @endforeach
    </ul>
</div>