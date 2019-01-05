@extends('vendor.zhuanti.public_vote.layout')
@section('content')
<div class="box">
  <div><img src="{{ uploads_url($proj->configs->vote->vote_img) }}" class="max_wp100"></div>

  <div class="pa10">
    <!--填写信息区域-->
    <div class="mt5 pa20 b1 bgf">
      <form name="registerform" id="registerform" class="registerform" action="{{ $proj->path }}/reg" method="post" enctype="multipart/form-data">

        @foreach($myform as $form)

          @if( in_array($form->type, ['string' , 'integer', 'name', 'phone', 'idcard', 'passport', 'email', 'qq', 'address', 'age', 'city']))
            <div class="mt10 cf">
              <div class="fl mr10  w100 tar"><p class="fz14 lh34"><span class="mr5 cor_4">{{ isset($form->required) ? '*' : '' }}</span>{{$form->name}}：</p></div>
              <div class="ov"><input type="text" name="info_{{$form->field}}" placeholder="{{$form->comment}}"   datatype="s6-18" errormsg="昵称至少6个字符,最多18个字符！"  class="ipt_01"></div>
            </div>
          @endif

          @if($form->type == 'radio')
            <div class="mt10 cf">
              <div class="fl mr10 w100 tar"><p class="fz14 lh34"><span class="mr5 cor_4">{{ isset($form->required) ? '*' : '' }}</span>{{$form->name}}：</p></div>
              <div class="ov">
                <p  style="height:34px;" class="lh34">
                  @foreach($form->options->radio->options as $op)
                    <input type="radio" name="info_{{$form->field}}" value="{{$op->key}}" {{isset($op->is_default) && $op->is_default > 0 ? 'checked' : '' }}/> {{$op->name}}
                  @endforeach
                </p>
              </div>
            </div>
          @endif

          @if($form->type == 'checkbox')
            <div class="mt10 cf">
              <div class="fl mr10 w100 tar"><p class="fz14 lh34"><span class="mr5 cor_4">{{ isset($form->required) ? '*' : '' }}</span>{{$form->name}}：</p></div>
              <div class="ov">
                <p  style="height:34px;" class="lh34">
                  @foreach($form->options->checkbox->options as $op)
                    <input type="checkbox" name="info_{{$form->field}}[]" value="{{$op->key}}" {{isset($op->is_default) && $op->is_default > 0 ? 'checked' : '' }}/> {{$op->name}}
                  @endforeach
                </p>
              </div>
            </div>
          @endif

          @if($form->type == 'select')
            <div class="mt10 cf">
              <div class="fl mr10 w100 tar"><p class="fz14 lh34"><span class="mr5 cor_4">{{ isset($form->required) ? '*' : '' }}</span>{{$form->name}}：</p></div>
              <div class="ov">
                <p style="height:34px;" class="lh34">
                  <select name="info_{{$form->field}}" style="height: 34px;width:100%;">
                    @foreach($form->options->select->options as $op)
                      <option value="{{$op->key}}">{{$op->name}}</option>
                    @endforeach
                  </select>
                </p>
              </div>
            </div>
          @endif

          @if($form->type == 'text')
            <div class="mt10 cf">
              <div class="fl mr10 w100 tar"><p class="fz14 lh34"><span class="mr5 cor_4">{{ isset($form->required) ? '*' : '' }}</span>{{$form->name}}：</p></div>
              <div class="ov"><textarea name="info_{{$form->field}}" id="info_{{$form->field}}" placeholder="{{$form->comment}}" class="textarea"></textarea></div>
            </div>
          @endif

          @if(in_array($form->type,['datetime', 'birthday']))
            <div class="mt10 cf">
              <div class="fl mr10 w100 tar"><p class="fz14 lh34"><span class="mr5 cor_4">{{ isset($form->required) ? '*' : '' }}</span>{{$form->name}}：</p></div>
              <div class="ov"><input type="date" name="info_{{$form->field}}" placeholder="{{$form->comment}}" class="ipt_01"></div>
            </div>
          @endif


          @if($form->type == 'upload')
            <div class="mt15 cf">
              <p class="fz14 lh18"><div class="fl mr10 fz14 w100 tar"><span class="mr5 cor_4">{{ isset($form->required) ? '*' : '' }}</span>{{$form->name}}：</div><span class="cor_2">{{$form->comment}}</span></p>
            </div>
            <div class="mt10 cf">
              <div class="fl mr10 vh w100 tar"><p class="fz14 lh34"><span class="mr5 cor_4">*</span>手机号：</p></div>
              <div class="ov">
                <div class="upload_btn new">
                  <input type="file" class="input_file" name="info_{{$form->field}}">
                </div>
              </div>
            </div>
        @endif

      @endforeach


      <!--提交报名-->
        <div class="mt45 mb10"><a href="javascript:void(0);" id="mysubmits" class="btn_1" style="{{isset($proj->configs->vote->vote_bgcolor) ? 'background-color: '.$proj->configs->vote->vote_bgcolor : ''}}">提交报名</a></div>
      </form>
    </div>
    <!--填写信息区域-->
  </div><!--pa10-->

  <!--底部菜单栏-->
@include('zhuanti::public_vote._nav')


<!--报名提交成功-弹窗-->
  <div id="bomb_boxs" class="bomb_box2 dn">
    <div class="bomb_box_text tac">
      <i class="btn_colse_bomb_box"></i>
      <div class="mt15"><img src="images/ico_correct_sym.png" id="alert_img" class="img2"></div>
      <p class="mt15 fz18 lh18 cor_3 fwb" id="alert_title">报名提交成功</p>
      <p class="mt15 mb15 fz14 lh14 cor_2 fwb" id="alert_msg">小编正在玩命审核中，请稍后！</p>
    </div>
  </div>

</div><!--box-->

<script type="text/javascript" src="{{ URL::asset('public_vote') }}/js/bombbox.1.0.js"></script>
<script type="text/javascript" src="{{ URL::asset('public_vote') }}/js/upload.js"></script>

<script type="text/javascript">
    $(function() {
        //关闭
        $(".btn_colse_bomb_box").tap(function(){
            $.bombbox.hide2();
            if($('#bomb_boxs').hasClass("succ")){
                window.location = @json($proj->path);
            }
        });
        function showErr(msg) {
            $('#bomb_boxs').removeClass('succ');
            $('#alert_img').attr('src' , 'images/ico_error.png');
            $('#alert_title').html('报名提交失败');
            $('#alert_msg').html(msg);
            $(".bomb_box2").bombbox();
        }
        /*提交表单*/
        var isCommit = false;
        $("#mysubmits").click(function() {
            if (isCommit) {
                return;
            }
            @foreach($myform as $form)
                @if(isset($form->required) && $form->required == 'on' && $form->type == 'phone')
                    var info_{{$form->field}} = $('input[name="info_{{$form->field}}"]').val();
                    var phoneReg = /^[1]\d{10}$/;
                    var telReg = /^\d{4}\-?\d{7,8}$/;
                    if(!phoneReg.test(info_{{$form->field}}) && !telReg.test(info_{{$form->field}})) {
                        showErr('{{$form->comment}}');
                        return false;
                    }
                @endif
                @if(isset($form->required) && $form->required == 'on' && $form->type == 'text')
                    if($("#info_{{$form->field}}").val() == '') {
                        showErr('{{$form->comment}}');
                        return false;
                    }
                @endif
                @if(isset($form->required) && $form->required == 'on' && $form->type != 'phone' && $form->type != 'text')
                    if($('input[name="info_{{$form->field}}"]').val() == '') {
                        showErr('{{$form->comment}}');
                        return false;
                    }
                @endif
            @endforeach

            isCommit = true;
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: $('#projPath').val() + '/register',
                data: new FormData($('#registerform')[0]),
                cache:false,
                processData: false,
                contentType: false,
                success: function (data){
                    if (data.code === 10014) {
                        location.href = path+'/login_start?redirectUrl='+encodeURIComponent(window.location.href);
                        return;
                    }else if(data.code > 0) {
                        $('#bomb_boxs').removeClass('succ');
                        $('#alert_img').attr('src' , 'images/ico_error.png');
                        $('#alert_title').html('报名提交失败');
                    } else {
                        $('#bomb_boxs').addClass('succ');
                        $('#alert_img').attr('src' , 'images/ico_correct_sym.png');
                        $('#alert_title').html('报名提交成功');
                    }
                    $('#alert_msg').html(data.msg);
                    $(".bomb_box2").bombbox();
                    isCommit = false;
                },
                error: function () {
                    showErr('提交失败,请确认提交信息重新填写');
                    isCommit = false;
                }
            });

        });
    })
</script>
@include('zhuanti::public_vote._share')
@endsection