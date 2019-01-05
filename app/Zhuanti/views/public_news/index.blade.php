<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>{{$pro_name}}</title>
    <link rel="stylesheet" href="http://365jia.cn/css/basic.css" type="text/css" />
    <link rel="stylesheet" href="/vendor/news/style/featured_progress.css" />
    <link rel="stylesheet" href="/vendor/news/style/wF_mask.css?v=2" />
    <link rel="stylesheet" href="/vendor/news/style/css.css?v=2" />
</head>

<style>
    .menu_li .nav_a:hover { text-decoration: none; }
    .menu_li:hover .nav_mask_pos,.current.menu_li .nav_mask_pos {
        display: block;
    }

    .nav_mask_pos {
        display: none;
        position: absolute;
        top: 0;
        left: 0;box_mask_sty
        width: 100%;
        height: 100%;
        background-color: #fff;
        opacity: .15;
        filter:Alpha(opacity=15);
        z-index: 2;
    }


    .tab_menu_list {
        margin-left: -45px;
        text-align: center;
    }

    .tab_menu_list li {
        width: 170px;
        margin-left: 45px;
    }

    .tab_menu_list .list_a {
        display: block;
        width: 100%;
        height: 50px;
        line-height: 50px;
        text-align: center;
        font-size: 18px;
        color: #333;
        background-color: #F2F2F2;
    }

    .tab_menu_list .list_a:hover,.tab_menu_list .current .list_a {
        color: #fff;
        background-color: {{isset($whole_configs['bg_color']) ? $whole_configs['bg_color'] : '#DF4244'}};
        text-decoration: none;
    }

    .news_list_area a:hover,.news_list_area a:hover em {
        color: {{isset($whole_configs['bg_color']) ? $whole_configs['bg_color'] : '#DF4244'}};;
        cursor: pointer;
        text-decoration: none;
    }

    .blk1_top1 a:hover,.blk1_top1 a:hover em {
        color: {{isset($whole_configs['bg_color']) ? $whole_configs['bg_color'] : '#DF4244'}};;
        cursor: pointer;
        text-decoration: none;
    }

    .border_pos {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        width: 276px;
        height: 159px;
        border: 3px solid {{isset($whole_configs['bg_color']) ? $whole_configs['bg_color'] : '#DF4244'}};
    }
</style>


<body>
<div class="box pb30">
    <div class="img_box">
        <div class="box_in">
            <img src="{{isset($whole_configs['news_top_pic']) ? uploads_url($whole_configs['news_top_pic']) : '/vendor/news/images/banner_00.jpg'}}">
        </div>
        <img src="{{isset($whole_configs['news_top_pic']) ? uploads_url($whole_configs['news_top_pic']) : '/vendor/news/images/banner_00.jpg'}}">
    </div>
    <!--导航-->
    <div class="nav_bg_sty" id="menu" style="background-color:{{isset($whole_configs['bg_color']) ? $whole_configs['bg_color'] : '#DF4244'}}">
        <ul class="menu_ul">
             @if($whole_configs['news_channel'])
               @foreach(explode(",", $whole_configs['news_channel']) as $i => $channel)
            <li class="menu_li">
                <a href="#channel_{{$i}}" class="nav_a">{{$channel}}</a>
                <span class="nav_mask_pos"></span>
            </li>
               @endforeach
             @endif
        </ul>
    </div><!--导航-->



    <div class="w1200 mar_auto">
            @if(isset($arr[\App\Models\News\Blocks::TYPE_BLOCK_1]))

                @foreach($arr[\App\Models\News\Blocks::TYPE_BLOCK_1] as $id => $block)
                    @php
                        $block1 = json_decode($block->block1, true);
                         $channel_id_l = isset($channel_arr[$block->channel_id]) ? 'channel_' . $channel_arr[$block->channel_id] : '';
                    @endphp

        <!--新闻动态1-->
       <a id="{{$channel_id_l}}" name="{{$channel_id_l}}"></a>
        <div  class="pt70">
            <p class="tac"><img style="width: 1200px;height:100px;" src="{{isset($block1['img_name']) ? uploads_url($block1['img_name']) : '/vendor/news/images/ico_title_01.png'}}" alt=""></p>
            <div class="mt40 pt25 cf">
                <div class="fl w580 mr30">
                    <a href="{{isset($block1['big_img_link']) ? $block1['big_img_link'] : '#'}}" target="_blank" class="re db tdn">
                        <img src="{{isset($block1['big_img_name']) ? uploads_url($block1['big_img_name']) : '/vendor/news/images/img_01.png'}}" class="img1"/>
                        <p class="box_mask_sty">
                            <span class="mask_text_sty"><em class="db pl25 pr25 ell">{{isset($block1['big_img_title']) ? $block1['big_img_title'] : ''}}</em></span>
                            <span class="mask_bg_sty"></span>
                        </p>
                    </a>
                </div>
                <div class="ov">
                    <p class="mt10 blk1_top1"><a href="{{isset($block1["right_top_link"]) ? $block1["right_top_link"] : "#"}}" target="_blank" class="fz30 fwb cor_3 ell">{{isset($block1['right_top_title']) ? mb_text_bytecut($block1['right_top_title'], 18, true) : ''}}</a></p>
                    <p class="mt25 pl5 fz16 lh30 cor_1">{{isset($block1['right_top_intro']) ? mb_text_bytecut($block1['right_top_intro'], 98) : ''}}<a href="{{isset($block1['right_top_link']) ? $block1['right_top_link'] : '#'}}" target="_blank" class="ml10 cor_4">[详细]</a></p>
                    <ul class="mt20 news_list_area">
                        <li class="fz18 lh44">
                            <a class="db ell" target="_blank" href="{{isset($block1['right_down_list_link1']) ? $block1['right_down_list_link1'] : '#'}}"><em class="ml5 mr10 cor_2">▪</em>{{isset($block1['right_down_list_title1']) ? $block1['right_down_list_title1'] : ''}}</a>
                        </li>
                        <li class="fz18 lh44">
                            <a class="db ell" target="_blank" href="{{isset($block1['right_down_list_link2']) ? $block1['right_down_list_link2'] : '#'}}"><em class="ml5 mr10 cor_2">▪</em>{{isset($block1['right_down_list_title2']) ? $block1['right_down_list_title2'] : ''}}</a>
                        </li>
                        <li class="fz18 lh44">
                            <a class="db ell" target="_blank" href="{{isset($block1['right_down_list_link3']) ? $block1['right_down_list_link3'] : '#'}}"><em class="ml5 mr10 cor_2">▪</em>{{isset($block1['right_down_list_title3']) ? $block1['right_down_list_title3'] : ''}}</a>
                        </li>
                        <li class="fz18 lh44">
                            <a class="db ell" target="_blank" href="{{isset($block1['right_down_list_link4']) ? $block1['right_down_list_link4'] : '#'}}"><em class="ml5 mr10 cor_2">▪</em>{{isset($block1['right_down_list_title4']) ? $block1['right_down_list_title4'] : ''}}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div><!--新闻动态1-->
                    @endforeach
                @endif


           <!--模块二 按照频道分-->




           @foreach(array_keys($channel_arr) as $channel)
               @php
                  if(!isset($arr[$channel])) continue;
                   $block_arr = $arr[$channel];
               @endphp
           @php
           $tags = [];
           $blk = [];
           $bj = 0;
           @endphp
           @foreach($block_arr as $tag => $block)
               @php
                   $tags[$bj] = $tag;
                   $blk[$bj] = $block;
                  $bj++;
               @endphp
           @endforeach

           @php
               $block_ = $blk[0];
               $block2 = json_decode($block_->block2, true)
           @endphp

       <!--新闻动态2-->
        <div id="{{isset($channel_arr[$block_->channel_id]) ? '#channel_' . $channel_arr[$block_->channel_id] : ''}}" class="pt70">
            <p class="tac"><img style="width: 1200px;height:100px;" src="{{isset($block2['img_name']) ? uploads_url($block2['img_name']) : '/vendor/news/images/ico_title_01.png'}}" alt=""/></p>
            <div class="mt40 pt20 tab_area">
                <ul class="fl_dib tab_menu_list">
                    @foreach($tags as $i => $t)
                    <li class="{{ !$i ? 'current' : ''}}"><a href="javascript:void(0);" class="list_a">{{$t}}</a></li>
                    @endforeach
                </ul>
                <div class="mt40 pt5">
                    <!--1-->

                    @php
                    $block2_index = 0;
                    @endphp

                    @foreach($blk as  $block)
                            @php
                                $block2 = json_decode($block->block2, true)
                            @endphp

                    <div class="tab_content cf {{!$block2_index ? '' : 'dn'}}">
                        <div class="fl w630 mr35">
                            <a href="{{isset($block2['big_img_link']) ? $block2['big_img_link'] : '#'}}" target="_blank" class="re db tdn">
                                <img src="{{isset($block2['big_img_name']) ? uploads_url($block2['big_img_name']) : '/vendor/news/images/img_02.png'}}" class="img2"/>
                                <p class="box_mask_sty">
                                    <span class="mask_text_sty"><em class="db pl25 pr25 ell">{{isset($block2['big_img_title']) ? $block2['big_img_title'] : ''}}</em></span>
                                    <span class="mask_bg_sty"></span>
                                </p>
                            </a>
                        </div>
                        <div class="ov">
                            <ul>
                                <li class="mt20 cf bg1">
                                    @php
                                        $list_link1 = isset($block2['list_link1']) ? $block2['list_link1'] : '#';
                                    @endphp
                                    <div class="fl"><img style="cursor:pointer;" onclick="window.open('{{$list_link1}}', '_blank')" src="{{isset($block2['list_img1_name']) ? uploads_url($block2['list_img1_name']) : '/vendor/news/images/img_03.png'}}" class="img3"/></div>
                                    <div class="ov">
                                        <div class="pl10 pr10">
                                            <p class="mt15 fz18 fwb cor_3 ell" style="cursor:pointer;" onclick="window.open('{{$list_link1}}', '_blank')">{{isset($block2['list_title1']) ? $block2['list_title1'] : ''}}</p>
                                            <p class="mt10 fz14 lh24 cor_1" style="cursor:pointer;" onclick="window.open('{{$list_link1}}', '_blank')">{{isset($block2['list_intro1']) ? mb_text_bytecut($block2['list_intro1'], 42) : ''}}<a href="{{isset($block2['list_link1']) ? $block2['list_link1'] : '#'}}" target="_blank" class="cor_4 ml5">【详情】</a></p>
                                        </div>
                                    </div>
                                </li>
                                <li class="mt25 cf bg1">
                                    @php
                                        $list_link2 = isset($block2['list_link2']) ? $block2['list_link2'] : '#';
                                    @endphp
                                    <div class="fl"><img style="cursor:pointer;" onclick="window.open('{{$list_link2}}', '_blank')" src="{{isset($block2['list_img2_name']) ? uploads_url($block2['list_img2_name']) : '/vendor/news/images/img_04.png'}}" class="img3"/></div>
                                    <div class="ov">
                                        <div class="pl10 pr10">
                                            <p class="mt15 fz18 fwb cor_3 ell" style="cursor:pointer;" onclick="window.open('{{$list_link2}}', '_blank')">{{isset($block2['list_title2']) ? $block2['list_title2'] : ''}}</p>
                                            <p class="mt10 fz14 lh24 cor_1" style="cursor:pointer;" onclick="window.open('{{$list_link2}}', '_blank')">{{isset($block2['list_intro2']) ? mb_text_bytecut($block2['list_intro2'], 42) : ''}}<a href="{{isset($block2['list_link2']) ? $block2['list_link2'] : '#'}}" target="_blank" class="cor_4 ml5">【详情】</a></p>
                                        </div>
                                    </div>
                                </li>


                                <li class="mt25 cf bg1">
                                    @php
                                        $list_link3 = isset($block2['list_link3']) ? $block2['list_link3'] : '#';
                                    @endphp
                                    <div class="fl"><img style="cursor:pointer;" onclick="window.open('{{$list_link3}}', '_blank')" src="{{isset($block2['list_img3_name']) ? uploads_url($block2['list_img3_name']) : '/vendor/news/images/img_04.png'}}" class="img3"/></div>
                                    <div class="ov">
                                        <div class="pl10 pr10">
                                            <p class="mt15 fz18 fwb cor_3 ell" style="cursor:pointer;" onclick="window.open('{{$list_link3}}', '_blank')">{{isset($block2['list_title3']) ? $block2['list_title3'] : ''}}</p>
                                            <p class="mt10 fz14 lh24 cor_1" style="cursor:pointer;" onclick="window.open('{{$list_link3}}', '_blank')">{{isset($block2['list_intro3']) ? mb_text_bytecut($block2['list_intro3'], 42) : ''}}<a href="{{isset($block2['list_link3']) ? $block2['list_link3'] : '#'}}" target="_blank" class="cor_4 ml5">【详情】</a></p>
                                        </div>
                                    </div>
                                </li>

                            </ul>
                        </div>
                    </div><!--tab_content-->

                    @php
                        $block2_index++;
                    @endphp
                    @endforeach

                    <!--2-->
                    <!--3-->

                </div>
            </div>
        </div><!--新闻动态2-->



          @endforeach






            @if(isset($arr[\App\Models\News\Blocks::TYPE_BLOCK_3]))
                @foreach($arr[\App\Models\News\Blocks::TYPE_BLOCK_3] as $id => $block)
                    @php
                        $block3 = json_decode($block->block3, true);

                    $channel_id_l = isset($channel_arr[$block->channel_id]) ? 'channel_' . $channel_arr[$block->channel_id] : '';
                    @endphp

        <!--视频动态-->

         <a id="{{$channel_id_l}}" name="{{$channel_id_l}}"></a>
        <div  class="pt70">
            @if(isset($block3['img_infos'][0]))
            <p class="tac"><img style="width: 1200px;height:100px;" src="{{isset($block3['img_name']) ? uploads_url($block3['img_name']) : ''}}" alt=""/></p>
                <div class="video_player">
                    <video id="video_player" class="bg2" preload="auto" src="{{isset($block3['img_infos'][0]['link']) ? $block3['img_infos'][0]['link'] : ''}}" controls="controls" width="100%" height="100%" x-webkit-airplay="true" webkit-playsinline="" playsinline="true"></video>
                </div>
            <p class="fz20 pt25 pb25 tac cor_3"></p>
            @endif
            <ul class="video_list_src fl_dib">
                @if(isset($block3['img_infos']))
                  @foreach($block3['img_infos'] as $i => $b3)
                <li class="{{!$i ? 'active' : ''}}">
                    <div class="re">
                        <img src="{{isset($b3['img_name']) ? uploads_url($b3['img_name']) : ''}}" class="img4" alt="{{isset($b3['title']) ? $b3['title'] : ''}}"/>
                        <input type="hidden" class="vlink" value="{{isset($b3['link']) ? $b3['link'] : ''}}"/>
                        <i class="play_btn_min"></i>
                    </div>

                    <span class="border_pos"></span>

                </li>
                  @endforeach
                @endif
            </ul>
        </div><!--视频动态-->

                @endforeach
            @endif



            @if(isset($arr[\App\Models\News\Blocks::TYPE_BLOCK_4]))

                @foreach($arr[\App\Models\News\Blocks::TYPE_BLOCK_4] as $id => $block)
                    @php
                        $block4 = json_decode($block->block4, true);
                    $channel_id_l = isset($channel_arr[$block->channel_id]) ? 'channel_' . $channel_arr[$block->channel_id] : '';
                    @endphp

        <!--新闻动态-->
        <a id="{{$channel_id_l}}" name="{{$channel_id_l}}"></a>
        <div  class="pt70">
            <p class="tac"><img style="width: 1200px;height:100px;" src="{{isset($block4['img_name']) ? uploads_url($block4['img_name']) : ''}}" alt=""/></p>
            <ul class="fl_dib news_img_text">

                @if(isset($block4['img_infos']))
                @foreach($block4['img_infos'] as $infos)
                <li>
                    <a href="{{isset($infos['link']) ? $infos['link'] : ''}}" target="_blank" class="re db tdn">
                        <img src="{{isset($infos['img_name']) ? uploads_url($infos['img_name']) : ''}}" class="img5"/>
                        <p class="box_mask_sty">
                            <span class="mask_text_sty"><em class="db pl25 pr25 ell">{{isset($infos['title']) ? $infos['title'] : ''}}</em></span>
                            <span class="mask_bg_sty"></span>
                        </p>
                    </a>

                    @if(isset($infos['list_infos']) && $infos['list_infos'])

                    @php
                        $list_infos_ = preg_split('|\n|', $infos['list_infos']);
                    @endphp

                    <div class="news_list_fz">
                        @foreach($list_infos_ as $list_infos_v)
                            @php
                                if(!$list_infos_v) continue;
                                list($title, $link) = explode("#", $list_infos_v);
                            @endphp
                        <a href="{{$link}}" target="_blank"><em class="mr10 cor_5">▪</em>{{$title}}</a>
                        @endforeach
                    </div>
                    @endif

                </li>
                @endforeach
                @endif
            </ul>
        </div><!--新闻动态-->

                    @endforeach
                @endif


    </div><!--w1200-->




        @if(isset($arr[\App\Models\News\Blocks::TYPE_BLOCK_5]))

            @foreach($arr[\App\Models\News\Blocks::TYPE_BLOCK_5] as $id => $block)
                @php
                    $block5 = json_decode($block->block5, true);
                 $channel_id_l = isset($channel_arr[$block->channel_id]) ? 'channel_' . $channel_arr[$block->channel_id] : '';
                @endphp


    <!--聚焦大图-->
                    <a id="{{$channel_id_l}}" name="{{$channel_id_l}}"></a>
                    <div class="pt70">
        <p class="tac"><img style="width: 1200px;height:100px;"  src="{{isset($block5['img_name']) ? uploads_url($block5['img_name']) : ''}}" alt="聚焦大图"/></p>
        <!--轮播-->
        <div class="mt40 pt5">
            <div class="wF_mask">
                <div class="wF_mask_pic">
                    <ul class="wF_mask_pic_ul">

                        @if(isset($block5['img_infos']))
                            @foreach($block5['img_infos'] as $infos)
                        <li><a href="{{isset($infos['link']) ? $infos['link'] : '#'}}" target="_blank"><img src="{{isset($infos['img_name']) ? uploads_url($infos['img_name']) : ''}}" alt="" title="{{isset($infos['title']) ? $infos['title'] : ''}}" /></a></li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

            @endforeach
        @endif




    @if(isset($arr[\App\Models\News\Blocks::TYPE_BLOCK_6]))

        @foreach($arr[\App\Models\News\Blocks::TYPE_BLOCK_6] as $id => $block)
            @php
                $block6 = json_decode($block->block6, true);
             $channel_id_l = isset($channel_arr[$block->channel_id]) ? 'channel_' . $channel_arr[$block->channel_id] : '';
            @endphp



            <a id="{{$channel_id_l}}" name="{{$channel_id_l}}"></a>
    <!--往届回顾-->
    <div class="pt70">
        <p class="tac"><img style="width: 1200px;height:100px;" src="{{isset($block6['img_name']) ? uploads_url($block6['img_name']) : ''}}" alt="往届回顾"/></p>
        <!--轮播-->
        <div class="move_box">
            <div class="box_in">
                <ul class="fl_dib">


                    @if(isset($block6['img_infos']))
                        @foreach($block6['img_infos'] as $infos)

                    <li>
                        <a href="{{isset($infos['link']) ? $infos['link'] : ''}}" target="_blank" class="tag_box_area">
                            <img src="{{isset($infos['img_name']) ? uploads_url($infos['img_name']) : ''}}"/>
                            <p class="box_mask_sty">
                                <span class="mask_text_sty_2"><em class="db pl25 pr25 ell">{{isset($infos['title']) ? $infos['title'] : ''}}</em></span>
                                <span class="mask_bg_sty"></span>
                            </p>
                        </a>
                    </li>

                        @endforeach
                    @endif

                </ul>
            </div>
            <a href="javascript:void(0);" class="box_btn_next"></a>
            <a href="javascript:void(0);" class="box_btn_prev"></a>
        </div><!-- #move_box -->
    </div><!--往届回顾-->

        @endforeach
    @endif







        @if(isset($arr[\App\Models\News\Blocks::TYPE_BLOCK_7]))

            @foreach($arr[\App\Models\News\Blocks::TYPE_BLOCK_7] as $id => $block)
                @php
                    $block7 = json_decode($block->block7, true);
                    $channel_id_l = isset($channel_arr[$block->channel_id]) ? 'channel_' . $channel_arr[$block->channel_id] : '';
                @endphp

    <!--图片中心-->
            <a id="{{$channel_id_l}}" name="{{$channel_id_l}}"></a>
    <div class="mt80 pt20">
        <p class="tac"><img style="width: 1200px;height:100px;"  src="{{isset($block7['img_name']) ? uploads_url($block7['img_name']) : ''}}" alt="图片中心"/></p>
        <!--轮播-->
        <div class="mt80 pt5">
            <div class="pic_tac_area">
                <div class="featured_progress">
                    <div class="wjy_pic">
                        <ul class="wjy_pic_ul">
                            @if(isset($block7['img_infos']))
                                @foreach($block7['img_infos'] as $infos)

                            <li><a href="{{isset($infos['link']) ? $infos['link'] : ''}}" target="_blank" title=""><img src="{{isset($infos['img_name']) ? uploads_url($infos['img_name']) : ''}}" alt="{{isset($infos['title']) ? $infos['title'] : ''}}" title="{{isset($infos['title']) ? $infos['title'] : ''}}" /></a></li>

                                @endforeach
                            @endif
                        </ul>
                    </div><!-- #wjy_pic -->
                </div><!-- #featured_progress -->
            </div>
        </div>
    </div><!--图片中心-->

            @endforeach
        @endif


@if(isset($arr[\App\Models\News\Blocks::TYPE_BLOCK_8]))

    @foreach($arr[\App\Models\News\Blocks::TYPE_BLOCK_8] as $id => $block)
        @php
            $block8 = json_decode($block->block8, true);
         $channel_id_l = isset($channel_arr[$block->channel_id]) ? 'channel_' . $channel_arr[$block->channel_id] : '';
        @endphp

            <a id="{{$channel_id_l}}" name="{{$channel_id_l}}"></a>
    <!--合作伙伴-->
    <div class="mt95 pt30">
        <div class="w1200 mar_auto">
            <p class="tac"><img src="/vendor/news/images/ico_title_06.png" alt="合作伙伴"/></p>
            <ul class="partner_list fl_dib">
                @if(isset($block8['img_infos']))
                    @foreach($block8['img_infos'] as $infos)
                        @php
                            $link = isset($infos['link']) ?  $infos['link'] : '';
                        @endphp
                        @if($link)
                        <li><img onclick="window.open('{{$link}}', '_blank')" src="{{isset($infos['img_name']) ? uploads_url($infos['img_name']) : ''}}"/></li>
                        @else
                        <li><img src="{{isset($infos['img_name']) ? uploads_url($infos['img_name']) : ''}}"/></li>
                        @endif

                    @endforeach
                @endif
            </ul>
        </div>
    </div>

        @endforeach
    @endif




</div><!--box-->

<script type="text/javascript" src="http://365jia.cn/js/jQuery/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="/vendor/news/js/featured_progress.js"></script>
<script type="text/javascript" src="/vendor/news/js/wF_mask.js"></script>
<script type="text/javascript">
    $(function() {
        //根据导航的个数来计算每个菜单的宽度
        (function() {
            var menuLiNum = $("#menu").find(".menu_li").length,
                menuWidth = $("#menu .menu_ul").width(),
                menuLiWidth = menuWidth/menuLiNum;
            $(".menu_li").width(menuLiWidth);
        } ());

        (function() {
            $("#menu .menu_li").click(function() {
              $(this).addClass('current').siblings().removeClass("current");
            });
        } ());

        //控制导航条位置
        (function() {
            var navPosTop = $("#menu").offset().top;
            $(window).scroll(function () {
                var sTop = $(window).scrollTop();
                if (sTop >= navPosTop) {
                    $("#menu").addClass("current");
                } else {
                    $("#menu").removeClass("current");
                }
            });
        } ());

        //新闻动态tab切换
        (function() {
            $(".tab_menu_list li").click(function() {
                var _this = $(this),
                    index = _this.index();

                //tab_content
                _this.addClass("current").siblings().removeClass("current");
                //$(".tab_area .tab_content").eq(index).removeClass("dn").siblings().addClass("dn");
                _this.parent().next().find('.tab_content').eq(index).removeClass("dn").siblings().addClass("dn");


            });
        } ());

        //视频
        (function() {
            $(".video_list_src li").click(function() {

                var textSrc = $(this).find('img').attr('alt');
                var videoSrc = $(this).find('.vlink').val();
                $(this).addClass('active').siblings().removeClass('active');
                //console.log(textSrc);
                $(".video_player").find("video").attr('src',videoSrc);
                document.getElementById("video_player").play();
                $("#video_player_title").text(textSrc);
                $(".video_player .play_btn_max").hide();
            })
        } ());

        //往届回顾
        ;(function () {

            $.each($('.move_box'), function (idx, item) {
                move($(item))
            })

            function move (obj) {
                var item = obj.find('.box_in'),
                    ul = item.find('ul'),
                    li = ul.find('li'),
                    liW= li.outerWidth(true),
                    liS = li.size(),
                    flag = 0, stopMove = false,
                    pageLINUm = 3, //表示整体展示的li数量
                    btn = obj.find('.box_btn_next, .box_btn_prev');

                if (liS <= pageLINUm) {
                    btn.hide();
                }
                ul.width(liW * liS);

                btn.bind({
                    click: function () {
                        var $this = $(this);
                        if (!!stopMove) return;
                        stopMove = true;
                        if ($this.hasClass('box_btn_next')) {
                            flag = (flag + pageLINUm > liS - pageLINUm) ? liS - pageLINUm : flag + pageLINUm;
                        } else {
                            flag = (flag - pageLINUm < 0) ? 0 : flag - pageLINUm;
                        }

                        ul.animate({
                            left: -liW * flag
                        },800,function () {
                            btn.show();
                            if (flag >= (liS - pageLINUm) || flag <= 0) {
                                $this.hide();
                            } else {
                            }
                            stopMove = false;
                        });
                    }
                });
            }

        } ());

    })
</script>
</body>
</html>