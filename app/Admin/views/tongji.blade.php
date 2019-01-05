<link rel="stylesheet" href="/vendor/tongji/style/special_count.css" type="text/css"/>
<script type="text/javascript" src="/vendor/tongji/js/echarts.common.min.js"></script>
<script type="text/javascript" src="/js/My97DatePicker/WdatePicker.js"></script>
<div class="special_count">
    <div class="b_sc_1">
        <!-- 专题总数统计 -->
        <div class="">
            <h3><span class="ico_line_sc"></span><span>专题总数类别统计(当前时间{{date("Y-m-d")}})</span></h3>
            <ul class="cf_sc mt35_sc">
                <li class="item_sort_sc">
                    <div class="item_sort_sc_in cf_sc">
                        <span class="ico_sc_1"></span>
                        <div class="item_sort_txt_sc">
                            <p>专题总数量/进行中</p>
                            <p class="mt10_sc"><span class="fwb_sc fz20_sc">{{$ret["all"]}}</span><span
                                        class="cor_2_sc mar10_sc">/</span><span
                                        class="fwb_sc fz20_sc cor_1_sc">{{$ret["alling"]}}</span></p>
                        </div>
                    </div>
                </li>
                <li class="item_sort_sc">
                    <div class="item_sort_sc_in cf_sc">
                        <span class="ico_sc_2"></span>
                        <div class="item_sort_txt_sc">
                            <p>定制开发</p>
                            <p class="mt10_sc"><span class="fwb_sc fz20_sc">{{$ret["other"]}}</span><span
                                        class="cor_2_sc mar10_sc">/</span><span
                                        class="fwb_sc fz20_sc cor_1_sc">{{$ret["othering"]}}</span></p>
                        </div>
                    </div>
                </li>
                <li class="item_sort_sc">
                    <div class="item_sort_sc_in cf_sc">
                        <span class="ico_sc_3"></span>
                        <div class="item_sort_txt_sc">
                            <p>投票公版</p>
                            <p class="mt10_sc"><span class="fwb_sc fz20_sc">{{$ret["vote"]}}</span><span
                                        class="cor_2_sc mar10_sc">/</span><span
                                        class="fwb_sc fz20_sc cor_1_sc">{{$ret["voteing"]}}</span></p>
                        </div>
                    </div>
                </li>
                <li class="item_sort_sc">
                    <div class="item_sort_sc_in cf_sc">
                        <span class="ico_sc_4"></span>
                        <div class="item_sort_txt_sc">
                            <p>集字集图公版</p>
                            <p class="mt10_sc"><span class="fwb_sc fz20_sc">{{$ret["jizi"]}}</span><span
                                        class="cor_2_sc mar10_sc">/</span><span
                                        class="fwb_sc fz20_sc cor_1_sc">{{$ret["jiziing"]}}</span></p>
                        </div>
                    </div>
                </li>
                <li class="item_sort_sc">
                    <div class="item_sort_sc_in cf_sc">
                        <span class="ico_sc_5"></span>
                        <div class="item_sort_txt_sc">
                            <p>抽奖公版</p>
                            <p class="mt10_sc"><span class="fwb_sc fz20_sc">{{$ret["cj"]}}</span><span
                                        class="cor_2_sc mar10_sc">/</span><span
                                        class="fwb_sc fz20_sc cor_1_sc">{{$ret["cjing"]}}</span></p>
                        </div>
                    </div>
                </li>
                <li class="item_sort_sc">
                    <div class="item_sort_sc_in cf_sc">
                        <span class="ico_sc_6"></span>
                        <div class="item_sort_txt_sc">
                            <p>砍价公版</p>
                            <p class="mt10_sc"><span class="fwb_sc fz20_sc">{{$ret["cut"]}}</span><span
                                        class="cor_2_sc mar10_sc">/</span><span
                                        class="fwb_sc fz20_sc cor_1_sc">{{$ret["cuting"]}}</span></p>
                        </div>
                    </div>
                </li>

            </ul>
        </div><!-- #专题总数统计 -->
        <!-- 频道统计 -->
        <div class="mt75_sc">
            <h3 class="cf_sc"><span class="ico_line_sc"></span><span>频道统计</span>
                <!-- 时间选择 -->
                <div class="chart_date_sc" id="chart_date_sc_1">
                    <span class="btn_chart_date_sc current_sc">全部</span>
                    <span class="btn_chart_date_sc">近一个月</span>
                    <span class="btn_chart_date_sc">近半年</span>

                    <input type="text" onClick="WdatePicker({el:this,dateFmt:'yyyy-MM-dd'})" id="start_channel"
                           class="ipt_sc_1"/>
                    <span class="cor_2_sc">—</span>
                    <input type="text" onClick="WdatePicker({el:this,dateFmt:'yyyy-MM-dd'})" id="end_channel"
                           class="ipt_sc_1"/>

                    <a href="javascript:void(0);" id="search_channel" title="" class="btn_sc_1">确认</a>
                </div><!-- #chart_date_sc -->
            </h3>
            <!-- 图表 -->
            <div id="chart_1" style="width: 100%;height:400px;" class="mt35_sc"></div>
        </div><!-- #频道统计 -->

        <!-- 月度统计 -->
        <div class="mt75_sc">
            <h3 class="cf_sc"><span class="ico_line_sc"></span><span>月度统计</span>

            </h3>
            <!-- 图表 -->
            <div id="chart_2" style="width: 100%;height:400px;" class="mt35_sc"></div>
            <div class="mt35_sc">
                <ul class="chart_column fz14 cf_sc">
                    <li><span>专题总数：</span><span class="chart_column_num ">75</span>个</li>
                    <li><span>定制专题总数：</span><span class="chart_column_num ">75</span>个</li>
                    <li><span>公版投票总数：</span><span class="chart_column_num ">75</span>个</li>
                    <li><span>公版集字总数：</span><span class="chart_column_num ">75</span>个</li>
                    <li><span>公版抽奖总数：</span><span class="chart_column_num ">75</span>个</li>
                    <li><span>公版砍价总数：</span><span class="chart_column_num ">75</span>个</li>
                </ul>
            </div>
        </div><!-- #月度统计 -->
    </div><!-- #b_sc_1 -->
</div>

<script type="text/javascript">

    var Charts = {
        myChart: null,
        init: function () {
            Charts.myChart = echarts.init(document.getElementById('chart_1'));
            var option = {
                tooltip: {
                    trigger: 'item',
                    formatter: "{b} : {c} ({d}%)"
                },
                legend: {
                    type: 'scroll',
                    orient: 'vertical',
                    right: '30%',
                    top: 20,
                    bottom: 20,
                    data: {!! $ret["branch"] !!}
                },
                series: [
                    {
                        name: '',
                        type: 'pie',
                        radius: '80%',
                        center: ['30%', '60%'],
                        data: [],
                        itemStyle: {
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        },
                        color: ['#37A2DA', '#32C5E9', '#67E0E3', '#9FE6B8', '#FFDB5C', '#ff9f7f', '#fb7293', '#E062AE', '#E690D1', '#e7bcf3', '#9d96f5', '#8378EA', '#96BFFF']
                    }
                ]
            };
            Charts.myChart.setOption(option);
            Charts.request({type: 0});
            Charts.typeChange();
            Charts.search();
        }, typeChange: function () {
            $('#chart_date_sc_1 .btn_chart_date_sc').click(function () {
                var i = $(this).index();
                Charts.request({type: i});
                $(this).parents('.chart_date_sc').find(".btn_chart_date_sc").removeClass("current_sc");
                $(this).addClass('current_sc');

            });
        }, search: function () {
            $('#search_channel').click(function () {
                var start = $("#start_channel").val();
                var end = $("#end_channel").val();
                Charts.request({start: start, end: end});
            });
        }, setdate(ser){
            Charts.myChart.setOption({
                legend: {
                    data:{!! $ret["branch"] !!}
                },
                series: [{
                    data: ser
                }]
            });
        }, request(date){
            $.getJSON('/admin/tongji_branch', date, function (ret) {
                console.log(ret);
                Charts.setdate(ret);

            });

        }
    };


    $(function () {
        ;
        (function () {
            Charts.init();
        }())

        //月度统计
        ;(function () {
            var myChart = echarts.init(document.getElementById('chart_2'));
            var option = {
                title: {
                    text: ''
                },
                tooltip: {
                    trigger: 'axis',
                    formatter: function (params) {
                        var res;
                        res = '<p>定制专题:' + al[params[0].dataIndex][0] + '</p><p>公版投票:' + al[params[0].dataIndex][1] + '</p><p>集字集图公版:' + al[params[0].dataIndex][2] + '</p><p>公版抽奖:' + al[params[0].dataIndex][3] + '</p><p>公版砍价:' + al[params[0].dataIndex][4] + '</p>'

                        return res;
                    }
                },
                legend: {
                    data: ['当月专题数量']
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: {!! $ret["months"] !!}
                },
                yAxis: {
                    type: 'value'
                },
                series: [
                    {
                        type: 'line',
                    }
                ],
                color: ['#37A2DA', '#32C5E9', '#67E0E3', '#9FE6B8', '#FFDB5C', '#ff9f7f', '#fb7293', '#E062AE', '#E690D1', '#e7bcf3', '#9d96f5', '#8378EA', '#96BFFF']
            };
            myChart.setOption(option);
            var  al = [], ser = [];
            //数据
            function data() {
                // 每个月 单休专题数据，这里只是提供例子展示，具体格式后台决定
                al = {!! $ret["month_sjs"] !!};
                for (var m = 0; m < al.length; m++) {
                    var sum = 0;
                    for (var n = 0; n < 5; n++) { //5个类别
                        sum = sum + al[m][n];//累加上次的值
                    }
                    ser.push(sum)
                }

            }

            //图标下 类别个数显示
            function sum() {
                var sum2 = 0; //所有专题总数
                for (var j = 1; j < $('.chart_column .chart_column_num').length; j++) {
                    var sum = 0;
                    for (var k = 0; k < al.length; k++) {
                        sum = sum + al[k][j - 1]; //单项中所有月份总数。
                    }
                    $('.chart_column .chart_column_num').eq(j).text(sum)

                    sum2 = sum + sum2; //计算全部专题数
                }
                $('.chart_column .chart_column_num').eq(0).text(sum2)
            }

            data();
            sum();

            myChart.setOption({
                series: [{
                    data: ser
                }]
            });

        }())
    })


</script>
