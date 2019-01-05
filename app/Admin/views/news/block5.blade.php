<div class="box-header with-border">
    <h3 class="box-title">创建<span style="font-weight:bold;color:dodgerblue;"> [板块{{$block_id}}] </span>信息</h3>
    <div class="box-tools">
        @include('admin::news.link',['project'=>$project, 'project_id' => @$project_id, 'block_id' => @$block_id])
    </div>
</div>

<div class="box box-primary">

    <div class="box-body">

        <form id="scaffold" action="{{route('news.block', ['project_id'=>$project_id, 'block_id' => $block_id])}}" method="post" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data" pjax-container="">
            <div class="box-body">
                <input  type="hidden" name="id" value="{{@$id}}"/>
                @include('admin::news.channel',['news_channles_arr'=>$news_channles_arr, 'field' => @$field])

                <table class="table table-hover" id="table-fields">
                    <tbody>
                    <tr>
                        <th>图片</th>
                        <th>标题</th>
                        <th>链接</th>
                        <th>操作</th>
                    </tr>

                            @if(isset($field['img_infos']) && $field['img_infos'])
                                @foreach($field['img_infos'] as $k=>$v)
                            <tr>
                                <td>
                                    <input type="hidden" name ="img_name[]" value="{{$v['img_name']}}">
                                    <input type="file" class="form-control win-date" name="img[]"  />
                                    <span class="help-block">
                                        <i class="fa fa-info-circle"></i>&nbsp;图片说明：jpg/png格式, 1000*600，500k以内
                                    </span>
                                    <div style="height: 100px;width: 100px;">
                                        <img style="width: 100%;height: 100%" src="{{uploads_url($v['img_name'])}}">
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control win-date" name="title[]" value="{{$v['title']}}" />
                                </td>
                                <td>
                                    <input type="text" class="form-control win-date" name="link[]" value="{{$v['link']}}" />
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger del-btn" data-content="" >删除</button>
                                </td>

                            </tr>
                        @endforeach
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
            <div class="box-footer">

                <button type="button" class="btn btn-info pull-right">提交</button>


            </div>

            {{ csrf_field() }}

        </form>

        <div id="postcard_box" style="height:500px; width:500px;  background-color: grey ;position: fixed; top: 15%;left: 40%;display: none">
            <button style="position: absolute;right: 0px;top: 0px;height: 25px;width: 25px; text-align: center " id="postcard_close">x</button>
            <div style="width:450px;height: 450px;position: absolute;left: 25px;top: 25px;background-color: white ">
                <img src="">
            </div>
        </div>

    </div>

</div>
<script id="winTemp" type="text/html">
    <tr>
        <td>
            <input type="file" class="form-control win-date" name="img[]" />
        </td>
        <td>
            <input type="text" class="form-control win-date" name="title[]" />

        </td>
        <td>
            <input type="text" class="form-control win-date" name="link[]" />
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
//        var isEmpty = false;
//        if($("#table-fields .name-input").length == 0) {
//            swal({
//                title: "请至少添加一个奖品马甲！",
//                type: "warning",
//                showCancelButton: true,
//                confirmButtonColor: "#DD6B55",
//                confirmButtonText: "确认",
//                closeOnConfirm: false,
//                cancelButtonText: "取消"
//            });
//            return false;
//        }
//        $("#table-fields .name-input").each(function (){
//            var name = $(this).val();
//            if (name == '') {
//                isEmpty = true;
//            }
//        });
//        if(isEmpty) {
//            swal({
//                title: "中奖人昵称不能为空！",
//                type: "warning",
//                showCancelButton: true,
//                confirmButtonColor: "#DD6B55",
//                confirmButtonText: "确认",
//                closeOnConfirm: false,
//                cancelButtonText: "取消"
//            });
//            return false;
//        }
//        $(this).html("<i class='fa fa-spinner fa-spin'></i>");
//        $(this).append("<span>提交</span>");

        if($("#fields_channel_id").val() == "") {
            swal({
                title: "请选择频道！",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                closeOnConfirm: false,
                cancelButtonText: "取消"
            });
            return false;
        }
        $('#scaffold').submit();

    })

    $('.btn-showPostcard').click(function () {
        var link = $('#postcard_link').val();
        var proj_id = $('#proj_id').val();
        console.log(link);
        console.log(proj_id);
        $.ajax({
            url: link,
            type: 'get',
            dataType: 'string',
            contentType: false,
            processData: false,

            //data: "proj_id=< ? php echo $data['proj']->id;?>",

            success: function (data) {
                $('#postcard_box').show();

            },
            error: function (data) {
                $("#upload_img").bind("click");
                try {
                    var data = JSON.parse(data.responseText);
                    if (data.errors != undefined) {
                        for (var error in data.errors) {
                            alert(data.errors[error]);
                            break;
                        }
                    } else {
                        alert('出错了！')
                    }
                } catch (e) {
                    alert('出错了')
                }
            }
        })

    });
    $('#postcard_close').click(function () {
        $('#postcard_box').hide();
    });

</script>