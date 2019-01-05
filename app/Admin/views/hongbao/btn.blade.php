<div class="btn-group">
    <div class="btn-group pull-left" style="margin-right: 10px">
        <a href="{{route('hongbao.logs', ['project_id' => $project->id])}}" class="btn btn-sm btn-default"><i class="fa fa-font"></i>&nbsp;抽取记录</a>
        @if($project->can('hongbao') && $project->configs->hongbao->category == 1)
        <a href="{{route('hongbao.zhulis', ['project_id' => $project->id])}}" class="btn btn-sm btn-default"><i class="fa fa-angellist"></i>&nbsp;助力记录</a>
        @endif
        <a href="{{route('hongbao.billings', ['project_id' => $project->id])}}" class="btn btn-sm btn-default"><i class="fa fa-bar-chart-o"></i>&nbsp; 红包账单</a>
    </div>
</div>

<div class="btn-group pull-right" style="margin-right: 100px">
    <a href="{{route('projects.index')}}" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;返回列表</a>
</div>