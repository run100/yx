<style>
    .row_top_10 {
        margin-top: 10px;
        margin-bottom: 10px;
    }
</style>
<section class="content"><div class="row"><div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">创建<span style="font-weight:bold;color:dodgerblue;"> [板块{{$block_id}}] </span>信息</h3>

                    <div class="box-tools">
                        <div class="btn-group pull-right" style="margin-right: 10px">
                            <a href="{{$project->path}}" target="_blank" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;创建预览</a>
                        </div>
                        <div class="btn-group pull-right" style="margin-right: 10px">
                            <a href="/admin/news/{{$project_id}}/blocks" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;返回列表</a>
                        </div>
                        <div class="btn-group pull-right" style="margin-right: 10px">
                            <a class="btn btn-sm btn-default"  data-toggle="modal" data-target="#myModal"><i class="fa fa-list"></i>&nbsp;板块样式预览</a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form action="{{route('news.block', ['project_id'=>$project_id, 'block_id' => $block_id])}}" method="post" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data" pjax-container="">

                    <input  type="hidden" name="id" value="{{@$id}}"/>

                    @yield('content');

                    <!-- /.box-body -->
                    <div class="box-footer">

                        <input type="hidden" name="_token" value="vS6g2idrbb4jPxGhs4DZJOzLkd2mKiyWyccs9CgN"><div class="col-md-2">

                        </div>
                        <div class="col-md-8">

                            <div class="btn-group pull-right">
                                <button type="submit" class="btn btn-info pull-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> 提交">提交</button>
                            </div>

                            <div class="btn-group pull-left">
                                <button type="reset" class="btn btn-warning">撤销</button>
                            </div>

                        </div>

                    </div>

                    {{--<input type="hidden" name="_previous_" value="http://zhuanti.wang.365jia.lab/admin/projects/202/players" class="_previous_">--}}
                    <!-- /.box-footer -->
                    {{ csrf_field() }}
                </form>
            </div>

        </div></div>

</section>



<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width:650px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    板块{{$block_id}}样式
                </h4>
            </div>
            <div class="modal-body">
                <img src="/common_news/block_imgs/block{{$block_id}}.png" width="600" height="300"/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>




<script>
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
        /*
        swal({
            title: "提交成功",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "确认",
            closeOnConfirm: false,
            cancelButtonText: "取消"
        });
        */
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