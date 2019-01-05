<style>
    .stats-table  { background: #515151;}
    .stats-table td, .stats-table th {background-color: white;}
</style>

<table class="table stats-table" style="width: 400px;">
    <tr>
        <td>数据截至</td>
        <td>{{$time}}</td>
    </tr>
    <tr>
        <td>专题UV</td>
        <td>{{$uv}}</td>
    </tr>
    <tr>
        <td>独立捐赠者</td>
        <td>{{$voters}}</td>
    </tr>
    <tr>
        <td>多少个省</td>
        <td>{{$provinces}}</td>
    </tr>
    <tr>
        <td>多少个市</td>
        <td>{{$cities}}</td>
    </tr>
    @if($donaters)
    <tr>
        <td>独立捐款者</td>
        <td>{{$donaters}}</td>
    </tr>
    <tr>
        <td>重复捐款者</td>
        <td>{{$rep_donaters}} ({{bcdiv($rep_donaters * 100, $donaters, 2)}}%)</td>
    </tr>
    @endif
    <tr>
        <td>捐款达10,000时间</td>
        <td>{{@$time_10000}}</td>
    </tr>
    <tr>
        <td>捐款达50,000时间</td>
        <td>{{@$time_50000}}</td>
    </tr>
    <tr>
        <td>捐款达100,000时间</td>
        <td>{{@$time_100000}}</td>
    </tr>
</table>

<table class="table stats-table" style="width: 600px;">
    <thead>
    <tr>
        <th scope="col"></th>
        <th scope="col">MINI线</th>
        <th scope="col">半程线</th>
        <th scope="col">全程线</th>
        <th scope="col">合计</th>
    </tr>
    </thead>
    <tr>
        <td>报名人数</td>
        <td>{{@$line_players['L1'] ?: 0}}</td>
        <td>{{@$line_players['L2'] ?: 0}}</td>
        <td>{{@$line_players['L3'] ?: 0}}</td>
        <td>{{@$players ?: 0}}</td>
    </tr>
    <tr>
        <td>报名组数</td>
        <td>{{@$line_groups['L1'] ?: 0}}</td>
        <td>{{@$line_groups['L2'] ?: 0}}</td>
        <td>{{@$line_groups['L3'] ?: 0}}</td>
        <td>{{@$groups ?: 0}}</td>
    </tr>
    <tr>
        <td>捐款额</td>
        <td>{{@$line_donates['L1'] ?: 0}}</td>
        <td>{{@$line_donates['L2'] ?: 0}}</td>
        <td>{{@$line_donates['L3'] ?: 0}}</td>
        <td>{{@$donates ?: 0}}</td>
    </tr>
    <tr>
        <td>“0元组”</td>
        <td>{{@$line_zero_groups['L1'] ?: 0}}</td>
        <td>{{@$line_zero_groups['L2'] ?: 0}}</td>
        <td>{{@$line_zero_groups['L3'] ?: 0}}</td>
        <td>{{@$zero_groups ?: 0}}</td>
    </tr>
    <tr>
        <td>“1元组”</td>
        <td>{{@$line_one_groups['L1'] ?: 0}}</td>
        <td>{{@$line_one_groups['L2'] ?: 0}}</td>
        <td>{{@$line_one_groups['L3'] ?: 0}}</td>
        <td>{{@$one_groups ?: 0}}</td>
    </tr>
</table>

<table class="table stats-table" style="width: 600px;">
    <thead>
    <tr>
        <td colspan="3" style="background: #8fb7db"><strong>独立捐款Top10</strong></td>
    </tr>
    <tr>
        <th scope="col">昵称</th>
        <th scope="col">OpenID</th>
        <th scope="col">捐款额</th>
    </tr>
    </thead>
    @foreach($top10_donate as $item)
        <tr>
            <td>{{$item->wxinfo['nickname']}}</td>
            <td>{{$item->openid}}</td>
            <td>{{$item->total}}</td>
        </tr>
    @endforeach
</table>

<table class="table stats-table" style="width: 300px;">
    <thead>
    <tr>
        <th scope="col">统计项</th>
        <th scope="col">数量</th>
        <th scope="col">占比</th>
    </tr>
    </thead>
    @foreach($stats as $item)
        @if(is_string($item))
            @php($stat_title = $item)
            <tr>
                <td colspan="3" style="background: #8fb7db"><strong>{{$item}}</strong></td>
            </tr>
        @else
            <tr>
                <td>{{$item['name']}}</td>
                <td>{{$item['total']}}</td>
                <td>{{bcdiv($item['total'] * 100, @$stat_title == '队长星座' ? $groups : $players, 2)}}%</td>
            </tr>
        @endif

    @endforeach
</table>