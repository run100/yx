<div class="box box-primary">
    <!-- /.box-header -->
    <div class="box-tools">
        <div class="btn-group pull-right" style="margin-right: 10px">
            <a href="{{route('projects.index')}}" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;返回列表</a>
        </div>
    </div>
    <div class="box-body">

        <form method="post" action="{{$action}}" id="scaffold" pjax-container>

            <div class="box-body">
                <h4>字段</h4>
                <p>1）姓名字段的key值默认填写name；   2）上传图片字段的key值默认填写img；  3）openid为必填，唯一标识   4）vote 票额，后台列表显示</p>

                <table class="table table-hover" id="table-fields">
                    <tbody>
                    <tr>
                        <th style="width: 100px">Key</th>
                        <th style="width: 200px">字段名称</th>
                        <th>类型</th>
                        <th>必填</th>
                        <th>索引</th>
                        <th>默认值</th>
                        <th>输入提示</th>
                        <th>操作</th>
                    </tr>

                    @if(old('fields'))
                        @foreach(old('fields') as $index => $field)
                            <tr data-options="@jsonattr(@$field['options'])">
                                <td>
                                    <input class="txt-options" type="hidden" name="fields[{{$index}}][options]" value="" />
                                    <input type="text" name="fields[{{$index}}][field]" class="form-control" placeholder="Key" value="{{$field['field']}}" />
                                </td>
                                <td>
                                    <input type="text" name="fields[{{$index}}][name]" class="form-control" placeholder="名称" value="{{$field['name']}}" />
                                </td>
                                <td>
                                    <select style="width: 180px" class="sel-type" name="fields[{{$index}}][type]">
                                        <option value="">---请选择---</option>
                                        @foreach($dbTypes as $k => $v)
                                            <option value="{{ $k }}" {{$field['type'] == $k ? 'selected' : '' }}>{{$v}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="checkbox" name="fields[{{$index}}][required]" {{ array_get($field, 'required') == 'on' ? 'checked': '' }}/></td>
                                <td>
                                    <select style="width: 150px" class="sel-key" name="fields[{{$index}}][key]">
                                        <option value="">无</option>
                                        @foreach($dbIndexes as $k => $v)
                                            <option value="{{ $k }}" {{$field['key'] == $k ? 'selected' : '' }}>{{$v}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" class="form-control" placeholder="默认值" name="fields[{{$index}}][default]" value="{{$field['default']}}"/></td>
                                <td><input type="text" class="form-control" placeholder="输入提示" name="fields[{{$index}}][comment]" value="{{$field['comment']}}" /></td>
                                <td>
                                    <a class="btn btn-sm btn-info table-field-sets"><i class="fa fa-cog"></i> 设置</a>
                                    <a class="btn btn-sm btn-success table-field-setting"><i class="fa fa-cog"></i> 配置</a>
                                    <a class="btn btn-sm btn-danger table-field-remove"><i class="fa fa-trash"></i> 删除</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                <input class="txt-options" type="hidden" name="fields[0][options]" value="" />
                                <input type="text" name="fields[0][field]" class="form-control" placeholder="Key" />
                            </td>
                            <td>
                                <input type="text" name="fields[0][name]" class="form-control" placeholder="名称" />
                            </td>
                            <td>
                                <select style="width: 180px" class="sel-type" name="fields[0][type]">
                                    <option value="">---请选择---</option>
                                    @foreach($dbTypes as $k => $v)
                                        <option value="{{ $k }}">{{$v}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="checkbox" name="fields[0][required]" /></td>
                            <td>
                                <select style="width: 150px" class="sel-key" name="fields[0][key]">
                                    <option value="">无</option>
                                    @foreach($dbIndexes as $k => $v)
                                        <option value="{{ $k }}">{{$v}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" class="form-control" placeholder="默认值" name="fields[0][default]"></td>
                            <td><input type="text" class="form-control" placeholder="输入提示" name="fields[0][comment]"></td>
                            <td>
                                <a class="btn btn-sm btn-info table-field-sets"><i class="fa fa-cog"></i> 设置</a>
                                <a class="btn btn-sm btn-success table-field-setting"><i class="fa fa-cog"></i> 配置</a>
                                <a class="btn btn-sm btn-danger table-field-remove"><i class="fa fa-trash"></i> 删除</a>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>

                <hr style="margin-top: 0;"/>

                <div class='form-inline margin' style="width: 100%">


                    <div class='form-group'>
                        <button type="button" class="btn btn-sm btn-success" id="add-table-field"><i class="fa fa-plus"></i>&nbsp;&nbsp;新增</button>
                    </div>

                </div>

            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" class="btn btn-info pull-right">提交</button>
            </div>

        {{ csrf_field() }}

        <!-- /.box-footer -->
        </form>

    </div>

</div>

<template id="table-field-tpl">
    <tr>
        <td>
            <input class="txt-options" type="hidden" name="fields[__index__][options]" value="" />
            <input type="text" name="fields[__index__][field]" class="form-control" placeholder="Key" />
        </td>
        <td>
            <input type="text" name="fields[__index__][name]" class="form-control" placeholder="名称" />
        </td>
        <td>
            <select style="width: 180px" class="sel-type" name="fields[__index__][type]">
                <option value="">---请选择---</option>
                @foreach($dbTypes as $k => $v)
                    <option value="{{ $k }}">{{$v}}</option>
                @endforeach
            </select>
        </td>
        <td><input type="checkbox" name="fields[__index__][required]" /></td>
        <td>
            <select style="width: 150px" class="sel-key" name="fields[__index__][key]">
                <option value="">无</option>
                @foreach($dbIndexes as $k => $v)
                    <option value="{{ $k }}">{{$v}}</option>
                @endforeach
            </select>
        </td>
        <td><input type="text" class="form-control" placeholder="默认值" name="fields[__index__][default]"></td>
        <td><input type="text" class="form-control" placeholder="输入提示" name="fields[__index__][comment]"></td>
        <td>
            <a class="btn btn-sm btn-info table-field-sets"><i class="fa fa-cog"></i> 设置</a>
            <a class="btn btn-sm btn-success table-field-setting"><i class="fa fa-cog"></i> 配置</a>
            <a class="btn btn-sm btn-danger table-field-remove"><i class="fa fa-trash"></i> 删除</a>
        </td>
    </tr>
</template>


<template class="tmp-option-form-string">
    <div class="text-left">
        <h4>字段配置</h4>
        <form accept-charset="UTF-8" class="form-horizontal">
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                            <input type="text" id="opt_regexp" name="regexp" value="" class="form-control" placeholder="正则表达式" />
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                            <input type="text" id="opt_errmsg" name="errmsg" value="" class="form-control" placeholder="验证失败时提示" />
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<template class="tmp-option-form-integer">
    <div class="text-left">
        <h4>字段配置</h4>
        <form accept-charset="UTF-8" class="form-horizontal">
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                            <input type="number" id="opt_min" name="min" value="" class="form-control" placeholder="最小值" />
                        </div>
                    </div>
                    <span class="col-sm-1 text-center" style="line-height: 30px;">-</span>
                    <div class="form-group col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                            <input type="number" id="opt_max" name="max" value="" class="form-control" placeholder="最大值" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                            <input type="text" id="opt_errmsg" name="errmsg" value="" class="form-control" placeholder="验证失败时提示" />
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>


<template class="tmp-option-form-age tmp-option-form-idcard tmp-option-form-birthday">
    <div class="text-left">
        <h4>字段配置</h4>
        <form accept-charset="UTF-8" class="form-horizontal">
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-sm-5">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                            <input type="number" id="opt_min" name="min" value="" class="form-control" placeholder="最小年龄" min="0" max="200" />
                        </div>
                    </div>
                    <span class="col-sm-1 text-center" style="line-height: 30px;">-</span>
                    <div class="form-group col-sm-5">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                            <input type="number" id="opt_max" name="max" value="" class="form-control" placeholder="最大年龄" min="0" max="200" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                            <input type="text" id="opt_errmsg" name="errmsg" value="" class="form-control" placeholder="年龄超限时提示" />
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<template class="tmp-option-form-datetime">
    <div class="text-left">
        <h4>字段配置</h4>
        <form accept-charset="UTF-8" class="form-horizontal">
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="input-group">
                            选择方式:<br/>
                            <input type="radio" name="input_type" value="datetime" /> 时间选择 &emsp;
                            <input type="radio" name="input_type" value="range" /> 时间范围 &emsp;
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="input-group">
                            字段组成:<br/>
                            <input type="checkbox" name="datetime_type" value="date" /> 日期 &emsp;
                            <input type="checkbox" name="datetime_type" value="time" /> 时间 &emsp;
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<template class="tmp-option-form-passport">
    <div class="text-left">
        <h4>字段配置</h4>
        <form accept-charset="UTF-8" class="form-horizontal">
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="input-group">
                            证件类型:<br/>
                            @foreach(\App\Models\Player::getModel()->listPassportType() as $k => $v)
                                <input type="checkbox" name="passport_type" value="{{$k}}" /> {{$v}} &emsp;
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                            <input type="number" id="opt_min" name="min" value="" class="form-control" placeholder="最小年龄" min="0" max="200" />
                        </div>
                    </div>
                    <span class="col-sm-1 text-center" style="line-height: 30px;">-</span>
                    <div class="form-group col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                            <input type="number" id="opt_max" name="max" value="" class="form-control" placeholder="最大年龄" min="0" max="200" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                            <input type="text" id="opt_errmsg" name="errmsg" value="" class="form-control" placeholder="身份证年龄超限时提示" />
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<template class="tmp-option-form-select">
    <div class="text-left">
        <h4>字段配置</h4>
        <form accept-charset="UTF-8" class="form-horizontal">
            <div class="box-body">

                <div class="row">
                    <div class="col-sm-9">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                <input type="text" id="opt_blank" name="blank_text" value="" class="form-control" placeholder="为空时显示" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                <input type="text" id="opt_key" name="key" value="" class="form-control" placeholder="保存为" />
                            </div>
                        </div>
                    </div>
                    <span class="col-sm-1 text-center" style="line-height: 30px;">:</span>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                <input type="text" id="opt_name" name="name" value="" class="form-control" placeholder="显示为" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button class="btn btn-xs btn-default btn-add"><i class="fa fa-arrow-down"></i> 新增</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-9">
                        <div class="form-group">
                            <select name="options" multiple="multiple" style="width:100%; height: 200px;">
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button class="btn btn-xs btn-default btn-up"><i class="fa fa-sort-up"></i> 上移</button>
                        <button class="btn btn-xs btn-default btn-down"><i class="fa fa-sort-down"></i> 下移</button>
                        <button class="btn btn-xs btn-success btn-def"><i class="fa fa-check-circle"></i> 选中</button>
                        <button class="btn btn-xs btn-warning btn-undef"><i class="fa fa-circle-o"></i> 反选</button>
                        <button class="btn btn-xs btn-danger btn-del"><i class="fa fa-times"></i> 删除</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>



<template class="tmp-option-form-checkbox tmp-option-form-radio">
    <div class="text-left">
        <h4>字段配置</h4>
        <form accept-charset="UTF-8" class="form-horizontal">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                <input type="text" id="opt_key" name="key" value="" class="form-control" placeholder="保存为" />
                            </div>
                        </div>
                    </div>
                    <span class="col-sm-1 text-center" style="line-height: 30px;">:</span>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                <input type="text" id="opt_name" name="name" value="" class="form-control" placeholder="显示为" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button class="btn btn-xs btn-default btn-add"><i class="fa fa-arrow-down"></i> 新增</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-9">
                        <div class="form-group">
                            <select name="options" multiple="multiple" style="width:100%; height: 200px;">
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button class="btn btn-xs btn-default btn-up"><i class="fa fa-sort-up"></i> 上移</button>
                        <button class="btn btn-xs btn-default btn-down"><i class="fa fa-sort-down"></i> 下移</button>
                        <button class="btn btn-xs btn-success btn-def"><i class="fa fa-check-circle"></i> 选中</button>
                        <button class="btn btn-xs btn-warning btn-undef"><i class="fa fa-circle-o"></i> 反选</button>
                        <button class="btn btn-xs btn-danger btn-del"><i class="fa fa-times"></i> 删除</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>



<template class="tmp-option-form-upload">
    <div class="text-left">
        <h4>字段配置</h4>
        <form accept-charset="UTF-8" class="form-horizontal">
            <div class="box-body">

                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="input-group">
                            上传方式:<br/>
                            <input type="radio" name="input_type" value="normal" /> 普通上传控件 &emsp;
                            <input type="radio" name="input_type" value="weixin_photo" /> 微信拍照 &emsp;
                            <input type="radio" name="input_type" value="weixin_album" /> 微信相册 &emsp;
                            <input type="radio" name="input_type" value="weixin" /> 微信拍照/相册 &emsp;
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-5">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                            <input type="number" id="opt_min" name="min" value="" class="form-control" placeholder="文件大小不低于KB" min="0" max="8192" />
                        </div>
                    </div>
                    <span class="col-sm-1 text-center" style="line-height: 30px;">-</span>
                    <div class="form-group col-sm-5">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                            <input type="number" id="opt_max" name="max" value="" class="form-control" placeholder="文件大小不超过KB" min="0" max="8192" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                            <input type="text" id="opt_errmsg" name="errmsg" value="" class="form-control" placeholder="尺寸超限时提示" />
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</template>



<template class="tmp-option-form-city">
    <div class="text-left">
        <h4>字段配置</h4>
        <form accept-charset="UTF-8" class="form-horizontal">
            <div class="box-body">

                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="input-group">
                            最大行政级别:<br/>
                            @foreach($dbCityTypes as $k => $v)
                                <input type="radio" name="max_city_type" value="{{$k}}" /> {{$v}} &emsp;
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="input-group">
                            最小行政级别:<br/>
                            @foreach($dbCityTypes as $k => $v)
                                <input type="radio" name="min_city_type" value="{{$k}}" /> {{$v}} &emsp;
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="input-group">
                            默认选择:<br/>
                            <div class="wj-city-group">
                                <select class="wj-city-part" name="country" data-level="1" data-value="86">
                                    <option value="">--请选择--</option>
                                </select>
                                <select class="wj-city-part" name="province" data-level="2" data-value="340000">
                                    <option value="">--请选择--</option>
                                </select>
                                <select class="wj-city-part" name="city" data-level="3" data-value="340100">
                                    <option value="">--请选择--</option>
                                </select>
                                <select class="wj-city-part" name="region" data-level="4">
                                    <option value="">--请选择--</option>
                                </select>

                                <select class="wj-city-part" style="display:none" data-level="-1">
                                    <option value="">--请选择--</option>
                                    @foreach(wj_city_data(true) as $city)
                                        <option data-parent="{{$city['parent']}}" value="{{$city['id']}}">{{$city['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</template>

<!-- 设置列表显示模板 -->
<template class="tmp-option-form-sets">
    <div class="text-left">
        <h4>字段显示设置</h4>
        <form accept-charset="UTF-8" class="form-horizontal">
            <div class="box-body">

                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="input-group">
                            <p><input type="checkbox" name="option_baoming" value="on" /> 报名信息 (<font color="gray">选择前台报名页的显示字段</font>) &emsp;</p>
                            <p><input type="checkbox" name="option_indexs" value="on" /> 首页显示(<font color="gray">活动首页的信息显示</font>) &emsp;</p>
                            <p><input type="checkbox" name="option_details" value="on" /> 详情页显示(<font color="gray">选手详情页的信息显示</font>) &emsp;</p>
                            <p><input type="checkbox" name="option_lists" value="on" /> 后台列表显示(<font color="gray">后台选手管理页面的列表显示</font>) </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<style type="text/css">
    .swal-wide{
        width:850px !important;
    }
</style>

<script>

    $(function () {
        $('input[type=checkbox]').iCheck({checkboxClass:'icheckbox_minimal-blue'});
        $('select').select2();

        $('#add-table-field').click(function (event) {
            $('#table-fields tbody').append($('#table-field-tpl').html().replace(/__index__/g, $('#table-fields tr').length - 1));
            $('select').select2();
            $('input[type=checkbox]').iCheck({checkboxClass:'icheckbox_minimal-blue'});
        });

        $('#table-fields').on('click', '.table-field-remove', function(event) {
            $(event.target).closest('tr').remove();
        });

        $('#table-fields').on('click', '.table-field-setting', function(event) {
            var tr = $(event.target).closest('tr');
            var selType = tr.find('.sel-type');

            if (['openid', 'name', 'phone', 'gender', 'email', 'qq', 'address', 'vote'].indexOf(selType.val()) >= 0) {
                swal('提示', '抱歉, 所选字段类型不支持配置', 'warning');
                return false;
            }

            loadOptionsForm(tr, selType.val());
        });

        //add by chenfei
        $('#table-fields').on('click', '.table-field-sets', function(event) {
            var tr = $(event.target).closest('tr');
            var selType = tr.find('.sel-type');

            if (selType == '') {
                swal('提示', '抱歉, 所选字段为空', 'warning');
                return false;
            }

            loadOptionsForm(tr, 'sets');
        });


        $('#scaffold').on('submit', function (event) {

            //event.preventDefault();

            if ($('#inputTableName').val() == '') {
                $('#inputTableName').closest('.form-group').addClass('has-error');
                $('#table-name-help').removeClass('hide');

                return false;
            }

            $('.txt-options').each(function() {
                var data = $(this).closest('tr').data('options');
                $(this).val(JSON.stringify(data));
            });

            return true;
        });


        function loadOptionsForm(tr, type) {
            if (!type || type == '') {
                swal('提示', '抱歉, 请先选择字段类型', 'warning');
                return false;
            }

            var options = tr.data('options');
            options = options || {};

            var tmp = $(".tmp-option-form-" + type);

            if (tmp.size() <= 0) {
                swal('提示', '抱歉, 所选字段类型不支持配置', 'warning');
                return false;
            }

            var el = document.importNode(tmp.get(0).content, true).children[0];

            if (type === 'city') {
                if (options[type]) {
                    $('select[name=country]', el).data('value', options[type]['country']);
                    $('select[name=province]', el).data('value', options[type]['province']);
                    $('select[name=city]', el).data('value', options[type]['city']);
                    $('select[name=region]', el).data('value', options[type]['region']);
                }
                initWjCityControl(el);
            }

            if (['checkbox', 'radio', 'select'].indexOf(type) >= 0) {
                var selOptions = options[type] && options[type].options || [];
                selOptions = $.map(selOptions, function(v) {
                    var opt = $('<option></option>');
                    var text = '';
                    if (v.is_default) {
                        text += 'v ';
                    } else {
                        text += '- ';
                    }

                    text += v.key + ':';
                    text += v.name;

                    opt.data('opt', v).text(text);
                    return opt;
                });

                $('select', el).append(selOptions);
                $('.btn-add', el).click(function() {
                    var frm = $(this).closest('form');
                    var frmData = frm.serializeObject();
                    var optData = {is_default: false, key: frmData.key, name: frmData.name};
                    if (!optData.key) {
                        return false;
                    }
                    if (!optData.name) {
                        return false;
                    }

                    var text = '';
                    if (optData.is_default) {
                        text += 'v ';
                    } else {
                        text += '- ';
                    }
                    text += optData.key + ':';
                    text += optData.name;
                    var newOpt = $('<option></option>').data('opt', optData).text(text);
                    $('select', el).append(newOpt);

                    $('#opt_key', el).val('');
                    $('#opt_name', el).val('');

                    return false;
                });
                $('.btn-up', el).click(function() {
                    var frm = $(this).closest('form');
                    var opts = $('select option:selected', frm);
                    var lastNode = null;
                    for (var i = 0; i < opts.size(); i++) {
                        var opt = opts.get(i);
                        var idx = $(opt).index();
                        if (!lastNode) {
                            if (idx > 0) {
                                lastNode = $('select option:eq('+(idx-1)+')', frm).get(0);
                                $(lastNode).before(opt);
                            }
                        } else {
                            $(lastNode).after(opt);
                        }
                        lastNode = opt;
                    }
                    return false;
                });
                $('.btn-down', el).click(function() {
                    var frm = $(this).closest('form');
                    var opts = $('select option:selected', frm);
                    var len = $('select option', frm).size();
                    var lastNode = null;
                    for (var i = opts.size() - 1; i >= 0; i--) {
                        var opt = opts.get(i);
                        var idx = $(opt).index();
                        if (!lastNode) {
                            if (idx < len-1) {
                                lastNode = $('select option:eq('+(idx+1)+')', frm).get(0);
                                $(lastNode).after(opt);
                            }
                        } else {
                            $(lastNode).before(opt);
                        }
                        lastNode = opt;
                    }
                    return false;
                });
                $('.btn-del', el).click(function() {
                    var frm = $(this).closest('form');
                    $('select option:selected', frm).remove();
                    return false;
                });
                $('.btn-def', el).click(function() {
                    var frm = $(this).closest('form');
                    $('select option:selected', frm).each(function() {
                        var opt = $(this).data('opt');
                        var text = $(this).text();
                        opt.is_default = true;
                        $(this).data('opt', opt).text(text.replace(/^-/, 'v'));
                    });
                    return false;
                });
                $('.btn-undef', el).click(function() {
                    var frm = $(this).closest('form');
                    $('select option:selected', frm).each(function() {
                        var opt = $(this).data('opt');
                        var text = $(this).text();
                        opt.is_default = false;
                        $(this).data('opt', opt).text(text.replace(/^v/, '-'));
                    });
                    return false;
                });

            }

            $('form', el).deserialize(options[type] || {}).submit(function() {
                options[type] = $(this).serializeObject();

                if (['checkbox', 'radio', 'select'].indexOf(type) >= 0) {
                    delete options[type].key;
                    delete options[type].name;
                    options[type].options = $.map($('select option', this), function(v) {
                        return $(v).data('opt');
                    });

                    var blocking = type !== 'checkbox';
                    var blockingDo = false;
                    for (var i = 0; i < options[type].options.length; i++) {
                        var opt = options[type].options[i];
                        if (!blockingDo && opt.is_default) {
                            blockingDo = true;
                            continue;
                        }
                        if (blocking && blockingDo) {
                            opt.is_default = false;
                        }
                    }
                }

                tr.data('options', options);
                return false;
            });

            swal({
                className: ['upload', 'passport', 'city'].indexOf(type) >= 0 ? 'swal-wide' : '',
                content: el,
                buttons: {
                    save: "保存",
                    cancel: {
                        text: '取消',
                        value: false,
                        visible: true
                    }
                }
            }).then(function(save) {
                if (save) {
                    var frm = $('form', el);
                    frm.trigger('submit');
                }
            });
        }
    });

    function initWjCityControl(el)
    {
        $('.wj-city-group', el).each(function() {
            var group = this;

            //Load country options
            $('.wj-city-part[data-level=1]', group).append(
                $('.wj-city-part[data-level=-1] option[data-parent=00]', group)
            );

            $('.wj-city-part[data-level!=-1]', group).change(function() {
                var lv = $(this).data('level');
                var nextLv = parseInt(lv) + 1;

                //Clear next level options
                $('.wj-city-part[data-level=-1]', group).append(
                    $('.wj-city-part[data-level='+nextLv+'] option[data-parent]', group)
                ).find('option').removeAttr('selected');

                //Reload next level options
                if ($(this).val()) {
                    $('.wj-city-part[data-level='+nextLv+']', group).append(
                        $('.wj-city-part[data-level=-1] option[data-parent='+$(this).val()+']', group)
                    );
                    $('input[type=hidden]', group).val($(this).val());
                }
                $('.wj-city-part[data-level='+nextLv+']', group).val('').trigger('change');

            });

            //Init default selection
            $('.wj-city-part', group).each(function() {
                var initVal = $(this).data('value');
                if (initVal) {
                    $(this).val(initVal).trigger('change');
                }
            });
        });
    }

</script>