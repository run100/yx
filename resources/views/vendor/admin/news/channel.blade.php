<div class="form-group">


    <label for="merchant_id"  style="float:left;" class="col-sm-2 control-label">频道</label>
    <div class="col-sm-8">

        <select class="form-control merchant_id" style="width: 100%;" id="fields_channel_id" name="fields[channel_id]">
            <option value=""></option>
            @foreach($news_channles_arr as $k => $v)
                <option value="{{ $v }}" {{$v == @$field['channel_id'] ? 'selected' : '' }}>{{$v}}</option>
            @endforeach
        </select>
    </div>

</div>

@if($block_id >= \App\Models\News\Blocks::TYPE_BLOCK_3)
<div class="form-group">

    <label for="merchant_id" class="col-sm-2 control-label">导航图标</label>

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
@endif