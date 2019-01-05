<div class="box box-primary">
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

        <form method="post" action="{{route('prizes.setting', ['project_id' => $project->id])}}" id="scaffold" pjax-container>

            <div class="box-body">
                <h4>奖品配置</h4>
                <p>项目时间：{{$project->configs->draw->stime}} ~ {{$project->configs->draw->etime}}</p>
                <p>配置：接口类奖品可配（格式： xx:1,2） 目前支持 </p>
                <p>| -- -- | -- -- 不填 => (与兑奖码(限量)类奖品处理方式一致)</p>
                <p>| -- -- | -- -- common => (与兑奖码(限量)类奖品处理方式一致)</p>
                <table class="table table-hover" id="table-fields">
                    <tbody>
                    <tr>
                        <th>奖品名称</th>
                        <th style="width: 120px">奖品类型</th>
                        <th style="width: 68px">限量</th>
                        <th style="width: 68px">100%</th>
                        <th style="width: 200px">配置</th>
                        <th style="width: 80px">投放量</th>
                        <th>中奖提示</th>
                        <th>时间</th>
                        <th style="width: 138px">操作</th>
                    </tr>

                    @if(old('prizes_fields'))
                        @foreach(old('prizes_fields') as $index => $field)
                            <tr>
                                <td>
                                    <input class="key-ipt" type="hidden" name="fields[{{$index}}][key]" value="{{$field['key']}}">
                                    <input type="text" name="fields[{{$index}}][name]" class="form-control" placeholder="奖品名称" value="{{$field['name']}}" />
                                </td>
                                <td>
                                    <select style="width: 100px" class="sel-key selectpicker form-control" name="fields[{{$index}}][type]">
                                        @foreach($dbIndexes as $k => $v)
                                            <option value="{{ $k }}" {{@$field['type'] == $k ? 'selected' : '' }}>{{$v}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" class="islimit" name="fields[{{$index}}][is_limit]" {{isset($field['is_limit']) && $field['is_limit'] == 1 ? 'checked' : ''}}>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" class="isyes" name="fields[{{$index}}][is_yes]" {{isset($field['is_yes']) && $field['is_yes'] == 1 ? 'checked' : ''}}>
                                </td>
                                <td>
                                    <input type="text" name="fields[{{$index}}][peizhi]" class="form-control peizhi"  placeholder="配置" value="{{$field['peizhi']??''}}"/>
                                </td>
                                <td>
                                    <input type="text" name="fields[{{$index}}][total]" class="form-control prizes-count" placeholder="数量" value="{{$field['total']??''}}" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/>
                                </td>
                                <td>
                                    <input type="text" name="fields[{{$index}}][tips]" class="form-control" placeholder="中奖提示" value="{{$field['tips']??''}}"/>
                                </td>
                                <td>

                                    @php
                                        if($field['timeplan']) {
                                           $plans = @$field['timeplan']['plans'] ?? null;
                                           echo '<div class="col-sm-12">';
                                           if ($plans) {
                                               foreach ($plans as $k => $val) {
                                                   echo '<div class="row" style="width: 390px">
                                                            <div class="col-sm-6">
                                                                <div class="input-group">
                                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                    <input type="text" name="fields['.$index.'][timeplan]['.$k.'][start]" value="'.date('Y-m-d H:i:s', $val['start']).'" class="form-control start_at" style="width: 160px">
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-6">
                                                                <div class="input-group">
                                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                    <input type="text" name="fields['.$index.'][timeplan]['.$k.'][end]" value="'.date('Y-m-d H:i:s', $val['end']).'" class="form-control end_at" style="width: 160px">
                                                                </div>
                                                            </div>
                                                        </div>';
                                               }
                                           }
                                        }
                                    echo '</div></td><td>
                                            <button type="button" class="btn btn-sm btn-success" data-index="'.$index.'" id="time_add" onclick="add_time_palns(this)">增加时间</button>
                                            <button type="button" class="btn btn-sm btn-danger del-btn">删除</button>';
                                    @endphp
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                <input class="key-ipt" type="hidden" name="fields[0][key]" value="pz_01">
                                <input type="text" name="fields[0][name]" class="form-control" placeholder="奖品名称" />
                            </td>
                            <td>
                                <select style="width: 100px" class="sel-key selectpicker form-control" name="fields[0][type]">
                                    @foreach($dbIndexes as $k => $v)
                                        <option value="{{ $k }}">{{$v}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" class="islimit" value="1" name="fields[0][is_limit]">
                            </td>
                            <td>
                                <input type="checkbox" class="isyes" value="1" name="fields[0][is_yes]">
                            </td>
                            <td>
                                <input type="text" name="fields[0][peizhi]" class="form-control peizhi"  placeholder="配置" value=""/>
                            </td>
                            <td>
                                <input type="text" name="fields[0][total]" class="form-control prizes-count" placeholder="数量" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/>
                            </td>
                            <td>
                                <input type="text" name="fields[0][tips]" class="form-control" placeholder="中奖提示"/>
                            </td>
                            <td>
                                <div class="col-sm-12">
                                    <div class="row" style="width: 390px">
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" name="fields[0][timeplan][0][start]" class="form-control start_at" style="width: 160px" value="{{$project->configs->draw->stime}}">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" name="fields[0][timeplan][0][end]" class="form-control end_at" style="width: 160px" value="{{$project->configs->draw->etime}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-success" data-index="0" id="time_add" onclick="add_time_palns(this)">增加时间</button>
                                <button type="button" class="btn btn-sm btn-danger del-btn">删除</button>
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

    </div>

</div>
<input type="hidden" id="drawType" value="{{$project->configs->draw->draw_type}}">
<template id="time-tpl">
    <div class="row" style="width: 390px">
        <div class="col-sm-6">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" name="fields[__index__][timeplan][__index1__][start]" class="form-control start_at" style="width: 160px">
            </div>
        </div>

        <div class="col-sm-6">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" name="fields[__index__][timeplan][__index1__][end]" class="form-control end_at" style="width: 160px">
            </div>
        </div>
    </div>
    <script>
        $('.start_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN"});

        $('.end_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN"});
    </script>
</template>

<template id="table-field-tpl">
    <tr>
        <td>
            <input class="key-ipt" type="hidden" name="fields[__index__][key]" value="pz___pzindex__">
            <input type="text" name="fields[__index__][name]" class="form-control" placeholder="奖品名称" />
        </td>
        <td>
            <select style="width: 100px" class="sel-key form-control" name="fields[__index__][type]">
                @foreach($dbIndexes as $k => $v)
                    <option value="{{ $k }}">{{$v}}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="checkbox" class="islimit" value="1" name="fields[__index__][is_limit]">
        </td>
        <td>
            <input type="checkbox" class="isyes" value="1" name="fields[__index__][is_yes]">
        </td>
        <td>
            <input type="text" style="display: none" name="fields[__index__][peizhi]" class="form-control peizhi"  placeholder="配置" value=""/>
        </td>
        <td>
            <input type="text" name="fields[__index__][total]" class="form-control prizes-count" placeholder="数量" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/>
        </td>
        <td>
            <input type="text" name="fields[__index__][tips]" class="form-control" placeholder="中奖提示"/>
        </td>
        <td>
            <div class="col-sm-12">
                <div class="row" style="width: 390px">
                    <div class="col-sm-6">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="fields[__index__][timeplan][0][start]" class="form-control start_at" value="{{$project->configs->draw->stime}}" style="width: 160px">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="fields[__index__][timeplan][0][end]" class="form-control end_at" value="{{$project->configs->draw->etime}}" style="width: 160px">
                        </div>
                    </div>
                </div>
            </div>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-success" data-index="__index__" id="time_add" onclick="add_time_palns(this)">增加时间</button>
            <button type="button" class="btn btn-sm btn-danger del-btn">删除</button>
        </td>
    </tr>
    <script>
        $('.start_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN"});
        $('.end_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN"});
        $(".start_at").on("dp.change", function (e) {
            $('.end_at').data("DateTimePicker").minDate(e.date);
        });
        $(".end_at").on("dp.change", function (e) {
            $('.start_at').data("DateTimePicker").maxDate(e.date);
        });
    </script>
</template>
<script>
    function add_time_palns(obj)
    {
        var index = $(obj).data('index');
        var $timeContainer = $(obj).parent().prev().find('div.col-sm-12');
        var time_index = $timeContainer.children('div.row').size();
        $timeContainer.append(
            $('#time-tpl').html().replace(/__index1__/g, time_index).replace(/__index__/g, index)
        );
    }
</script>
<script>
    var prizesIndex =  $('#table-fields tr').length - 1;
    $(function () {
        $('#add-table-field').click(function (event) {
            prizesIndex++;
            if ($('#table-fields tr').length < 10) {
                var prizes_index = '0'+$('#table-fields tr').length;
            } else {
                var prizes_index = $('#table-fields tr').length;
            }
            $('#table-fields tbody').append(
                $('#table-field-tpl').html().replace(/__index__/g, prizesIndex).replace(/__pzindex__/g, prizes_index)
            );

        });

        $('.start_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN"});
        $('.end_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN"});

        $(".start_at").on("dp.change", function (e) {
            $('.end_at').data("DateTimePicker").minDate(e.date);
        });
        $(".end_at").on("dp.change", function (e) {
            $('.start_at').data("DateTimePicker").maxDate(e.date);
        });
        $('#table-fields select').each(function(){
            if (parseInt($(this).val()) == 2) {
                //谢谢参与
                hide_form(this);
            } else {
                show_form(this, true);
            }
        })
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
                var key = index > 8 ? 'pz'+(index+1) : 'pz_0'+(index+1);
                $(e).val(key);
            });

        });
    });

    function hide_form(select){
        $(select).parent().nextAll().find('.peizhi').hide();
        $(select).parent().nextAll().find('.islimit').removeAttr('checked');
        $(select).parent().nextAll().find('.isyes').removeAttr('checked');
        $(select).parent().nextAll().find('.prizes-count').hide();
        $(select).parent().nextAll().find('div.col-sm-12').hide();
        $(select).parent().nextAll().find('button.btn-success').hide();
    }
    function show_form(select, showPeizhi){
        if(showPeizhi) {
            $(select).parent().nextAll().find('.peizhi').show();
        } else {
            $(select).parent().nextAll().find('.peizhi').hide();
        }
        $(select).parent().nextAll().find('.prizes-count').show();
        $(select).parent().nextAll().find('div.col-sm-12').show();
        $(select).parent().nextAll().find('button.btn-success').show();
    }

    //分类
    $('#table-fields').on('change', 'select', function(){
        if (parseInt($(this).val()) == 2) {
            //谢谢参与
            hide_form(this);
        } else {
            show_form(this, true);
        }
    });

    //限量
    $('#table-fields').on('change', '.islimit', function(){
       if ($(this).is(':checked')) {
           if ($(this).parent().prevAll().find('.selectpicker').val() == 2) {
               $(this).removeAttr('checked');
               $(this).parent().nextAll().find('.isyes').removeAttr('checked');
           }
       } else {
           $(this).parent().nextAll().find('.isyes').removeAttr('checked');
       }
    });

    //100%
    $('#table-fields').on('change', '.isyes', function(){
        if ($(this).is(':checked')) {
            if ($(this).parent().prevAll().find('.selectpicker').val() == 2) {
                $(this).removeAttr('checked');
            }
            if (!$(this).parent().prevAll().find('.islimit').is(':checked')) {
                $(this).removeAttr('checked');
            }
        }
    });

    //提交
    $('.btn-info').click(function () {
        var isEmpty = false;
        var isNameEmpty = false;
        var hasText = false;
        $("#table-fields select").each(function (){
            var type = parseInt($(this).val());
            if(!$(this).parent().nextAll().find('.islimit').is(':checked') && !hasText){
                hasText = true;
            }
            if(type!=2 && !isEmpty) {
                var count = $(this).parent().next().next().children('input').val();
                isEmpty = count == "" ? true : (parseInt(count) < 0);
            }
            if(type!=2 && !isNameEmpty) {
                var name = $(this).parent().prev().children('input:eq(1)').val();
                isNameEmpty = name == "";
            }
        });
        //判断 type
        var drawType = $('#drawType').val();
        var nPrizesLength = $("#table-fields tr").length;
        if((drawType == 'jgg' || drawType == 'dzp') && nPrizesLength!=9) {
            swal({
                title: "大转盘、九宫格类抽奖活动奖品数必须设置为8个！",
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
                title: "非“谢谢参与”类 投放量 必须填写且大于等于0！",
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
                title: "非“谢谢参与”类 奖品名称必填 ！",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                closeOnConfirm: false,
                cancelButtonText: "取消"
            });
            return false;
        }
        if(!hasText) {
            swal({
                title: "必须含有一个 不限量类 奖品！",
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
