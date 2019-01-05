function getmatrix(a,b,c,d,e,f){
    var aa=Math.round(180*Math.asin(a)/ Math.PI);
    var bb=Math.round(180*Math.acos(b)/ Math.PI);
    var cc=Math.round(180*Math.asin(c)/ Math.PI);
    var dd=Math.round(180*Math.acos(d)/ Math.PI);
    var deg=0;
    if(aa==bb||-aa==bb){
        deg=dd;
    }else if(-aa+bb==180){
        deg=180+cc;
    }else if(aa+bb==180){
        deg=360-cc||360-dd;
    }
    return deg>=360?0:deg;
}
function createXuanZhuanImg() {
    var img = $('.xzImg');
    var spanHtml = '<div class="imgDiv" style="display:none;position: relative;left: 0px;top: -20px;width:150px;height:20px;background: #000;opacity: 0.5; cursor:pointer;"><span class="left" style="color:#fff;display: inline-block;width: 50px;">左</span><span class="right" style="color:#fff;display: inline-block;width: 50px;">右</span><span class="commit" style="color:#fff;display: inline-block;width: 50px;">确认</span></div>';
    img.append(spanHtml);
    img.mouseenter(function(e){
        $(this).find('div.imgDiv').show();
    }).mouseleave(function(e){
        $(this).find('div.imgDiv').hide();
    });
    img.on('click', '.left', function(){
        var obj = $(this).parent().parent().find('img');
        var transform = obj.css('transform');
        var deg = 0;

        if(transform != 'none') {
            deg=eval('get'+obj.css('transform'));
        }
        var step=90;
        obj.css({'transform':'rotate('+(deg+step)%360+'deg)'});
    });
    img.on('click', '.right', function(){
        var obj = $(this).parent().parent().find('img');
        var transform = obj.css('transform');
        var deg = 0;
        if(transform != 'none') {
            deg=eval('get'+obj.css('transform'));
        }
        var step=-90;
        obj.css({'transform':'rotate('+(deg+step)%360+'deg)'});
    });
    img.on('click', '.commit', function(){
        var obj = $(this).parent().parent().find('img');
        var transform = obj.css('transform');
        if(transform == 'none'){
            swal('未调整角度');return;
        }
        var deg = eval('get'+obj.css('transform'));
        if(deg == 0){
            swal('未调整角度');return;
        }
        var field = obj.data('field');
        var id = obj.data('id');
        var data = {img:obj.attr('src'), deg:deg, field:field, id:id};
        console.log(data);
        $.post('/admin/player/img_rotate', data, function(res){
            if(res.code == 0){
                obj.attr('src', res.data.url);
                obj.css({'transform':'rotate(0deg)'});
                swal('图片调整成功');
            } else {
                swal('图片调整失败');
            }
        })
    });
}

function addUpdatePlayerStatusEvent(){
    $('.fa-close').click(function(){
        var $btn = $(this);
        var id = $btn.data('id');
        swal({
            title: "确认为审核 不通过 ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "确认",
            closeOnConfirm: false,
            cancelButtonText: "取消"
        }).then(function(ok){
            if (!ok) {
                return false;
            }

            $.ajax({
                method: 'post',
                url: '/admin/player/update_status',
                data: {
                    id:id,
                    checked:2
                },
                success: function (req) {
                    if (req.code == 0) {
                        $btn.parent().parent().parent().find('td:eq(3)').html('审核未通过');
                        swal('审核 不通过 成功！', '', 'success');
                    }else {
                        swal('审核 不通过 成功！', '', 'error');
                    }
                }
            });
        });
    });
    $('.fa-check').click(function(){
        var $btn = $(this);
        var id = $btn.data('id');
        swal({
            title: "确认为审核 通过 ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "确认",
            closeOnConfirm: false,
            cancelButtonText: "取消"
        }).then(function(ok){
            if (!ok) {
                return false;
            }

            $.ajax({
                method: 'post',
                url: '/admin/player/update_status',
                data: {
                    id:id,
                    checked:1,
                },
                success: function (req) {
                    if (req.code == 0) {
                        $btn.parent().parent().parent().find('td:eq(3)').html('审核通过');
                        swal('审核 通过 成功！', '', 'success');
                    }else {
                        swal('未知错误', '', 'error');
                    }
                }
            });
        });
    });
}

function openImages(){
    $('.openImg').click(function(){
        layer.closeAll();
        layer.photos({
            photos: '#layer-photos-'+$(this).data('id')
            ,anim: 5
        });
    })
}