<form method="post" id="the-form">

<div class="weui-cells weui-cells_form">
    @foreach($fields as $field)
        @if (isset($field->$step))
        @if($field->type === 'openid')
            <input type="hidden" name="{{$field->field}}" value="" />
        @elseif($field->type === 'name')
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">{{$field->name}}</label></div>
                <div class="weui-cell__bd">
                    <input {{isset($field->required) ? 'required' : '' }} required class="weui-input" type="text" name="{{$field->field}}" placeholder="{{$field->comment ?: "请输入{$field->name}"}}" />
                </div>
                <div class="weui-cell__ft">
                    <i class="weui-icon-warn"></i>
                </div>
            </div>
        @elseif($field->type === 'phone')
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">{{$field->name}}</label></div>
                <div class="weui-cell__bd">
                    <input  {{isset($field->required) ? 'required' : '' }} class="weui-input" type="tel" name="{{$field->field}}" placeholder="{{$field->comment ?: "请输入{$field->name}"}}" />
                </div>
                <div class="weui-cell__ft">
                    <i class="weui-icon-warn"></i>
                </div>
            </div>
        @elseif($field->type === 'gender')

            <div class="weui-cell weui-cells_checkbox" style="padding-top: 0;padding-bottom: 0;">
                <label class="weui-label">{{$field->name}}</label>
                <label class="weui-cell weui-check__label" for="s11">
                    <div class="weui-cell__hd">
                        <input type="radio" class="weui-check" name="{{$field->field}}" id="s11" checked="checked" value="male">
                        <i class="weui-icon-checked"></i>
                    </div>
                    <div class="weui-cell__bd">
                        <p>男</p>
                    </div>
                </label>
                <label class="weui-cell weui-check__label" for="s12">
                    <div class="weui-cell__hd">
                        <input type="radio" name="{{$field->field}}" class="weui-check" id="s12" value="female">
                        <i class="weui-icon-checked"></i>
                    </div>
                    <div class="weui-cell__bd">
                        <p>女</p>
                    </div>
                </label>
            </div>

        @elseif($field->type === 'city')
            <div class="weui-cell weui-cell_select weui-cell_select-after">
                <div class="weui-cell__hd">
                    <label for="" class="weui-label">{{$field->name}}</label>
                </div>
                <div class="weui-cell__bd">
                    <input type="hidden" name="{{$field->field}}" class="city">
                    <input style="height: 45px;" class="select-city weui-input" type="text" readonly placeholder="{{$field->comment ?: "请选择{$field->name}"}}">
                </div>
            </div>



        <select id="cites" style="display: none;">
            @foreach(wj_city_data(true) as $city)
                <option data-parent="{{$city['parent']}}" value="{{$city['id']}}">{{$city['name']}}</option>
            @endforeach
        </select>
        <script>
            var provinces = [];
            $('.select-city').on('click', function () {
                var that = $(this);
                $('#cites').find('[data-parent="86"]').each(function(index, item){
                    provinces.push({
                        label: item.innerText,
                        value: item.value,
                        children: []
                    })
                });
                provinces.forEach(function (province) {
                     $('#cites').find('[data-parent="'+province.value+'"]').each(function(index, city){
                        province.children.push({
                            label: city.innerText,
                            value: city.value,
                            children: []
                        })
                    });
                     province.children.forEach(function (city) {
                         $('#cites').find('[data-parent="'+city.value+'"]').each(function(index, district){
                             city.children.push({
                                 label: district.innerText,
                                 value: district.value,
                             })
                         });
                     })

                })

            weui.picker( provinces, {
                className: 'custom-classname',
                container: 'body',
                defaultValue: [340000, 340100, 340104],
                onConfirm: function (result) {
                    that.parent().find('.city').val(result[2].value);
                    that.val(result[2].label)
                },
                id: 'doubleLinePicker'
            });
            });

        </script>

        @elseif($field->type === 'string')
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">{{$field->name}}</label></div>
                <div class="weui-cell__bd">
                    <input  {{isset($field->required) ? 'required' : '' }} class="weui-input" type="text" name="{{$field->field}}" placeholder="{{$field->comment ?: "请输入{$field->name}"}}" />
                </div>
                <div class="weui-cell__ft">
                    <i class="weui-icon-warn"></i>
                </div>
            </div>
        @elseif($field->type === 'integer')
            <div class="weui-cell">
                <div class="weui-cell_hd">
                    <label class="weui-label">{{$field->name}}</label>
                </div>
                <div class="weui-cell__bd">
                    <input  {{isset($field->required) ? 'required' : '' }} class="weui-input" type="number" name="{{$field->field}}" placeholder="{{$field->comment ?: "请输入{$field->name}"}}" />
                </div>
                <div class="weui-cell__ft">
                    <i class="weui-icon-warn"></i>
                </div>
            </div>
        @elseif($field->type === 'select')
            <div class="weui-cell weui-cell_select weui-cell_select-after">
                <div class="weui-cell_hd">
                    <label class="weui-label">{{$field->name}}</label>
                </div>
                <div class="weui-cell__bd">
                    <select class="weui-select" name="{{$field->field}}">
                        @foreach($field->options->select->options as $option)
                            <option value="{{$option->key}}" {{$option->is_default ? 'selected' : '' }}>{{$option->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @elseif($field->type === 'checkbox')
            <div class="weui-cells__title">{{$field->name}}</div>
            <div class="weui-cells weui-cells_checkbox">
                @foreach($field->options->checkbox->options as $option)
                    <label class="weui-cell weui-check__label">
                        <div class="weui-cell__hd">
                            <input type="checkbox" class="weui-check" name="{{$option->key}}"  {{ $option->is_default ? 'checked': '' }}>
                            <i class="weui-icon-checked"></i>
                        </div>
                        <div class="weui-cell__bd">
                            <p>{{$option->name}}</p>
                        </div>
                    </label>
                @endforeach
            </div>
        @elseif($field->type === 'radio')
            <div class="weui-cells__title">{{$field->name}}</div>
            <div class="weui-cells weui-cells_radio">
                @foreach($field->options->radio->options as $option)
                    <label class="weui-cell weui-check__label" style="position: relative">
                        <div class="weui-cell__hd" style="position: absolute">
                            <input type="radio" class="weui-check" value="{{$option->key}}" name="{{$field->field}}" {{ $option->is_default ? 'checked' : '' }}>
                            <span class="weui-icon-checked"></span>
                        </div>
                        <div class="weui-cell__bd" style="margin-left: 35px;">
                            <p>{{$option->name}}</p>
                        </div>
                    </label>
                @endforeach
            </div>
        @elseif($field->type === 'upload')
            {{--<div class="weui-cell">--}}
                {{--<div class="weui-cell__bd">--}}
                    {{--<div class="weui-uploader">--}}
                        {{--<div class="weui-uploader__hd">--}}
                            {{--<p class="weui-uploader__title">图片上传</p>--}}
                            {{--<div class="weui-uploader__info"></div>--}}
                        {{--</div>--}}
                        {{--<div class="weui-uploader__bd">--}}
                            {{--<ul class="weui-uploader__files" id="uploaderFiles">--}}
                                {{--<li class="weui-uploader__file" style="background-image:url(./images/pic_160.png)"></li>--}}
                                {{--<li class="weui-uploader__file" style="background-image:url(./images/pic_160.png)"></li>--}}
                                {{--<li class="weui-uploader__file" style="background-image:url(./images/pic_160.png)"></li>--}}
                                {{--<li class="weui-uploader__file weui-uploader__file_status" style="background-image:url(./images/pic_160.png)">--}}
                                    {{--<div class="weui-uploader__file-content">--}}
                                        {{--<i class="weui-icon-warn"></i>--}}
                                    {{--</div>--}}
                                {{--</li>--}}
                                {{--<li class="weui-uploader__file weui-uploader__file_status" style="background-image:url(./images/pic_160.png)">--}}
                                    {{--<div class="weui-uploader__file-content">50%</div>--}}
                                {{--</li>--}}
                            {{--</ul>--}}
                            {{--<div class="weui-uploader__input-box">--}}
                                {{--<input id="uploaderInput" class="weui-uploader__input" type="file" accept="image/*" multiple="">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        @elseif($field->type === 'datetime')
            @if($field->options->datetime->input_type == 'range')
                <div class="weui-cell">
                    <div class="weui-cell__hd"><label for="" class="weui-label">{{$field->name}}</label></div>
                    <div class="weui-cell__bd">
                        @if($field->options->datetime->datetime_type == 'date')
                            <input name="{{$field->field}}_start" class="weui-input" type="date" value="">
                        @elseif($field->options->datetime->datetime_type == 'time')
                            <input name="{{$field->field}}_start" class="weui-input" type="time" value="">
                        @else
                            <input name="{{$field->field}}_start" class="weui-input" type="datetime-local" value="">
                        @endif
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd">
                        <label class="weui-label"></label>
                    </div>
                    <div class="weui-cell__bd">
                        @if($field->options->datetime->datetime_type == 'date')
                            <input name="{{$field->field}}_end" class="weui-input" type="date" value="">
                        @elseif($field->options->datetime->datetime_type == 'time')
                            <input name="{{$field->field}}_end" class="weui-input" type="time" value="">
                        @else
                            <input name="{{$field->field}}_end" class="weui-input" type="datetime-local" value="">
                        @endif
                    </div>

                </div>
            @else
            <div class="weui-cell">
                <div class="weui-cell__hd"><label for="" class="weui-label">{{$field->name}}</label></div>
                <div class="weui-cell__bd">
                    @if($field->options->datetime->datetime_type == 'date')
                        <input  {{isset($field->required) ? 'required' : '' }} name="{{$field->field}}" class="weui-input" type="date" value="">
                    @elseif($field->options->datetime->datetime_type == 'time')
                        <input  {{isset($field->required) ? 'required' : '' }} name="{{$field->field}}" class="weui-input" type="time" value="">
                    @else
                        <input  {{isset($field->required) ? 'required' : '' }} name="{{$field->field}}" class="weui-input" type="datetime-local" value="">
                    @endif
                </div>
            </div>
            @endif
        @elseif($field->type === 'vote')
            <div class="weui-cell">
                <div class="weui-cell_hd">
                    <label class="weui-label">{{$field->name}}</label>
                </div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" name="{{$field->field}}" placeholder="{{$field->comment ?: "请输入{$field->name}"}}" />
                </div>
            </div>
        @elseif($field->type === 'idcard')
            <div class="weui-cell">
                <div class="weui-cell_hd">
                    <label class="weui-label">{{$field->name}}</label>
                </div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" name="{{$field->field}}" placeholder="{{$field->comment ?: "请输入{$field->name}"}}" />
                </div>
            </div>
        @elseif($field->type === 'passport')
            <div class="weui-cell weui-cell_select weui-cell_select-after">
                <div class="weui-cell_hd">
                    <label class="weui-label">{{$field->name}}类型</label>
                </div>
                <div class="weui-cell__bd">
                    <select class="weui-select" name="{{$field->field}}_type">
                        @foreach($field->options->passport->passport_type as $option)
                            <option value="{{$option}}">
                                @switch(strtoupper($option))
                                    @case('SFZ')
                                     身份证
                                @break
                                    @case('TBZ')
                                     台胞证
                                    @break
                                    @case('GAT')
                                     港澳台通行证
                                    @break
                                    @case('HUZ')
                                    护照
                                    @break
                                    @endswitch
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell_hd">
                    <label class="weui-label">{{$field->name}}号码</label>
                </div>
                <div class="weui-cell__bd">
                    <input {{isset($field->required) ? 'required' : '' }} class="weui-input" type="text" name="{{$field->field}}" placeholder="{{$field->comment ?: "请输入{$field->name}"}}" />
                </div>
                <div class="weui-cell__ft">
                    <i class="weui-icon-warn"></i>
                </div>
            </div>
        @elseif($field->type === 'birthday')
            <div class="weui-cell">
                <div class="weui-cell_hd">
                    <label class="weui-label">{{$field->name}}</label>
                </div>
                <div class="weui-cell__bd">
                    <input  {{isset($field->required) ? 'required' : '' }} class="weui-input" type="date" name="{{$field->field}}" placeholder="{{$field->comment ?: "请输入{$field->name}" }}" value="">
                </div>
                <div class="weui-cell__ft">
                    <i class="weui-icon-warn"></i>
                </div>
            </div>
        @elseif($field->type === 'age')
            <div class="weui-cell">
                <div class="weui-cell_hd">
                    <label class="weui-label">{{$field->name}}</label>
                </div>
                <div class="weui-cell__bd">
                    <input class="weui-input" name="{{$field->field}}" type="number" placeholder="{{$field->comment ?: "请输入{$field->name}"}}" value="">
                </div>
            </div>
        @elseif($field->type === 'text')
            <div class="weui-cells__title">{{$field->name}}</div>
            <div class="weui-cells weui-cells_form">
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <textarea  maxlength="500" class="long-text weui-textarea" name="{{$field->field}}" placeholder="{{$field->comment ?: "请输入{$field->name}" }}" rows="3"></textarea>
                        <div class="weui-textarea-counter"><span class="length">0</span>/500</div>
                    </div>
                </div>
            </div>
            <script>
                $('.long-text').on('input', function() {
                    $(this).parent().find('.length').text(500-$(this).val().length)
                })
            </script>
        @else
        @endif
        @endif

    @endforeach
        <div class="weui-btn-area">
            <a class="weui-btn weui-btn_primary" href="javascript:" id="submit">确定</a>
        </div>
</div>

</form>
<!--BEGIN toast-->
<div id="toast" style="display: none;">
    <div class="weui-mask_transparent"></div>
    <div class="weui-toast">
        <i class="weui-icon-success-no-circle weui-icon_toast"></i>
        <p class="weui-toast__content">已成功</p>
    </div>
</div>
<!--end toast-->

<script>
    $('#the-form').on('input', 'input[required]', function () {
        if ($(this).val().length > 0) {
            $(this).closest('.weui-cell').removeClass('weui-cell_warn')
        } else {
            $(this).closest('.weui-cell').addClass('weui-cell_warn')
        }

    })
    $('#submit').on('click', function () {
        var missing = false;
        $('#the-form input[required]').each(function (i, e) {
            e = $(e)
            if (e.val() == '') {
                e.closest('.weui-cell').addClass('weui-cell_warn');
                missing = true;
            }
            console.log(e.value)
        });

        if (missing) {
            return false;
        }

        $.ajax({
            type: 'post',
            url: '/lua/baoming/{{$step}}?proj=1&&open_id=aa',
            data: $('#the-form').serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.code === 0) {
                    var $toast = $('#toast');

                    if ($toast.css('display') != 'none') return;

                    $toast.fadeIn(100);
                    setTimeout(function () {
                        $toast.fadeOut(100);
                    }, 2000);
                    $('#the-form').find('input').val('');
                } else {
                    weui_alert(data.msg);
                }


                console.log(data);
            },
            error: function (error) {
                console.log(error);
            }

        })
    });
</script>
