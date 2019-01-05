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
        <form method="post" action="{{route('hongbao.majia', ['project_id'=>$project->id])}}" id="scaffold" pjax-container>
            <div class="box-body">
                <h4>红包马甲 （温馨提示：页面有缓存，马甲数据2分钟内生效!）</h4>
                <p>项目时间：{{$project->configs->hongbao->stime}} ~ {{$project->configs->hongbao->etime}}</p>
                <table class="table table-hover" id="table-fields">
                    <tbody>
                        <tr>
                            <th>昵称</th>
                            <th>红包金额</th>
                            <th>操作</th>
                        </tr>
                        @forelse($datas as $v)
                            @php
                            $obj = wj_json_decode($v);
                            @endphp
                            <tr>
                                <td>
                                    <input type="text" class="form-control name-input" value="{{$obj['n']}}" readonly/>
                                </td>
                                <td>
                                    <input type="text" class="form-control win-date" value="{{$obj['m']}}" readonly/>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger del-btn" data-content="{{$v}}" >删除</button>
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td>
                                <input type="text" name="fields[n][]" class="form-control name-input" placeholder="昵称" value=""/>
                            </td>
                            <td>
                                <input type="text" name="fields[m][]" class="form-control win-date" placeholder="金额" value=""/>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger del-btn" data-content="" >删除</button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <hr style="margin-top: 0;"/>

                <div class='form-inline margin' style="width: 100%">
                    <div class='form-group'>
                        <button type="button" class="btn btn-sm btn-success" id="add-table-field"><i class="fa fa-plus"></i>&nbsp;&nbsp;新增</button>
                    </div>
                </div>

            </div>
            <div class="box-footer">
                @if(strtotime($project->configs->hongbao->stime) <= time()&& time() < strtotime($project->configs->hongbao->etime))
                    <button type="button" class="btn btn-info pull-right">提交</button>
                @endif
            </div>

        {{ csrf_field() }}

        </form>

    </div>

</div>
<script id="winTemp" type="text/html">
    <tr>
        <td>
            <input type="text" name="fields[n][]" class="form-control name-input" placeholder="昵称" value=""/>
        </td>
        <td>
            <input type="text" name="fields[m][]" class="form-control win-date" placeholder="金额" value=""/>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger del-btn" data-content="" >删除</button>
        </td>
    </tr>
</script>
<script>
    function listenInputMoney(id, isInt) {
        $(id).keyup(function () {
            var v = $(this).val();
            if (/^0+$/.test(v)) {
                txt = 0;
            } else if (/^0+[1-9]+$/.test(v)) {
                var reg = v.match(/^0+/);
                if (reg != null) {
                    txt = v.substr(reg[0].length);
                }

            } else {
                if (isInt) {
                    var reg = v.match(/\d+/);
                } else {
                    var reg = v.match(/\d+\.?\d{0,2}/);
                }
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
    }
    $('#add-table-field').click(function () {
        $('#table-fields tbody').append($('#winTemp').html());
    });
    listenInputMoney('.win-date');
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
            var content = $btn.attr('data-content');
            if ( content == '') {
                $btn.parent().parent().remove();
            } else {
                $btn.html("<i class='fa fa-spinner fa-spin'></i>");
                $btn.append("<span>删除</span>");
                $.post($('#scaffold').attr('action')+'/del', {mj_json:content}, function(res){
                    $btn.parent().parent().remove();
                })
            }
        });
    });
    //提交
    $('.btn-info').click(function () {
        var isEmpty = false;
        if($("#table-fields .name-input").length == 0) {
            swal({
                title: "请至少添加一个奖品马甲！",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                closeOnConfirm: false,
                cancelButtonText: "取消"
            });
            return false;
        }
        $("#table-fields .name-input").each(function (){
            var name = $(this).val();
            if (name == '') {
                isEmpty = true;
            }
        });
        var isMoneyEmpry = false;
        $("#table-fields .win-date").each(function (){
            var money = $(this).val();
            if (money == '') {
                isMoneyEmpty = true;
            }
        });
        if(isEmpty) {
            swal({
                title: "昵称不能为空！",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                closeOnConfirm: false,
                cancelButtonText: "取消"
            });
            return false;
        }
        if(isMoneyEmpry) {
            swal({
                title: "红包金额不能为空！",
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