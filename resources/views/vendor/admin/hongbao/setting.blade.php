<div class="box box-primary">
    <div class="btn-group">
        <div class="btn-group pull-left" style="margin-right: 10px">
            <a href="{{route('hongbao.setting', ['project_id' => $project->id])}}" class="btn btn-sm btn-default"><i class="fa fa-safari"></i>&nbsp;红包配置</a>
            <a href="{{route('hongbao.majia', ['project_id' => $project->id])}}" class="btn btn-sm btn-default"><i class="fa fa-drupal"></i>&nbsp;红包马甲</a>
        </div>
    </div>
    <div class="box-tools">
        <div class="btn-group pull-right" style="margin-right: 10px">
            <a href="{{route('projects.index')}}" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;返回列表</a>
        </div>
    </div>
    <div class="box-body">

        <form method="post" action="{{route('hongbao.setting', ['project_id' => $project->id])}}" id="scaffold" pjax-container>

            <div class="box-body">
                <h4>红包配置</h4>
                <p>项目时间：{{$project->configs->hongbao->stime}} ~ {{$project->configs->hongbao->etime}}</p>
                <table class="table table-hover" id="table-fields">
                    <tbody>
                    <tr>
                        <th>红包总额</th>
                        <th style="width: 120px">红包最小值</th>
                        <th style="width: 120px">红包最大值</th>
                        @if($project->configs->hongbao->category == 2)
                        <th style="width: 68px">队长额外奖励</th>
                        @endif
                        <th style="width: 68px">100%</th>
                        <th style="width: 100px">红包数量</th>
                        <th>时间</th>
                        <th style="width: 138px">操作</th>
                    </tr>
                    @if($hbsetting)
                        <tr>
                            <td>
                                <input type="text" name="hongbao[money]" class="form-control limit-money" placeholder="红包总额" value="{{$hbsetting['money']}}"/>
                            </td>
                            <td>
                                <input type="text" name="hongbao[min_money]" class="form-control limit-money" placeholder="红包最小值" value="{{$hbsetting['min_money']}}" />
                            </td>
                            <td>
                                <input type="text" name="hongbao[max_money]" class="form-control limit-money" placeholder="红包最大值" value="{{$hbsetting['max_money']}}" />
                            </td>
                            <td>
                                <input id="isYes" type="checkbox" value="1" class="islimit" name="hongbao[is_yes]" {{isset($hbsetting['is_yes']) && $hbsetting['is_yes'] == 1 ? 'checked' : ''}}>
                            </td>
                            <td>
                                <input type="text" name="hongbao[total]" class="form-control peizhi"  placeholder="红包数量" value="{{$hbsetting['total']}}" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/>
                            </td>
                            <td>
                                @php
                                    if($hbsetting['timeplan']) {
                                       $plans = @$hbsetting['timeplan']['timeplan']['plans'] ?? null;
                                       echo '<div class="col-sm-12">';
                                       if ($plans) {
                                           foreach ($plans as $k => $val) {
                                               echo '<div class="row" style="width: 390px">
                                                        <div class="col-sm-6">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                <input type="text" name="hongbao[timeplan]['.$k.'][start]" value="'.date('Y-m-d H:i:s', $val['start']).'" class="form-control start_at" style="width: 160px">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                <input type="text" name="hongbao[timeplan]['.$k.'][end]" value="'.date('Y-m-d H:i:s', $val['end']).'" class="form-control end_at" style="width: 160px">
                                                            </div>
                                                        </div>
                                                    </div>';
                                           }
                                       }
                                    }
                                echo '</div></td><td><button type="button" class="btn btn-sm btn-success" data-index="0" id="time_add" onclick="add_time_palns(this)">增加时间</button><button type="button" class="btn btn-sm btn-danger del-btn">删除</button>';
                                @endphp
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td>
                                <input type="text" name="hongbao[money]" class="form-control limit-money" placeholder="红包总额" />
                            </td>
                            <td>
                                <input type="text" name="hongbao[min_money]" class="form-control limit-money" placeholder="红包最小值" />
                            </td>
                            <td>
                                <input type="text" name="hongbao[max_money]" class="form-control limit-money" placeholder="红包最大值" />
                            </td>
                            <td>
                                <input id="isYes" type="checkbox" value="1" class="islimit" name="hongbao[is_yes]" @if($project->configs->hongbao->category == 1) checked @endif>
                            </td>
                            <td>
                                <input type="text" name="hongbao[total]" class="form-control peizhi"  placeholder="红包数量" onkeyup="this.value=this.value.replace(/[1-9]\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/>
                            </td>
                            <td>
                                <div class="col-sm-12">
                                    <div class="row" style="width: 390px">
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" name="hongbao[timeplan][0][start]" class="form-control start_at" style="width: 160px" value="{{$project->configs->hongbao->stime}}">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" name="hongbao[timeplan][0][end]" class="form-control end_at" style="width: 160px" value="{{$project->configs->hongbao->etime}}">
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
<template id="time-tpl">
    <div class="row" style="width: 390px" data-index="__index1__">
        <div class="col-sm-6">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" name="hongbao[timeplan][__index1__][start]" class="form-control start_at" style="width: 160px">
            </div>
        </div>

        <div class="col-sm-6">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" name="hongbao[timeplan][__index1__][end]" class="form-control end_at" style="width: 160px">
            </div>
        </div>
    </div>
    <script>
        $('.start_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN"});
        $('.end_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN"});
    </script>
</template>
<script>
    function add_time_palns(obj)
    {
        var $timeContainer = $(obj).parent().prev().find('div.col-sm-12');
        var time_index = $timeContainer.children('div.row').size();
        $timeContainer.append(
            $('#time-tpl').html().replace(/__index1__/g, time_index)
        );
    }
    function del_time_plan($obj)
    {
        var $timeContainer = $obj.parent().prev().find('div.col-sm-12');
        $timeContainer.find('.row:last').remove();
    }
    $(function () {

        $('.start_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN"});
        $('.end_at').datetimepicker({"format":"YYYY-MM-DD HH:mm:ss","locale":"zh-CN"});

        $(".start_at").on("dp.change", function (e) {
            $('.end_at').data("DateTimePicker").minDate(e.date);
        });
        $(".end_at").on("dp.change", function (e) {
            $('.start_at').data("DateTimePicker").maxDate(e.date);
        });

        $('.limit-money').keyup(function () {
            var v = $(this).val();
            if (/^0+$/.test(v)) {
                txt = 0;
            } else if (/^0+[1-9]+$/.test(v)) {
                var reg = v.match(/^0+/);
                if (reg != null) {
                    txt = v.substr(reg[0].length);
                }
            } else {
                var reg = v.match(/\d+\.?\d{0,2}/);
                var txt = '';
                if (reg != null) {
                    txt = reg[0];
                } else {
                    txt = '';
                }
            }
            $(this).val(txt);
        }).change(function () {
            $(this).keypress();
            var v = $(this).val();
            if (/\.$/.test(v)) {
                $(this).val(v.substr(0, v.length - 1));
            }
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
            del_time_plan($btn)
        });
    });

    function alertError(msg) {
        swal({
            title: msg,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "确认",
            closeOnConfirm: false,
            cancelButtonText: "取消"
        });
        return false;
    }

    //提交
    $('.btn-info').click(function () {
        var money = $('input[name="hongbao[money]"]').val();
        var min = $('input[name="hongbao[min_money]"]').val();
        var max = $('input[name="hongbao[max_money]"]').val();
        var total = $('input[name="hongbao[total]"]').val();
        total = total == '' ? 0 : parseInt(total);
        if (money == '' || money<=0) {
            return alertError("请设置红包总额，且总额大于0！");
        }
        if (min == '' || min<0.3) {
            return alertError("请设置红包最小值，且大于等于0.3元！");
        }
        if (max == '' || max<min) {
            return alertError("请设置红包最大值，且大于等于红包最小值！");
        }
        if (!$('input[name="hongbao[is_yes]"]:checked').val() && total <=0 ) {
            return alertError("不是100%，红包必须设置数量且不能小于1！");
        }
        $(this).html("<i class='fa fa-spinner fa-spin'></i>");
        $(this).append("<span>提交</span>");
        $('#scaffold').submit();
    });

    //100%
    @if($project->configs->hongbao->category == 1)
        $('#isYes').click(function(){
        $("#isYes").prop("checked",true);

        });
    @endif

</script>
