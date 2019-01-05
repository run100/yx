<div class="btn-group">
    <div class="btn-group pull-left" style="margin-right: 10px">
        @if($project->can('jizi'))
            <a href="{{route('jizi.logs', ['project_id' => $project->id])}}" class="btn btn-sm btn-default"><i class="fa fa-font"></i>&nbsp;集字/图记录</a>
        @endif
        <a href="{{route('prizes.logs', ['project_id' => $project->id])}}" class="btn btn-sm btn-default"><i class="fa fa-angellist"></i>&nbsp;抽奖记录</a>
        @if($project->isPrize() && $project->configs->draw->is_zhuli == 'Y')
            <a href="{{route('prizes.zhuli_logs', ['project_id' => $project->id])}}" class="btn btn-sm btn-default"><i class="fa fa-font"></i>&nbsp; 助力记录</a>
        @endif
        <a href="{{route('report.index', ['project_id' => $project->id])}}" class="btn btn-sm btn-default"><i class="fa fa-bar-chart-o"></i>&nbsp;数据统计</a>
    </div>
</div>