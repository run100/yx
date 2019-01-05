@include('admin::form.btn')
<div class="box-tools">
    <div class="btn-group pull-right" style="margin-right: 10px">
        <a href="{{route('projects.index')}}" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;返回列表</a>
    </div>
</div>
<div class="row">
    <form class="form-inline" id="reportForm" action="/admin/datareport/{{$id}}/datas">
        <div class="col-md-2">
            <label for="exampleInputName2" style="width: 68px;">项目</label>
            <select name="capacity" id="capacity" style="width: 168px;">
                @foreach($caps as $k=>$v)
                    <option value="{{$k}}">{{$v}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="exampleInputEmail2"  style="width: 68px;">方式</label>
            <select name="timeType" style="width: 168px;">
                <option value="hour">时</option>
                <option value="day">天</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="exampleInputEmail2"  style="width: 68px;">时间</label>
            <input style="width: 140px" id="startAt" name="startAt" value="{{date('Y-m-d 00:00', strtotime($endAt))}}" class="form-control" type="text">
            ~
            <input style="width: 140px" id="endAt" name="endAt" value="{{$endAt}}" class="form-control" type="text">
        </div>
        <div class="col-md-2">
            <button type="button" id="reportBtn" class="btn btn-warning">查看</button>
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-8">
    <table class="table table-bordered" id="totalTable">
        <thead></thead>
        <tbody></tbody>
    </table>
    </div>
</div>
<div class="row">
    <div class="row bg-danger" style="padding-left: 30px">
        <h4 class="text-danger" id="projectName">集字/图</h4>
    </div>
    <div class="row">
        <div class="col-md-4">
            <table class="table table-bordered" id="dataTable">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="col-md-2">
            <span id="detailTableTitle" style="float: right;"></span>
        </div>
        <div class="col-md-4">
            <table class="table table-bordered" id="detailTable">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <canvas id="myChart"></canvas>
        </div>
        <div class="col-md-4">
            <table class="table table-bordered" id="table">
                <thead>

                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    var data = {labels: [], datasets: []};
    window.projectConf = {
        jizi : [{key:'time', name:'时间'},{key:'bm', name:'报名人数'},{key:'ff', name:'发放字/图总数'},{key:'cz', name:'操作'}],
        jiziTotal : [{key:'date',name:'项目时间'},{key:'time', name:'当前时间'},{key:'cy', name:'参与人数'},{key:'bm', name:' 报名人数'},{key:'ff',name:'发放字/图总数'}],
        jiziData : [{key:'name', name:'字/图名称'},{key:'total', name:'总量'},{key:'yf', name:' 已发数量'},{key:'sy', name:' 剩余数量'}],
        jiziDetail : [{key:'name', name:'字/图名称'},{key:'total', name:'总量'},{key:'yf', name:' 已发数量'}],
        draw : [{key:'time', name:'时间'},{key:'cy', name:'参与人次'},{key:'zj', name:'中奖人次'},{key:'cz', name:'操作'}],
        drawTotal : [{key:'date', name:'项目时间'},{key:'time', name:' 当前时间'},{key:'cy', name:'参与人数'},@if($hasJizi){key:'bm', name:'报名人数'},@endif{key:'cj',name:'抽奖次数'},{key:'zj',name:'中奖次数'},{key:'ff',name:'发放奖品总数'}],
        drawData : [{key:'name', name:'奖品名称'},{key:'total', name:'总量'},{key:'yf', name:' 已发数量'},{key:'sy', name:' 剩余数量'}],
        drawDetail : [{key:'name', name:'奖品名称'},{key:'ff', name:'发放数量'}],
    };
    $(function () {
        $('#startAt').datetimepicker({"format":"YYYY-MM-DD HH:mm","locale":"zh-CN"});
        $('#endAt').datetimepicker({"format":"YYYY-MM-DD HH:mm","locale":"zh-CN"});
        var ctx = document.getElementById("myChart").getContext('2d');

        var options = {
            layout: {
                padding: {
                    left: 50,
                    right: 50,
                    top: 0,
                    bottom: 0
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        callback: function(value, index, values) {
                            return value;
                        }
                    }
                }]
            }
            };
        window.chart = new Chart(ctx, {type:'line', data:data, options:options});
        ajaxForm();
    });
    $('#reportBtn').click(function () {
        ajaxForm();
    });
    function ajaxForm(){
        $('#projectName').html($('#capacity option:checked').html());
        var capacity = $('#capacity').val();
        var tables = {table:capacity, totalTable:capacity+'Total', dataTable:capacity+'Data'};
        addTableHead(tables);
        $.get($('#reportForm').attr('action'), $('#reportForm').serialize(), function(res){
            window.chart.data = res.data.datas;
            console.log(window.chart.data);
            window.chart.update();
            updateTableBody(tables, res.data);
        })
    }
    function addTableHead(tables) {
        for(var tableId in tables) {
            $('#'+tableId+' tbody').html('');
            var dataKey = tables[tableId];
            var head = '<tr>';
            for (var i in window.projectConf[dataKey]) {
                head += '<td>' + window.projectConf[dataKey][i]['name'] + '</td>';
            }
            head += '</tr>';
            $('#' + tableId + ' thead').html(head);
        }
    }
    function updateTableBody(tables, data) {
        for(var tableId in tables) {
            var dataKey = tables[tableId];
            for (var i in data[dataKey]){
                var content = '<tr>';
                for (var j in window.projectConf[dataKey]){
                    content += '<td>'+data[dataKey][i][window.projectConf[dataKey][j]['key']]+'</td>';
                }
                content += '</tr>';
                $('#'+tableId+' tbody').append(content);
            }
        }
    }
    function makeDetailTable(capacity, date) {
        $('#detailTableTitle').html(date+'详情：');
        var tables = {detailTable:capacity+'Detail'};
        addTableHead(tables);
        $.get($('#reportForm').attr('action')+'detail', {capacity:capacity,date:date}, function(res){
            updateTableBody(tables, res.data);
        })
    }
</script>