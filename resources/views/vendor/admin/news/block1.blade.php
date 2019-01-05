@extends('admin::news.base')
@section('content')
<div class="box-body">

    <div class="fields-group">
        @include('admin::news.channel',['news_channles_arr'=>$news_channles_arr, 'field' => @$field])

        <div class="form-group  ">

            <label for="info_img" class="col-sm-2 control-label">板块一[导航图标]</label>

            <div class="col-sm-8">
                @if(isset($field['img_name']) && $field['img_name'])
                    <div style="height: 100px;width: 100px;">
                        <img style="width: 100%;height: 100%" src="{{uploads_url(@$field['img_name'])}}">
                    </div>
                @endif
                <input type="hidden" name ="fields[img_name]"  value="{{@$field['img_name']}}">
                <input type="file" class="info_img" name="fields[img]">
                <span class="help-block">
                <i class="fa fa-info-circle"></i>&nbsp;图片说明：jpg/png格式, 1200*100，300k以内
                </span>
            </div>
        </div>



        <div class="form-group ">

            <label for="info_img" class="col-sm-2 control-label">板块一[左图]</label>

            <div class="col-sm-8">
                @if(isset($field['big_img_name']) && $field['big_img_name'])
                    <div style="height: 100px;width: 100px;">
                        <img style="width: 100%;height: 100%" src="{{uploads_url(@$field['big_img_name'])}}">
                    </div>
                @endif
                <input type="hidden" name ="fields[big_img_name]" value="{{@$field['big_img_name']}}">
                <input type="file" class="info_img" name="fields[big_img]">
                <span class="help-block">
                <i class="fa fa-info-circle"></i>&nbsp;图片说明：jpg/png格式, 580*350，300k以内
                </span>
                <div class="form-inline row_top_10"><span>图片标题：</span><input type="text" style="width:550px;" id="big_img_title" name="fields[big_img_title]" value="{{@$field['big_img_title']}}" class="form-control info_phone" placeholder="输入 图片标题"></div>
                <div class="form-inline row_top_10"><span>图片链接：</span><input type="text" style="width:550px;" id="big_img_link" name="fields[big_img_link]" value="{{@$field['big_img_link']}}" class="form-control info_phone" placeholder="输入 图片链接"></div>
            </div>
        </div>



        <div class="form-group  ">

            <label for="info_phone" class="col-sm-2 control-label">板块一[右上-标题]</label>

            <div class="col-sm-8">


                <div class="input-group">

                    <span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
                    <input type="text" id="right_top_title" name="fields[right_top_title]" value="{{@$field['right_top_title']}}" class="form-control info_phone" placeholder="输入 标题">
                    <textarea name="fields[right_top_intro]" class="form-control" rows="5" placeholder="输入 引言">{{@$field['right_top_intro']}}</textarea>
                    <input type="text" id="right_top_link" name="fields[right_top_link]" value="{{@$field['right_top_link']}}" class="form-control info_phone" placeholder="输入 链接">

                </div>


            </div>
        </div>


        <div class="form-group  ">
            <label for="info_phone" class="col-sm-2 control-label">板块一[右下-列表1]</label>
            <div class="col-sm-8">

                <div class="input-group">

                    <span class="input-group-addon"><i class="fa fa-list-alt"></i></span>

                    <input type="text" id="right_down_list_title1" name="fields[right_down_list_title1]" value="{{@$field['right_down_list_title1']}}" class="form-control info_phone" placeholder="输入 标题">
                    <input type="text" id="right_down_list_link1" name="fields[right_down_list_link1]" value="{{@$field['right_down_list_link1']}}" class="form-control info_phone" placeholder="输入 链接">
                </div>


            </div>
        </div>

        <div class="form-group  ">

            <label for="info_phone" class="col-sm-2 control-label">板块一[右下-列表2]</label>

            <div class="col-sm-8">


                <div class="input-group">

                    <span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
                    <input type="text" id="right_down_list_title2" name="fields[right_down_list_title2]" value="{{@$field['right_down_list_title2']}}" class="form-control info_phone" placeholder="输入 标题">
                    <input type="text" id="right_down_list_link2" name="fields[right_down_list_link2]" value="{{@$field['right_down_list_link2']}}" class="form-control info_phone" placeholder="输入 链接">
                </div>


            </div>
        </div>


        <div class="form-group  ">

            <label for="info_phone" class="col-sm-2 control-label">板块一[右下-列表3]</label>

            <div class="col-sm-8">


                <div class="input-group">

                    <span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
                    <input type="text" id="right_down_list_title3" name="fields[right_down_list_title3]" value="{{@$field['right_down_list_title3']}}" class="form-control info_phone" placeholder="输入 标题">
                    <input type="text" id="right_down_list_link3" name="fields[right_down_list_link3]" value="{{@$field['right_down_list_link3']}}" class="form-control info_phone" placeholder="输入 链接">

                </div>


            </div>
        </div>


        <div class="form-group">

            <label for="info_phone" class="col-sm-2 control-label">板块一[右下-列表4]</label>

            <div class="col-sm-8">


                <div class="input-group">

                    <span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
                    <input type="text" id="right_down_list_title4" name="fields[right_down_list_title4]" value="{{@$field['right_down_list_title4']}}" class="form-control info_phone" placeholder="输入 标题">
                    <input type="text" id="right_down_list_link4" name="fields[right_down_list_link4]" value="{{@$field['right_down_list_link4']}}" class="form-control info_phone" placeholder="输入 链接">
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
