<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        @php
        $current = old($column, $value);
        if ($current) {
            $city_info = wj_city_info($current);
        } else {
            $city_info = $options;
        }

        @endphp

        <div class="wj-city-group">
            <input type="hidden" name="{{$name}}" value="{{$current}}"/>
            <select class="wj-city-part" data-level="1" data-value="{{@$city_info['country']}}">
                <option value="">--请选择--</option>
            </select>
            <select class="wj-city-part" data-level="2" data-value="{{@$city_info['province']}}">
                <option value="">--请选择--</option>
            </select>
            <select class="wj-city-part" data-level="3" data-value="{{@$city_info['city']}}">
                <option value="">--请选择--</option>
            </select>
            <select class="wj-city-part" data-level="4" data-value="{{@$city_info['region']}}">
                <option value="">--请选择--</option>
            </select>

            <select class="wj-city-part" style="display:none" data-level="-1">
                <option value="">--请选择--</option>
                @foreach(wj_city_data(true) as $city)
                    <option data-parent="{{$city['parent']}}" value="{{$city['id']}}">{{$city['name']}}</option>
                @endforeach
            </select>
        </div>

        @include('admin::form.help-block')

    </div>
</div>
