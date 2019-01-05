

$(function() {
  if(!($(".featured_progress").size() > 0)) return false;
  // if(!($("#js_featured_progress img"))) return false;

  // var chief = $("#js_wF_progress"); 

  $(".featured_progress").each(function (index , element) {
    var chief = $(element);
    var btn_area = '<div class="prev"></div><div class="next"></div>';
    var gv = 0;
    var i = 0;
    // var chief = $(".featured_progress");
    var li_item = chief.find(".wjy_pic li");
    var li_item_img = chief.find(".wjy_pic li img");
    var li_item_out = chief.find(".wjy_pic ul");
    
    var iw = li_item.width();
    var w_lio = li_item.width()*(li_item.length+5); //这里加5是因为复制了五个元素
    li_item_out.css({"width":w_lio+760,"margin-left": ( $(window).width() - 660 ) / 2 - 580 }); //这里加上760
    var num_area = '<div class="wjy_num"><ul></ul></div>';
    var txt_area = '<div class="wjy_txt"><ul></ul></div>';
    chief.append(function(){return num_area;}); //添加序号
    //exp_item.eq(0).addClass("active");  
    for ( k=0; k<li_item.length;k++ ){ //序列号取值，以图片个数
      chief.find(".wjy_num ul").append(function(){
        return '<li><span class="wjy_num_pro">'+(k+1)+'</span></li>';
      });
    };
   
    
    chief.append(function(){return txt_area;});
    for ( var o=0; o<li_item.length;o++ ){ //序列号取值，以图片个数
      chief.find(".wjy_txt ul").append(function(){
        return '<li><div class="wjy_txt_c"><h3></h3></div><p class="wjy_txt_bg"></p></li>';
      });
    };

     var txt_item = chief.find(".wjy_txt li");
     txt_item.eq(0).addClass("active");
    li_item.eq(0).addClass("active");

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

    

    //添加前三条li到最后
    //var add_li_item = li_item.first().html();
    li_item_out.append('<li id="qq">' + li_item.eq(0).html()+ '</li>'+'<li>' + li_item.eq(1).html()+'</li>'+'<li>' + li_item.eq(2).html()+'</li>');
    //添加最后两个到最前面
    li_item_out.prepend('<li>'+li_item.eq(-2).html()+'</li>'+'<li>'+li_item.last().html()+'</li>');
    //console.log(chief.find(".wjy_pic li").length)
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

        li_item.each(function() {
          $(this).removeClass("active")
        });
        li_item.eq(i).addClass("active");

        value();

    });
    
    value();

    function next() {
        var i = gv;
        if(i > li_item.length-1){ //到尽头折返
            li_item_out.css({"left":0});
            gv = 1; //循环一轮 无缝衔接
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

      li_item.each(function() {
          $(this).removeClass("active");
      });
      if (gv < li_item.length || gv > li_item.length){
        li_item.eq(gv).addClass("active");
        if(gv==1) {
          $("#qq").removeClass("active"); //加个id是为了解决第二轮该class去不掉的问题
        }
      }else {
        chief.find(".wjy_pic li").eq(gv+2).addClass("active");
      }

    };

    function rules() {
        next();
    };
    chief.find(".next").live("click",function(){
        next();
      //console.log(gv);

    }); 

    
    chief.append(btn_area);
    chief.mouseover(function(){$(this).addClass("wjy_hover");});
    chief.mouseout(function(){$(this).removeClass("wjy_hover");});
    var timer1 = setInterval(function(){rules()},times); //自动执行向后函数
    chief.mouseover(function(){ clearInterval(timer1); });//鼠标悬停停止
    chief.mouseout(function(){ timer1 = setInterval(function(){rules()},times); }); //鼠标离开继续

  });
});