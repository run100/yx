<style>
    .stats-table  { background: #515151;}
    .stats-table td, .stats-table th {background-color: white; vertical-align: middle !important;}
</style>


<table class="table stats-table" style="width: 900px;">
    <thead>
    <tr>
        <th scope="col">OpenID</th>
        <th scope="col">昵称</th>
        <th scope="col">队长姓名</th>
        <th scope="col">手机号</th>
        <th scope="col">授权码</th>
        <th scope="col">管理</th>
    </tr>
    </thead>
    @foreach($list as $tid => $item)
        <tr>
            <td>{{@$item['openid']}}</td>
            <td><img style="width: 40px; border-radius: 20px;" src="{{@$item['wx_info']['headimgurl']}}" /> {{@$item['wx_info']['nickname']}}</td>
            <td>{{@$item['info']['name']}}</td>
            <td>{{@$item['info']['phone']}}</td>
            <td><img src="{{@$item['ticket']}}" /></td>
            <td><a href="@route('yx2018.admin.wx_update_perms', ['act'  => 'cancel', 'tid' => $tid])">取消授权</a></td>
        </tr>
    @endforeach
</table>
