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
        <form method="post" action="{{route('prizes.majia', ['project_id'=>$project->id])}}" id="scaffold" pjax-container>
            <div class="box-body">
                <h4>中奖马甲 （温馨提示：中奖数据会立即生效!）</h4>
                <p>项目时间：{{$project->configs->draw->stime}} ~ {{$project->configs->draw->etime}}</p>
                <table class="table table-hover" id="table-fields">
                    <tbody>
                        <tr>
                            <th>中奖人昵称</th>
                            <th>奖品</th>
                            <th>操作</th>
                        </tr>
                        @forelse($datas as $v)
                            @php
                            $obj = wj_json_decode($v);
                            @endphp
                            <tr>
                                <td>
                                    <input type="text" class="form-control name-input" value="{{$obj['name']}}" readonly/>
                                </td>
                                <td>
                                    <input type="text" class="form-control win-date" value="{{$obj['prize']}}" readonly/>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger del-btn" data-content="{{$v}}" >删除</button>
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td>
                                <input type="text" name="fields[name][]" class="form-control name-input" placeholder="昵称" value=""/>
                            </td>
                            <td>
                                <select style="width: 500px" class="sel-key form-control" name="fields[prize][]">
                                    @foreach($project->configs->base_form_prizes as $v)
                                        @if($v->type!=2)
                                        <option value="{{$v->name}}">{{$v->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
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
                @if(time() < strtotime($project->configs->draw->etime))
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
            <input type="text" name="fields[name][]" class="form-control name-input" placeholder="昵称" value=""/>
        </td>
        <td>
            <select style="width: 500px" class="sel-key form-control" name="fields[prize][]">
                @foreach($project->configs->base_form_prizes as $v)
                    @if($v->type!=2)
                    <option value="{{$v->name}}">{{$v->name}}</option>
                    @endif
                @endforeach
            </select>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger del-btn" data-content="" >删除</button>
        </td>
    </tr>
</script>
<script>

    $('#add-table-field').click(function () {
        $('#table-fields tbody').append($('#winTemp').html());
    });
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
        if(isEmpty) {
            swal({
                title: "中奖人昵称不能为空！",
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