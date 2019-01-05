
<style>
    .stats-table  { background: #515151;}
    .stats-table td, .stats-table th {background-color: white;}
</style>


<table class="table stats-table" style="width: 800px;">
    <thead>
    <tr>
        <th scope="col">openid</th>
        <th scope="col">路线</th>
        <th scope="col">昵称</th>
        <th scope="col">二维码</th>
    </tr>
    </thead>
    @foreach($rows as $row)
        <tr>
            <td>{{$row['openid']}}</td>
            <td>{{$row['line']}}</td>
            <td>{{$row['nickname']}}</td>
            <td>
                <a href="{{$row['qrcode']}}" target="_blank">
                    <img src="{{$row['qrcode']}}" alt="" style="width: 100px" >
                </a>
            </td>
        </tr>
    @endforeach
</table>

@if($pre_url)
    <a href="{{$pre_url}}">上一页</a>
@endif
@if($next_url)
    <a href="{{$next_url}}">下一页</a>
@endif
