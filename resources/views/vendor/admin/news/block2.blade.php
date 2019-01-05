@extends('admin::news.base')
@section('content')
<div class="box-body">

    <div class="fields-group">
        @include('admin::news.channel',['news_channles_arr'=>$news_channles_arr, 'field' => @$field])

        <div class="form-group">
            <label for="info_img" class="col-sm-2 control-label">板块二[导航图标]</label>
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


        <div class="form-group">
            <label for="info_phone" class="col-sm-2 control-label">板块二[标签]</label>
            <div class="col-sm-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                    <input type="text" id="sub_block_tag" name="fields[sub_block_tag]" value="{{@$field['sub_block_tag']}}" class="form-control info_phone" placeholder="输入 标签">
                </div>
            </div>
        </div>


        <div class="form-group  ">
            <label for="info_img" class="col-sm-2 control-label">板块二[左图]</label>
            <div class="col-sm-8">
                @if(isset($field['big_img_name']) && $field['big_img_name'])
                    <div style="height: 100px;width: 100px;">
                        <img style="width: 100%;height: 100%" src="{{uploads_url(@$field['big_img_name'])}}">
                    </div>
                @endif
                <input type="hidden" name ="fields[big_img_name]" value="{{@$field['big_img_name']}}">
                <input type="file" class="info_img" name="fields[big_img]">
                <span class="help-block">
                  <i class="fa fa-info-circle"></i>&nbsp;图片说明：jpg/png格式, 630*380，300k以内
                </span>
                <div class="form-inline row_top_10"><span>图片标题：</span><input type="text" style="width:550px;" id="big_img_title" name="fields[big_img_title]" value="{{@$field['big_img_title']}}" class="form-control info_phone" placeholder="输入 图片标题"></div>
                <div class="form-inline row_top_10"><span>图片链接：</span><input type="text" style="width:550px;" id="big_img_link" name="fields[big_img_link]" value="{{@$field['big_img_link']}}" class="form-control info_phone" placeholder="输入 图片链接"></div>
            </div>
        </div>


        <div class="form-group ">
            <label for="info_img" class="col-sm-2 control-label">板块二[右-列表1]</label>
            <div class="col-sm-8">
                    @if(isset($field['list_img1_name']) && $field['list_img1_name'])
                    <div style="height: 100px;width: 100px;">
                    <img style="width: 100%;height: 100%" src="{{uploads_url(@$field['list_img1_name'])}}">
                    </div>
                    @endif
                <input type="hidden" name ="fields[list_img1_name]" value="{{@$field['list_img1_name']}}">
                <input type="file" class="info_img " name="fields[list_img1]">
                <span class="help-block">
                  <i class="fa fa-info-circle"></i>&nbsp;图片说明：jpg/png格式, 165*99，100k以内
                </span>
                <input type="text" id="list_title1" name="fields[list_title1]" value="{{@$field['list_title1']}}" class="form-control row_top_10 info_phone" placeholder="输入 标题">
                <textarea class="form-control row_top_10" rows="5" placeholder="输入 引言" name="fields[list_intro1]">{{@$field['list_intro1']}}</textarea>
                <input type="text" id="list_link1" name="fields[list_link1]" value="{{@$field['list_link1']}}" class="form-control row_top_10 info_phone" placeholder="输入 链接">
            </div>
        </div>


        <div class="form-group ">
            <label for="info_img" class="col-sm-2 control-label">板块二[右-列表2]</label>
            <div class="col-sm-8">
                    @if(isset($field['list_img2_name']) && $field['list_img2_name'])
                    <div style="height: 100px;width: 100px;">
                        <img style="width: 100%;height: 100%" src="{{uploads_url(@$field['list_img2_name'])}}">
                    </div>
                    @endif
                <input type="hidden" name ="fields[list_img2_name]" value="{{@$field['list_img2_name']}}">
                <input type="file" class="info_img " name="fields[list_img2]">
                <span class="help-block">
                  <i class="fa fa-info-circle"></i>&nbsp;图片说明：jpg/png格式, 165*99，100k以内
                </span>
                <input type="text" id="list_title2" name="fields[list_title2]" value="{{@$field['list_title2']}}" class="form-control row_top_10 info_phone" placeholder="输入 标题">
                <textarea class="form-control row_top_10" rows="5" placeholder="输入 引言" name="fields[list_intro2]">{{@$field['list_intro2']}}</textarea>
                <input type="text" id="list_link2" name="fields[list_link2]" value="{{@$field['list_link2']}}" class="form-control row_top_10 info_phone" placeholder="输入 链接">
            </div>
        </div>


        <div class="form-group ">
            <label for="info_img" class="col-sm-2 control-label">板块二[右-列表3]</label>
            <div class="col-sm-8">
                @if(isset($field['list_img3_name']) && $field['list_img3_name'])
                    <div style="height: 100px;width: 100px;">
                        <img style="width: 100%;height: 100%" src="{{uploads_url(@$field['list_img3_name'])}}">
                    </div>
                @endif
                <input type="hidden" name ="fields[list_img3_name]" value="{{@$field['list_img3_name']}}">
                <input type="file" class="info_img " name="fields[list_img3]">
                <span class="help-block">
                  <i class="fa fa-info-circle"></i>&nbsp;图片说明：jpg/png格式, 165*99，100k以内
                </span>
                <input type="text"  name="fields[list_title3]" value="{{@$field['list_title3']}}" class="form-control row_top_10 info_phone" placeholder="输入 标题">
                <textarea  class="form-control row_top_10" rows="5" placeholder="输入 引言" name="fields[list_intro3]">{{@$field['list_intro3']}}</textarea>
                <input type="text" id="list_link3" name="fields[list_link3]" value="{{@$field['list_title3']}}" class="form-control row_top_10 info_phone" placeholder="输入 链接">
            </div>
        </div>

    </div>

</div>
@endsection