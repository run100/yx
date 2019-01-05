<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        @php
        $passport_types = \App\Models\Player::getModel()->listPassportType();
        $current = explode(':', old($column, $value));
        if (count($current) === 2) {
            $part1 = $current[0];
            $part2 = $current[1];
        } else {
            $part1 = 'SFZ';
            $part2 = '';
        }

        @endphp

        <div class="input-group wj-passport">
            <input type="hidden" name="{{$name}}" value="{{old($column, $value)}}"/>
            <div class="input-group-btn">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-value="{{$part1}}"><span class="btn-txt">{{$passport_types[$part1]}}</span> <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    @foreach(wj_mask($passport_types, $options['passport_type']) as $k => $v)
                    <li><a href="javascript:;" data-value="{{$k}}">{{$v}}</a></li>
                    @endforeach
                </ul>
            </div><!-- /btn-group -->
            <input type="text" class="form-control" value="{{$part2}}" />
        </div><!-- /input-group -->

        @include('admin::form.help-block')

    </div>
</div>
