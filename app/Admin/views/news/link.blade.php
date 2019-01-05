<div class="btn-group pull-right" style="margin-right: 10px">
    <a href="{{$project->path}}" target="_blank" class="btn btn-sm btn-default"><i class="fa fa-list"></i>  创建预览</a>
</div>
<div class="btn-group pull-right" style="margin-right: 10px">
    <a href="/admin/news/{{$project_id}}/blocks" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;返回列表</a>
</div>

<div class="btn-group pull-right" style="margin-right: 10px">
    <a class="btn btn-sm btn-default"  data-toggle="modal" data-target="#myModal"><i class="fa fa-list"></i>&nbsp;板块样式预览</a>
</div>

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