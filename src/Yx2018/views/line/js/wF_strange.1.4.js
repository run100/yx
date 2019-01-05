/* 
* wF_progress.js
* © 2014 by wangjinyao http://www.webfeike.net/
* v1.0 2013-10-25 最初功能
* v1.1 2013-11-26 添加全局变量gv，修改触发左右按钮条件
* v1.2 2014-07-19 wF_strange.js 基础上修改；添加进度 无缝滚动 ,后需优化
* v1.2 2014-11-12 wF_strange.1.4.js 基础上修改,后需优化

*/

$(function() {
  if(!($(".wF_progress").size() > 0)) return false;
  // if(!($("#js_wF_progress img"))) return false;

  // var chief = $("#js_wF_progress"); 

  $(".wF_progress").each(function (index , element) {
    var chief = $(element);
    var btn_area = '<div class="prev"></div><div class="next"></div>';
    var gv = 0;
    var i = 0;
    // var chief = $(".wF_progress"); 
    var li_item = chief.find(".wjy_pic li");
    var li_item_img = chief.find(".wjy_pic li img");
    var li_item_out = chief.find(".wjy_pic ul");
    
    var iw = li_item.width();
    var w_lio = li_item.width()*(li_item.length+1);
    li_item_out.css({"width":w_lio});
    var num_area = '<div class="wjy_num"><ul></ul></div>';
    var txt_area = '<div class="wjy_txt"><ul></ul></div>';
    chief.append(function(){return num_area;}); //添加序号
    //exp_item.eq(0).addClass("active");  
    for ( k=0; k<li_item.length;k++ ){ //序列号取值，以图片个数
      chief.find(".wjy_num ul").append(function(){
        return '<li><span class="wjy_num_pro"></span></li>';
      });
    };
   
    
    chief.append(function(){return txt_area;});
    for ( o=0; o<li_item.length;o++ ){ //序列号取值，以图片个数
      chief.find(".wjy_txt ul").append(function(){
        return '<li><div class="wjy_txt_c"><h3></h3></div><p class="wjy_txt_bg"></p></li>';
      });
    };

     var txt_item = chief.find(".wjy_txt li");
     txt_item.eq(0).addClass("active");  

    var times = 5000;
    var num_item = chief.find(".wjy_num li");
    num_item.eq(0).addClass("active");


    function value() {
        var i = gv;
        var txt_c = li_item_img.eq(i).attr("title");
        var txt_c_sub = li_item_img.eq(i).attr("text");
        chief.find(".wjy_txt .wjy_txt_c").eq(i).find("h3").html(txt_c);
        chief.find(".wjy_txt .wjy_txt_c").eq(i).find("p").html(txt_c_sub);
    };

    

    //添加一条
    var add_li_item = li_item.first().html();
    li_item_out.append('<li>' + add_li_item + '</li>');
    //点击序号操作
    chief.find(".wjy_num li").live("click",function(){
        num_item.each(function(){
          num_item.removeClass("active")
        });
        $(this).addClass("active");
        var i = $(this).index(); //根据当前序号赋值
        gv = i;

        li_item_out.stop().animate({left:-iw*i},400);

        num_item.each(function(){
          $(this).removeClass("active");
          
        });
        num_item.eq(i).addClass("active");
        
        value();

        txt_item.each(function(){
          $(this).removeClass("active")
        });
        txt_item.eq(i).addClass("active");
        
    });

    chief.find(".prev").live("click",function(){
        var i = gv;
        if(i == 0){ //到尽头折返
            li_item_out.css({"left":-iw*li_item.length});
            gv = li_item.length-1;
        }else{
            gv = i-1;
        };
        var i = gv;
        li_item_out.stop().animate({left:-iw*i},400);

        txt_item.each(function(){
          $(this).removeClass("active")
        });
        txt_item.eq(i).addClass("active");

        num_item.each(function(){
          $(this).removeClass("active")
        });
        num_item.eq(i).addClass("active");
        value();

    });
    
    value();

    function next() {
        var i = gv;
        if(i > li_item.length-1){ //到尽头折返
            li_item_out.css({"left":0});
            gv = 1; //循环一轮 无缝衔接
            //alert("33");

            
        }else{
            gv = i+1;
        };
        
        var i = gv;
        
        value();
        
        li_item_out.stop().animate({left:-iw*i},400);

        num_item.each(function(){
          $(this).removeClass("active");
          
        });
        if (i < li_item.length || i > li_item.length){
            num_item.eq(i).addClass("active");
            
        }else {
            num_item.first().addClass("active");
        }

        txt_item.each(function(){
          $(this).removeClass("active");
          
        });
        if (i < li_item.length || i > li_item.length){
            txt_item.eq(i).addClass("active");
            
        }else {
            txt_item.first().addClass("active");
        }

    };

    function rules() {
        next();
    };
    chief.find(".next").live("click",function(){
        next();
    }); 

    
    chief.append(btn_area);
    //chief.mouseover(function(){$(this).addClass("wjy_hover");});
    //chief.mouseout(function(){$(this).removeClass("wjy_hover");});
    var timer1 = setInterval(function(){rules()},times); //自动执行向后函数
    chief.mouseover(function(){ clearInterval(timer1); });//鼠标悬停停止
    chief.mouseout(function(){ timer1 = setInterval(function(){rules()},times); }); //鼠标离开继续

  });
});