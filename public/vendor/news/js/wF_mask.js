
$(function() {
  if(!($(".wF_mask").size() > 0)) return false;


  $(".wF_mask").each(function (index , element) {
    var chief = $(element);
    var btn_area = '<div class="prev"><i></i></div><div class="next"><i></i></div>';
    var gv = 0;
    var i = 0;
    // var chief = $(".wF_mask"); 
    var li_item = chief.find(".wF_mask_pic li");
    var li_item_img = chief.find(".wF_mask_pic li img");
    var li_item_out = chief.find(".wF_mask_pic ul");
    
    var iw = li_item.width();
    var w_lio = li_item.width()*(li_item.length+3);
    li_item_out.css({"width":w_lio,"margin-left": ( $(window).width() - iw ) / 2 - iw });
    
    var num_area = '<div class="wF_mask_num" style="width:'+ iw +'px;margin-left:'+ ( $(window).width() - iw ) / 2 +'px"><span class="text_curr">'+ (gv+1) +'</span>/<span class="text_total">'+ li_item.length +'</span></div>';
    var txt_area = '<div class="wF_mask_txt" style="width:'+ iw +'px;margin-left:'+ ( $(window).width() - iw ) / 2 +'px"><ul></ul></div>';
    chief.append(function(){return num_area;}); //添加序号
    //exp_item.eq(0).addClass("active");  

    
    
    chief.append(function(){return txt_area;});
    chief.append(btn_area);

    chief.find(".prev,.next").css({"width":  ( $(window).width() - iw ) / 2   });
    console.log( ( $(window).width() - iw ) / 2 - iw  )
    for ( o=0; o<li_item.length;o++ ){ //序列号取值，以图片个数
      chief.find(".wF_mask_txt ul").append(function(){
        return '<li><div class="wF_mask_txt_c"><h3></h3></div><p class="wF_mask_txt_bg"></p></li>';
      });
    };

    var txt_item = chief.find(".wF_mask_txt li");
    txt_item.eq(0).addClass("active");  

    var times = 5000;
    var num_item = chief.find(".wF_mask_num li");
    num_item.eq(0).addClass("active"); 


    function value() {
        var i = gv;
        var txt_c = li_item_img.eq(i).attr("title");
        var txt_c_sub = li_item_img.eq(i).attr("text");
        chief.find(".wF_mask_txt .wF_mask_txt_c").eq(i).find("h3").html(txt_c);
        chief.find(".wF_mask_txt .wF_mask_txt_c").eq(i).find("p").html(txt_c_sub);
    };
    
    //添加一条
    var add_li_item = li_item.first().html();
    li_item_out.append('<li>' + add_li_item + '</li>');
    li_item_out.append('<li>' + li_item.eq(1).html() + '</li>');

    var add_li_item2 = li_item.last().html();
    li_item_out.prepend('<li>' + add_li_item2 + '</li>');


    chief.delegate(".prev","click",function(){
        var i = gv;
        if(i == 0){ //到尽头折返
            li_item_out.css({"left":-iw*li_item.length});
            gv = li_item.length-1;
        }else{
            gv = i-1;
        };
        var i = gv;
        if ( gv==li_item.length ){
          chief.find(".text_curr").html(1)
        }else{
          chief.find(".text_curr").html(gv+1)
        }
        li_item_out.stop().animate({left:-iw*i},400);
        txt_item.each(function(){
          $(this).removeClass("active")
        });
        txt_item.eq(i).addClass("active");

        value();
    });
    value();
    function next() {
        var i = gv;
        console.log(li_item.length)
        if(i > li_item.length-1){ //到尽头折返
            li_item_out.css({"left":0});
            gv = 1; //循环一轮 无缝衔接
        }else{
            gv = i+1;
            
            
        };
        
        var i = gv;
        if ( gv==li_item.length ){
          chief.find(".text_curr").html(1)
        }else{
          chief.find(".text_curr").html(gv+1)
        }
        
        value();
        
        li_item_out.stop().animate({left:-iw*i},400);

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
    chief.delegate(".next","click",function(){
        next();
    }); 

    
    
    chief.mouseover(function(){$(this).addClass("wjy_hover");});
    chief.mouseout(function(){$(this).removeClass("wjy_hover");});
    var timer1 = setInterval(function(){rules()},times); //自动执行向后函数
    chief.mouseover(function(){ clearInterval(timer1); });//鼠标悬停停止
    chief.mouseout(function(){ timer1 = setInterval(function(){rules()},times); }); //鼠标离开继续

  });
});