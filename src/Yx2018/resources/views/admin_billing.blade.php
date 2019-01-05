<style>
    .stats-table  { background: #515151;}
    .stats-table td, .stats-table th {background-color: white;}
</style>


<table class="table stats-table" style="width: 800px;">
    <thead>
    <tr>
        <th scope="col">日期</th>
        <th scope="col">交易笔数</th>
        <th scope="col">交易额</th>
        <th scope="col">退款额</th>
        <th scope="col">红包退款额</th>
        <th scope="col">手续费</th>
    </tr>
    </thead>
    @foreach($billings as $date => $b)
    <tr>
        <td>{{$b->dateline}} <a target="_blank" href="{{$b->link}}">[自动对账]</a></td>
        <td>{{$b->billings}}</td>
        <td>{{$b->payed}}</td>
        <td>{{$b->refund}}</td>
        <td>{{$b->hongbao_refund}}</td>
        <td>{{$b->tax}}</td>
    </tr>
    @endforeach
</table>
