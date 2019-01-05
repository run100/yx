<style>
    .row_top_10 {
        margin-top: 10px;
        margin-bottom: 10px;
    }
</style>
<section class="content"><div class="row"><div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">创建</h3>

                    <div class="box-tools">
                        <div class="btn-group pull-right" style="margin-right: 10px">
                            <a href="http://zhuanti.wang.365jia.lab/admin/projects/202/players" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;列表</a>
                        </div> <div class="btn-group pull-right" style="margin-right: 10px">
                            <a class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;返回</a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form action="http://zhuanti.wang.365jia.lab/admin/projects/202/players" method="post" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data" pjax-container="">

                    <div class="box-body">

                        <div class="fields-group">

                            <input type="hidden" name="project_id" value="202" class="project_id">

                            <div class="form-group">

                                <label for="merchant_id" class="col-sm-2 control-label">频道</label>

                                <div class="col-sm-8">


                                    <input type="hidden" name="merchant_id">
                                    <select class="form-control merchant_id" style="width: 100%;" name="merchant_id">
                                        <option value=""></option>
                                        @foreach($news_channles_arr as $k => $v)
                                            <option value="{{ $k }}" {{@$field['channel_id'] == $k ? 'selected' : '' }}>{{$v}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">

                                <label for="merchant_id" class="col-sm-2 control-label">板块</label>

                                <div class="col-sm-8">


                                    <input type="hidden" name="merchant_id">
                                    <select class="form-control merchant_id" style="width: 100%;" name="merchant_id">
                                        <option value=""></option>
                                        @foreach($blockTypes as $k => $v)
                                            <option value="{{ $k }}" {{@$field['block_id'] == $k ? 'selected' : '' }}>{{$v}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>





                            <div class="form-group  ">

                                <label for="info_nickname" class="col-sm-2 control-label"> 板块名称</label>

                                <div class="col-sm-8">
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>

                                        <input type="text" id="info_nickname" name="info_nickname" value="" class="form-control info_nickname" placeholder="输入  板块名称">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group  ">

                                <label for="info_img" class="col-sm-2 control-label">板块一[导航图标]</label>

                                <div class="col-sm-8">
                                    <div style="height: 100px;width: 100px;">
                                        <img style="width: 100%;height: 100%" src="{{uploads_url(@$field['img_name'])}}">
                                    </div>
                                    <input type="hidden" name ="img_name[]" value="{{@$field['img_name']}}">
                                    <input type="file" class="info_img" name="info_img">
                                </div>
                            </div>



                            <div class="form-group  ">

                                <label for="info_img" class="col-sm-2 control-label">板块一[左图]</label>

                                <div class="col-sm-8">
                                    <div style="height: 100px;width: 100px;">
                                        <img style="width: 100%;height: 100%" src="{{uploads_url(@$field['img_name'])}}">
                                    </div>
                                    <input type="hidden" name ="img_name[]" value="{{@$field['img_name']}}">
                                    <input type="file" class="info_img" name="info_img">
                                    <div class="form-inline row_top_10"><span>图片标题：</span><input type="text" style="width:550px;" id="info_phone" name="info_phone" value="" class="form-control info_phone" placeholder="输入 图片标题"></div>
                                    <div class="form-inline row_top_10"><span>图片链接：</span><input type="text" style="width:550px;" id="info_phone" name="info_phone" value="" class="form-control info_phone" placeholder="输入 图片链接"></div>
                                </div>
                            </div>



                            <div class="form-group  ">

                                <label for="info_phone" class="col-sm-2 control-label">板块一[右上-标题]</label>

                                <div class="col-sm-8">


                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>


                                        <textarea name="info_xuanyan" class="form-control" rows="5" placeholder="输入 标题#引言#链接"></textarea>


                                    </div>


                                </div>
                            </div>





                            <div class="form-group  ">

                                <label for="info_phone" class="col-sm-2 control-label">板块一[右下-列表1]</label>

                                <div class="col-sm-8">


                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>

                                        <input type="text" id="info_phone" name="info_phone" value="" class="form-control info_phone" placeholder="输入 标题#链接">
                                    </div>


                                </div>
                            </div>









                            <div class="form-group  ">

                                <label for="info_phone" class="col-sm-2 control-label">板块一[右下-列表2]</label>

                                <div class="col-sm-8">


                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>

                                        <input  type="text" id="info_phone" name="info_phone" value="" class="form-control info_phone" placeholder="输入 标题#链接">
                                    </div>


                                </div>
                            </div>


                            <div class="form-group  ">

                                <label for="info_phone" class="col-sm-2 control-label">板块一[右下-列表3]</label>

                                <div class="col-sm-8">


                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>

                                        <input type="text" id="info_phone" name="info_phone" value="" class="form-control info_phone" placeholder="输入 标题#链接">
                                    </div>


                                </div>
                            </div>


                            <div class="form-group">

                                <label for="info_phone" class="col-sm-2 control-label">板块一[右下-列表4]</label>

                                <div class="col-sm-8">


                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>

                                        <input type="text" id="info_phone" name="info_phone" value="" class="form-control info_phone" placeholder="输入 标题#链接">
                                    </div>
                                </div>
                            </div>







                            <div class="form-group  ">
                                <label for="info_img" class="col-sm-2 control-label">板块二[导航图标]</label>

                                <div class="col-sm-8">
                                    <div style="height: 100px;width: 100px;">
                                        <img style="width: 100%;height: 100%" src="{{uploads_url(@$field['img_name'])}}">
                                    </div>
                                    <input type="hidden" name ="img_name[]" value="{{@$field['img_name']}}">
                                    <input type="file" class="info_img" name="info_img">
                                </div>
                            </div>





                            <div class="form-group">
                                <label for="info_phone" class="col-sm-2 control-label">板块二[标签]</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                        <input type="text" id="info_phone" name="info_phone" value="" class="form-control info_phone" placeholder="输入 标签">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group  ">
                                <label for="info_img" class="col-sm-2 control-label">板块二[左图]</label>
                                <div class="col-sm-8">
                                    <div style="height: 100px;width: 100px;">
                                        <img style="width: 100%;height: 100%" src="{{uploads_url(@$field['img_name'])}}">
                                    </div>
                                    <input type="hidden" name ="img_name[]" value="{{@$field['img_name']}}">

                                    <input type="file" class="info_img" name="info_img">
                                    <div class="form-inline row_top_10"><span>图片标题：</span><input type="text" style="width:550px;" id="info_phone" name="info_phone" value="" class="form-control info_phone" placeholder="输入 图片标题"></div>
                                    <div class="form-inline row_top_10"><span>图片链接：</span><input type="text" style="width:550px;" id="info_phone" name="info_phone" value="" class="form-control info_phone" placeholder="输入 图片链接"></div>
                                </div>
                            </div>


                            <div class="form-group ">
                                <label for="info_img" class="col-sm-2 control-label">板块二[右-列表1]</label>
                                <div class="col-sm-8">
                                    <div style="height: 100px;width: 100px;">
                                        <img style="width: 100%;height: 100%" src="{{uploads_url(@$field['img_name'])}}">
                                    </div>
                                    <input type="hidden" name ="img_name[]" value="{{@$field['img_name']}}">
                                    <input type="file" class="info_img " name="info_img">
                                    <input type="text" id="info_phone" name="info_phone" value="" class="form-control row_top_10 info_phone" placeholder="输入 标题">
                                    <textarea name="info_xuanyan" class="form-control row_top_10" rows="5" placeholder="输入 引言"></textarea>
                                    <input type="text" id="info_phone" name="info_phone" value="" class="form-control row_top_10 info_phone" placeholder="输入 链接">
                                </div>
                            </div>

                            <div class="form-group ">
                                <label for="info_img" class="col-sm-2 control-label">板块二[右-列表2]</label>
                                <div class="col-sm-8">
                                    <div style="height: 100px;width: 100px;">
                                        <img style="width: 100%;height: 100%" src="{{uploads_url(@$field['img_name'])}}">
                                    </div>
                                    <input type="hidden" name ="img_name[]" value="{{@$field['img_name']}}">
                                    <input type="file" class="info_img " name="info_img">
                                    <input type="text" id="info_phone" name="info_phone" value="" class="form-control row_top_10 info_phone" placeholder="输入 标题">
                                    <textarea name="info_xuanyan" class="form-control row_top_10" rows="5" placeholder="输入 引言"></textarea>
                                    <input type="text" id="info_phone" name="info_phone" value="" class="form-control row_top_10 info_phone" placeholder="输入 链接">
                                </div>
                            </div>


                            <div class="form-group ">
                                <label for="info_img" class="col-sm-2 control-label">板块二[右-列表3]</label>
                                <div class="col-sm-8">
                                    <div style="height: 100px;width: 100px;">
                                        <img style="width: 100%;height: 100%" src="{{uploads_url(@$field['img_name'])}}">
                                    </div>
                                    <input type="hidden" name ="img_name[]" value="{{@$field['img_name']}}">
                                    <input type="file" class="info_img " name="info_img">
                                    <input type="text" id="info_phone" name="info_phone" value="" class="form-control row_top_10 info_phone" placeholder="输入 标题">
                                    <textarea name="info_xuanyan" class="form-control row_top_10" rows="5" placeholder="输入 引言"></textarea>
                                    <input type="text" id="info_phone" name="info_phone" value="" class="form-control row_top_10 info_phone" placeholder="输入 链接">
                                </div>
                            </div>







                           {{--<div class="form-group  ">--}}

                                {{--<label for="checked" class="col-sm-2 control-label">审核状态</label>--}}

                                {{--<div class="col-sm-8">--}}


                                    {{--<label class="radio-inline">--}}
                                        {{--<input type="radio" name="checked" value="0" class="minimal checked" checked="">&nbsp;待审核&nbsp;&nbsp;--}}
                                    {{--</label>--}}
                                    {{--<label class="radio-inline">--}}
                                        {{--<input type="radio" name="checked" value="1" class="minimal checked">&nbsp;审核通过&nbsp;&nbsp;--}}
                                    {{--</label>--}}
                                    {{--<label class="radio-inline">--}}
                                        {{--<input type="radio" name="checked" value="2" class="minimal checked">&nbsp;审核未通过&nbsp;&nbsp;--}}
                                    {{--</label>--}}


                                {{--</div>--}}
                            {{--</div>--}}










                            {{--<div class="form-group  ">--}}

                                {{--<label for="info_xuanyan" class="col-sm-2 control-label"> 宣言</label>--}}

                                {{--<div class="col-sm-8">--}}


                                    {{--<textarea name="info_xuanyan" class="form-control" rows="5" placeholder="输入  宣言"></textarea>--}}
                                {{--</div>--}}
                            {{--</div>--}}


                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">

                        <input type="hidden" name="_token" value="vS6g2idrbb4jPxGhs4DZJOzLkd2mKiyWyccs9CgN"><div class="col-md-2">

                        </div>
                        <div class="col-md-8">

                            <div class="btn-group pull-right">
                                <button type="submit" class="btn btn-info pull-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> 提交">提交</button>
                            </div>

                            <div class="btn-group pull-left">
                                <button type="reset" class="btn btn-warning">撤销</button>
                            </div>

                        </div>

                    </div>

                    <input type="hidden" name="_previous_" value="http://zhuanti.wang.365jia.lab/admin/projects/202/players" class="_previous_"><!-- /.box-footer -->
                </form>
            </div>

        </div></div>

</section>