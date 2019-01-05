<div class="box box-primary">
    <!-- /.box-header -->
    <div class="btn-group">
        <div class="btn-group pull-left" style="margin-right: 10px">
            @if($project->can('jizi'))<a href="{{route('jizi.setting', ['project_id' => $project->id])}}" class="btn btn-sm btn-default"><i class="fa fa-font"></i>&nbsp;字/图配置</a>@endif
            <a href="{{route('prizes.setting', ['project_id' => $project->id])}}" class="btn btn-sm btn-default"><i class="fa fa-safari"></i>&nbsp;奖品配置</a>
            <a href="{{route('prizes.majia', ['project_id' => $project->id])}}" class="btn btn-sm btn-default"><i class="fa fa-drupal"></i>&nbsp;中奖马甲</a>
        </div>
    </div>
    <div class="box-tools">
        <div class="btn-group pull-right" style="margin-right: 10px">
            <a href="{{route('projects.index')}}" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;返回列表</a>
        </div>
    </div>
    <div class="box-body">
        <form method="post" action="{{route('jizi.setting', ['project_id' => $project->id])}}" id="scaffold" pjax-container>
            <div class="box-body">
                <h4>集字/图配置</h4>
                <p>项目时间：{{$project->configs->jizi->stime}} ~ {{$project->configs->jizi->etime}}</p>
                <table class="table table-hover" id="table-fields">
                    <tbody>
                    <tr>
                        <th style="width: 200px">字段名称</th>
                        <th style="width: 200px">是否限量</th>
                        <th>字的总数</th>
                        <th>时间</th>
                        <th style="width: 200px">操作</th>
                    </tr>
                    @if(old('jizi_fields'))
                        @foreach(old('jizi_fields') as $index => $field)
                            <tr>
                                <td>
                                    <input type="hidden" class="key-ipt" name="fields[{{$index}}][key]" value="{{$field['key']}}">
                                    <input type="text" name="fields[{{$index}}][name]" class="form-control" placeholder="名称" value="{{$field['name']}}" />
                                </td>
                                <td>
                                    <select style="width: 150px" class="sel-key form-control" name="fields[{{$index}}][is_limit_count]" id="is_limit_count" onchange="show_sub(this)">
                                        @foreach($isLimitCount as $k => $v)
                                            <option value="{{ $k }}" {{@$field['is_limit_count'] == $k ? 'selected' : '' }}>{{$v}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="fields[{{$index}}][total]" class="form-control is_hidden" placeholder="字的总数" value="{{$field['total']}}" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/>
                                </td>
                                <td>
                                    @php
                                        if($field['timeplan']) {
                                           $plans = @$field['timeplan']['plans'] ?? null;
                                           echo '<div class="col-sm-12 is_hidden">';
                                           if ($plans) {
                                               foreach ($plans as $k => $val) {
                                               echo '<div class="row" style="width: 390px">
                                                        <div class="col-lg-6">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                <input type="text" name="fields['.$index.'][timeplan]['.$k.'][start]" value="'.(!empty($val['start'])?date('Y-m-d H:i:s', $val['start']):'').'" class="form-control start_at" style="width: 160px">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                <input type="text" name="fields['.$index.'][timeplan]['.$k.'][end]" value="'.(!empty($val['end'])?date('Y-m-d H:i:s', $val['end']):'').'" class="form-control end_at" style="width: 160px">
                                                            </div>
                                                        </div>
                                                    </div>';
                                              }
                                          }
                                        }
                                    echo '</div></td><td>
                                            <button type="button" class="btn btn-sm btn-success  is_hidden" id="time_add" data-index="'.$index.'" onclick="add_time_palns(this)"><i class="fa fa-plus"></i>&nbsp;&nbsp;增加时间</button>
                                            <button type="button" class="btn btn-sm btn-danger del-btn">&nbsp;&nbsp;删除</button>';
                                    @endphp
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                <input type="hidden" class="key-ipt" name="fields[0][key]" value="jz_01">
                                <input type="text" name="fields[0][name]" class="form-control" placeholder="名称" />
                            </td>
                            <td>
                                <select style="width: 150px" class="sel-key form-control" name="fields[0][is_limit_count]" id="is_limit_count" onchange="show_sub(this)">
                                    @foreach($isLimitCount as $k => $v)
                                        <option value="{{ $k }}" {{ 'N'== $k ? 'selected' : '' }}>{{$v}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" style="display: none" name="fields[0][total]" class="form-control is_hidden" placeholder="字的总数" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/>
                            </td>
                            <td>
                                <div class="col-sm-12 is_hidden" style="display: none">
                                    <div class="row" style="width: 390px">
                                        <div class="col-lg-6">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" name="fields[0][timeplan][0][start]" value="{{$project->configs->jizi->stime}}" class="form-control start_at" style="width: 160px">
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" name="fields[0][timeplan][0][end]" value="{{$project->configs->jizi->etime}}" class="form-control end_at" style="width: 160px">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-success is_hidden" style="display: none" id="time_add" data-index="0" onclick="add_time_palns(this)"><i class="fa fa-plus"></i>&nbsp;&nbsp;增加时间</button>
                                <button type="button" class="btn btn-sm btn-danger del-btn">&nbsp;&nbsp;删除</button>
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
                <button type="button" class="btn btn-info pull-right">提交</button>
            </div>

        {{ csrf_field() }}

        <!-- /.box-footer -->
        </form>

        <input type="hidden" id="jiziFontType" value="{{$project->configs->jizi->font_type}}">
    </div>

</div>

<template id="time-tpl">
    <div class="row" style="width: 390px">
        <div class="col-lg-6">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" name="fields[__index__][timeplan][__index1__][start]" class="form-control start_at" style="width: 160px">
            </div>
        </div>

        <div class="col-lg-6">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" name="fields[__index__][timeplan][__index1__][end]" class="form-control end_at" style="width: 160px">
            </div>
        </div>
    </div>
    <script>
        $('.start_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN"});

        $('.end_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN","useCurrent":false});
    </script>
</template>

<template id="table-field-tpl">
    <tr>
        <td>
            <input type="hidden" class="key-ipt" name="fields[__index__][key]" value="jz___jzindex__">
            <input type="text" name="fields[__index__][name]" class="form-control" placeholder="名称" />
        </td>
        <td>
            <select style="width: 150px" class="sel-key form-control" name="fields[__index__][is_limit_count]" id="is_limit_count" onchange="show_sub(this)">
                @foreach($isLimitCount as $k => $v)
                    <option value="{{ $k }}" {{ 'N'== $k ? 'selected' : '' }}>{{$v}}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="text" name="fields[__index__][total]" class="form-control is_hidden" style="display: none" placeholder="字的总数" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/>
        </td>
        <td>
            <div class="col-sm-12 is_hidden" style="display: none">
                <div class="row" style="width: 390px">
                    <div class="col-lg-6">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="fields[__index__][timeplan][0][start]" value="{{$project->configs->jizi->stime}}" class="form-control start_at" style="width: 160px">
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="fields[__index__][timeplan][0][end]" value="{{$project->configs->jizi->etime}}" class="form-control end_at" style="width: 160px">
                        </div>
                    </div>
                </div>
            </div>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-success is_hidden" style="display: none" id="time_add" data-index="__index__" onclick="add_time_palns(this)"><i class="fa fa-plus"></i>&nbsp;&nbsp;增加时间</button>
            <button type="button" class="btn btn-sm btn-danger del-btn">&nbsp;&nbsp;删除</button>
        </td>
    </tr>
    <script>
        $('.start_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN"});

        $('.end_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN","useCurrent":false});
    </script>
</template>
<script>
    function add_time_palns(obj)
    {
        var index = $(obj).data('index');
        var $timeContainer = $(obj).parent().prev().find('div.is_hidden');
        var time_index = $timeContainer.children('div.row').size();
        $timeContainer.append(
            $('#time-tpl').html().replace(/__index1__/g, time_index).replace(/__index__/g, index)
        );
    }
    //选择默认不填隐藏后面的表单
    $(function () {
        $("#table-fields select").each(function (index,element){
            var txt = $(this).val();
            if (txt == 'N') {
                $(this).parent().parent().find('.is_hidden').hide();
            }
        });
    });

    function show_sub(obj){
        var v = obj.options[obj.options.selectedIndex].value;
        if (v == 'N') {
            $(obj).parent().parent().find('.is_hidden').hide();
        } else if (v == 'Y') {
            $(obj).parent().parent().find('.is_hidden').show();
        }

    }
</script>
<script>
    var fieldsIndex =  $('#table-fields tr').length - 1;
    $(function () {

        $('#add-table-field').click(function (event) {
            if($('#jiziFontType').val() == 'picture12' && $('#table-fields tr').length>=13){
                swal({
                    title: "集图12类型最多添加12个字段！",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "确认",
                    closeOnConfirm: false,
                    cancelButtonText: "取消"
                });
                return false;
            }
            if($('#jiziFontType').val() == 'picture9' && $('#table-fields tr').length>=10){
                swal({
                    title: "集图9类型最多添加9个字段！",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "确认",
                    closeOnConfirm: false,
                    cancelButtonText: "取消"
                });
                return false;
            }
            fieldsIndex++;
            if ($('#table-fields tr').length < 10) {
                var jz_index = '0'+$('#table-fields tr').length;
//                console.log(jz_index);
            } else {
                var jz_index = $('#table-fields tr').length;
            }
            $('#table-fields tbody').append(
                $('#table-field-tpl').html().replace(/__index__/g, fieldsIndex).replace(/__jzindex__/g, jz_index)
            );
        });

        $('.start_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN"});

        $('.end_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN","useCurrent":false});

        $(".start_at").on("dp.change", function (e) {
            $('.end_at').data("DateTimePicker").minDate(e.date);
        });
        $(".end_at").on("dp.change", function (e) {
            $('.start_at').data("DateTimePicker").maxDate(e.date);
        });

    });
    //删除事件
    $('#table-fields').on('click', '.del-btn', function(){
        var $btn = $(this);
        swal({
            title: "确认删除?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "确认",
            closeOnConfirm: false,
            cancelButtonText: "取消"
        }).then(function(ok){
            if (!ok) {
                return false;
            }
            $btn.parents('tr:eq(0)').remove();
            //重新赋值 key
            $('#table-fields .key-ipt').each(function(index, e){
                var key = index > 8 ? 'jz'+(index+1) : 'jz_0'+(index+1);
                $(e).val(key);
            });

        });
    });

    $('.btn-info').click(function () {
        if($('#jiziFontType').val() == 'picture12' && $('#table-fields tr').length<13){
            swal({
                title: "集图12类型的活动必须添加12个！",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                closeOnConfirm: false,
                cancelButtonText: "取消"
            });
            return false;
        }
        if($('#jiziFontType').val() == 'picture9' && $('#table-fields tr').length<10){
            swal({
                title: "集图9类型的活动必须添加9个！",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                closeOnConfirm: false,
                cancelButtonText: "取消"
            });
            return false;
        }
        var hasDefault = false;
        var isEmpty = false;
        var isNameEmpty = false;
        $("#table-fields select").each(function (){
            if ($(this).val() == 'N' && !hasDefault) {
               hasDefault = true;
            }
            if($(this).val() == 'Y' &&!isEmpty) {
                var count = $(this).parent().next().children('input').val();
                isEmpty = count == "" ? true : (parseInt(count) <= 0);
            }
            if($(this).parent().prev().find('input:eq(1)').val() == '' && !isNameEmpty){
                isNameEmpty = true;
            }
        });

        if(!hasDefault) {
            swal({
                title: "请至少设置一个不限量的字！",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                closeOnConfirm: false,
                cancelButtonText: "取消"
            });
            return false;
        }
        if(isEmpty) {
            swal({
                title: "限量的字，投放量必须填写且大于0！",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                closeOnConfirm: false,
                cancelButtonText: "取消"
            });
            return false;
        }
        if(isNameEmpty) {
            swal({
                title: "字段名称必须填写！",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                closeOnConfirm: false,
                cancelButtonText: "取消"
            });
            return false;
        }
        $(this).html("<i class='fa fa-spinner fa-spin'></i>");
        $(this).append("<span>提交</span>");
        $('#scaffold').submit();
    })
</script>
