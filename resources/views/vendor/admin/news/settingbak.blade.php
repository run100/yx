<section class="content">


    <div class="row"><div class="col-md-12"><div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">新增板块</h3>
                    <div class="box-tools pull-right">
                    </div><!-- /.box-tools -->
                </div><!-- /.box-header -->
                <div class="box-body" style="display: block;">
                    <form method="POST" action="/admin/yxfunction" class="form-horizontal" accept-charset="UTF-8" pjax-container="1" enctype="multipart/form-data">
                        <div class="box-body fields-group">

                            <div class="form-group  ">

                                <label for="block_id" class="col-sm-2 control-label">板块名称</label>

                                <div class="col-sm-8">


                                    <input type="hidden" name="block_id">

                                    <select class="form-control block_id select2-hidden-accessible" style="width: 100%;" name="block_id" tabindex="-1" aria-hidden="true">
                                        <option value=""></option>
                                        <option value="1">板块一</option>
                                        <option value="2">板块二</option>
                                        <option value="3">板块三</option>
                                        <option value="4">板块四</option>
                                        <option value="5">板块五</option>
                                        <option value="6">板块六</option>
                                        <option value="7">板块七</option>
                                        <option value="8">板块八</option>
                                    </select><span class="select2 select2-container select2-container--default" dir="ltr" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-block_id-ya-container"><span class="select2-selection__rendered" id="select2-block_id-ya-container"><span class="select2-selection__placeholder">板块名称</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>


                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="channel_id" class="col-sm-2 control-label">频道名称</label>

                                <div class="col-sm-8">


                                    <input type="hidden" name="channel_id">

                                    <select class="form-control channel_id select2-hidden-accessible" style="width: 100%;" name="channel_id" tabindex="-1" aria-hidden="true">
                                        <option value=""></option>
                                        <option value="101">频道1</option>
                                        <option value="102">频道2</option>
                                    </select><span class="select2 select2-container select2-container--default" dir="ltr" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-channel_id-la-container"><span class="select2-selection__rendered" id="select2-channel_id-la-container"><span class="select2-selection__placeholder">频道名称</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>


                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block1_banner1" class="col-sm-2 control-label">板块一[导航图标]</label>

                                <div class="col-sm-8">


                                    <div class="file-input file-input-new"><div class="file-preview ">
                                            <div class="close fileinput-remove">×</div>
                                            <div class="file-drop-disabled">
                                                <div class="file-preview-thumbnails">
                                                </div>
                                                <div class="clearfix"></div>    <div class="file-preview-status text-center text-success"></div>
                                                <div class="kv-fileinput-error file-error-message" style="display: none;"></div>
                                            </div>
                                        </div>
                                        <div class="kv-upload-progress hide"><div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                    0%
                                                </div>
                                            </div></div>
                                        <div class="input-group file-caption-main">
                                            <div tabindex="500" class="form-control file-caption  kv-fileinput-caption">
                                                <div class="file-caption-name"></div>
                                            </div>

                                            <div class="input-group-btn">

                                                <button type="button" tabindex="500" title="Abort ongoing upload" class="btn btn-default hide fileinput-cancel fileinput-cancel-button"><i class="glyphicon glyphicon-ban-circle"></i>  <span class="hidden-xs">Cancel</span></button>

                                                <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">浏览</span><input type="file" class="block1_banner1" name="block1_banner1" id="1537411953638"></div>
                                            </div>
                                        </div></div><div id="kvFileinputModal" class="file-zoom-dialog modal fade" tabindex="-1" aria-labelledby="kvFileinputModalLabel"><div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <div class="kv-zoom-actions pull-right"><button type="button" class="btn btn-default btn-header-toggle btn-toggleheader" title="Toggle header" data-toggle="button" aria-pressed="false" autocomplete="off"><i class="glyphicon glyphicon-resize-vertical"></i></button><button type="button" class="btn btn-default btn-fullscreen" title="Toggle full screen" data-toggle="button" aria-pressed="false" autocomplete="off"><i class="glyphicon glyphicon-fullscreen"></i></button><button type="button" class="btn btn-default btn-borderless" title="Toggle borderless mode" data-toggle="button" aria-pressed="false" autocomplete="off"><i class="glyphicon glyphicon-resize-full"></i></button><button type="button" class="btn btn-default btn-close" title="Close detailed preview" data-dismiss="modal" aria-hidden="true"><i class="glyphicon glyphicon-remove"></i></button></div>
                                                    <h3 class="modal-title">Detailed Preview <small><span class="kv-zoom-title"></span></small></h3>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="floating-buttons"></div>
                                                    <div class="kv-zoom-body file-zoom-content"></div>
                                                    <button type="button" class="btn btn-navigate btn-prev" title="View previous file"><i class="glyphicon glyphicon-triangle-left"></i></button> <button type="button" class="btn btn-navigate btn-next" title="View next file"><i class="glyphicon glyphicon-triangle-right"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;图片尺寸：1200*100
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block1_left_pic" class="col-sm-2 control-label">板块一[左图]</label>

                                <div class="col-sm-8">


                                    <div class="file-input file-input-new"><div class="file-preview ">
                                            <div class="close fileinput-remove">×</div>
                                            <div class="file-drop-disabled">
                                                <div class="file-preview-thumbnails">
                                                </div>
                                                <div class="clearfix"></div>    <div class="file-preview-status text-center text-success"></div>
                                                <div class="kv-fileinput-error file-error-message" style="display: none;"></div>
                                            </div>
                                        </div>
                                        <div class="kv-upload-progress hide"><div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                    0%
                                                </div>
                                            </div></div>
                                        <div class="input-group file-caption-main">
                                            <div tabindex="500" class="form-control file-caption  kv-fileinput-caption">
                                                <div class="file-caption-name"></div>
                                            </div>

                                            <div class="input-group-btn">

                                                <button type="button" tabindex="500" title="Abort ongoing upload" class="btn btn-default hide fileinput-cancel fileinput-cancel-button"><i class="glyphicon glyphicon-ban-circle"></i>  <span class="hidden-xs">Cancel</span></button>

                                                <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">浏览</span><input type="file" class="block1_left_pic" name="block1_left_pic" id="1537411953588"></div>
                                            </div>
                                        </div></div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;图片尺寸：580*350
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block1_right_top_title" class="col-sm-2 control-label">板块一[右上-标题]</label>

                                <div class="col-sm-8">


                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>

                                        <input type="text" id="block1_right_top_title" name="block1_right_top_title" value="" class="form-control block1_right_top_title" placeholder="输入 板块一[右上-标题]">


                                    </div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;标题+链接,标题和链接用#连接, 例如：习近平这些贺信，与未来紧密关联#http://365jia.cn
</span>

                                </div>
                            </div>
                            <div class="form-group  ">

                                <label for="block1_right_top_intro" class="col-sm-2 control-label">板块一[右上-引言]</label>

                                <div class="col-sm-8">


                                    <textarea name="block1_right_top_intro" class="form-control" rows="5" placeholder="输入 板块一[右上-引言]"></textarea>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;引言+链接,引言和链接用#连接, 例如：世界公众科学素质促进大会在北京召开#http://365jia.cn
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block1_right_down_list1" class="col-sm-2 control-label">板块一[右下-列表1]</label>

                                <div class="col-sm-8">


                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>

                                        <input type="text" id="block1_right_down_list1" name="block1_right_down_list1" value="" class="form-control block1_right_down_list1" placeholder="输入 板块一[右下-列表1]">


                                    </div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;标题+链接,标题和链接用#连接, 例如：习近平这些贺信，与未来紧密关联#http://365jia.cn
</span>

                                </div>
                            </div>
                            <div class="form-group  ">

                                <label for="block1_right_down_list2" class="col-sm-2 control-label">板块一[右下-列表2]</label>

                                <div class="col-sm-8">


                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>

                                        <input type="text" id="block1_right_down_list2" name="block1_right_down_list2" value="" class="form-control block1_right_down_list2" placeholder="输入 板块一[右下-列表2]">


                                    </div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;标题+链接,填写格式同上
</span>

                                </div>
                            </div>
                            <div class="form-group  ">

                                <label for="block1_right_down_list3" class="col-sm-2 control-label">板块一[右下-列表3]</label>

                                <div class="col-sm-8">


                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>

                                        <input type="text" id="block1_right_down_list3" name="block1_right_down_list3" value="" class="form-control block1_right_down_list3" placeholder="输入 板块一[右下-列表3]">


                                    </div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;标题+链接,填写格式同上
</span>

                                </div>
                            </div>
                            <div class="form-group  ">

                                <label for="block1_right_down_list4" class="col-sm-2 control-label">板块一[右下-列表4]</label>

                                <div class="col-sm-8">


                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>

                                        <input type="text" id="block1_right_down_list4" name="block1_right_down_list4" value="" class="form-control block1_right_down_list4" placeholder="输入 板块一[右下-列表4]">


                                    </div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;标题+链接,填写格式同上
</span>

                                </div>
                            </div>
                            <div class="form-group  ">

                                <label for="block2_banner1" class="col-sm-2 control-label">板块二[导航图标]</label>

                                <div class="col-sm-8">


                                    <div class="file-input file-input-new"><div class="file-preview ">
                                            <div class="close fileinput-remove">×</div>
                                            <div class="file-drop-disabled">
                                                <div class="file-preview-thumbnails">
                                                </div>
                                                <div class="clearfix"></div>    <div class="file-preview-status text-center text-success"></div>
                                                <div class="kv-fileinput-error file-error-message" style="display: none;"></div>
                                            </div>
                                        </div>
                                        <div class="kv-upload-progress hide"><div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                    0%
                                                </div>
                                            </div></div>
                                        <div class="input-group file-caption-main">
                                            <div tabindex="500" class="form-control file-caption  kv-fileinput-caption">
                                                <div class="file-caption-name"></div>
                                            </div>

                                            <div class="input-group-btn">

                                                <button type="button" tabindex="500" title="Abort ongoing upload" class="btn btn-default hide fileinput-cancel fileinput-cancel-button"><i class="glyphicon glyphicon-ban-circle"></i>  <span class="hidden-xs">Cancel</span></button>

                                                <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">浏览</span><input type="file" class="block2_banner1" name="block2_banner1" id="1537411953647"></div>
                                            </div>
                                        </div></div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;图片尺寸：1200*100
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block2_tag" class="col-sm-2 control-label">板块二[标签]</label>

                                <div class="col-sm-8">


                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>

                                        <input type="text" id="block2_tag" name="block2_tag" value="" class="form-control block2_tag" placeholder="输入 板块二[标签]">


                                    </div>


                                </div>
                            </div>
                            <div class="form-group  ">

                                <label for="block2_left_pic" class="col-sm-2 control-label">板块二[左图]</label>

                                <div class="col-sm-8">


                                    <div class="file-input file-input-new"><div class="file-preview ">
                                            <div class="close fileinput-remove">×</div>
                                            <div class="file-drop-disabled">
                                                <div class="file-preview-thumbnails">
                                                </div>
                                                <div class="clearfix"></div>    <div class="file-preview-status text-center text-success"></div>
                                                <div class="kv-fileinput-error file-error-message" style="display: none;"></div>
                                            </div>
                                        </div>
                                        <div class="kv-upload-progress hide"><div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                    0%
                                                </div>
                                            </div></div>
                                        <div class="input-group file-caption-main">
                                            <div tabindex="500" class="form-control file-caption  kv-fileinput-caption">
                                                <div class="file-caption-name"></div>
                                            </div>

                                            <div class="input-group-btn">

                                                <button type="button" tabindex="500" title="Abort ongoing upload" class="btn btn-default hide fileinput-cancel fileinput-cancel-button"><i class="glyphicon glyphicon-ban-circle"></i>  <span class="hidden-xs">Cancel</span></button>

                                                <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">浏览</span><input type="file" class="block2_left_pic" name="block2_left_pic" id="1537411953606"></div>
                                            </div>
                                        </div></div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;图片尺寸：630*380
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block2_left_pic_info" class="col-sm-2 control-label">板块二[左图-标题+链接]</label>

                                <div class="col-sm-8">


                                    <textarea name="block2_left_pic_info" class="form-control" rows="3" placeholder="输入 板块二[左图-标题+链接]"></textarea>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;标题+链接,标题和链接用#连接, 例如：习近平这些贺信，与未来紧密关联#http://365jia.cn
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block2_right_pic1" class="col-sm-2 control-label">板块二[右-列表1-图片]</label>

                                <div class="col-sm-8">


                                    <div class="file-input file-input-new"><div class="file-preview ">
                                            <div class="close fileinput-remove">×</div>
                                            <div class="file-drop-disabled">
                                                <div class="file-preview-thumbnails">
                                                </div>
                                                <div class="clearfix"></div>    <div class="file-preview-status text-center text-success"></div>
                                                <div class="kv-fileinput-error file-error-message" style="display: none;"></div>
                                            </div>
                                        </div>
                                        <div class="kv-upload-progress hide"><div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                    0%
                                                </div>
                                            </div></div>
                                        <div class="input-group file-caption-main">
                                            <div tabindex="500" class="form-control file-caption  kv-fileinput-caption">
                                                <div class="file-caption-name"></div>
                                            </div>

                                            <div class="input-group-btn">

                                                <button type="button" tabindex="500" title="Abort ongoing upload" class="btn btn-default hide fileinput-cancel fileinput-cancel-button"><i class="glyphicon glyphicon-ban-circle"></i>  <span class="hidden-xs">Cancel</span></button>

                                                <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">浏览</span><input type="file" class="block2_right_pic1" name="block2_right_pic1" id="1537411953645"></div>
                                            </div>
                                        </div></div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;图片尺寸：165*99
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block2_right_pic1_info1" class="col-sm-2 control-label">板块二[右-列表1-标题+引言+链接]</label>

                                <div class="col-sm-8">


                                    <textarea name="block2_right_pic1_info1" class="form-control" rows="6" placeholder="输入 板块二[右-列表1-标题+引言+链接]"></textarea>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;填写格式：标题#引言#链接
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block2_right_pic2" class="col-sm-2 control-label">板块二[右-列表2-图片]</label>

                                <div class="col-sm-8">


                                    <div class="file-input file-input-new"><div class="file-preview ">
                                            <div class="close fileinput-remove">×</div>
                                            <div class="file-drop-disabled">
                                                <div class="file-preview-thumbnails">
                                                </div>
                                                <div class="clearfix"></div>    <div class="file-preview-status text-center text-success"></div>
                                                <div class="kv-fileinput-error file-error-message" style="display: none;"></div>
                                            </div>
                                        </div>
                                        <div class="kv-upload-progress hide"><div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                    0%
                                                </div>
                                            </div></div>
                                        <div class="input-group file-caption-main">
                                            <div tabindex="500" class="form-control file-caption  kv-fileinput-caption">
                                                <div class="file-caption-name"></div>
                                            </div>

                                            <div class="input-group-btn">

                                                <button type="button" tabindex="500" title="Abort ongoing upload" class="btn btn-default hide fileinput-cancel fileinput-cancel-button"><i class="glyphicon glyphicon-ban-circle"></i>  <span class="hidden-xs">Cancel</span></button>

                                                <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">浏览</span><input type="file" class="block2_right_pic2" name="block2_right_pic2" id="1537411953703"></div>
                                            </div>
                                        </div></div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;图片尺寸：165*99
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block2_right_pic2_info2" class="col-sm-2 control-label">板块二[右-列表2-标题+引言+链接]</label>

                                <div class="col-sm-8">


                                    <textarea name="block2_right_pic2_info2" class="form-control" rows="6" placeholder="输入 板块二[右-列表2-标题+引言+链接]"></textarea>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;填写格式：标题#引言#链接
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block2_right_pic3" class="col-sm-2 control-label">板块二[右-列表3-图片]</label>

                                <div class="col-sm-8">


                                    <div class="file-input file-input-new"><div class="file-preview ">
                                            <div class="close fileinput-remove">×</div>
                                            <div class="file-drop-disabled">
                                                <div class="file-preview-thumbnails">
                                                </div>
                                                <div class="clearfix"></div>    <div class="file-preview-status text-center text-success"></div>
                                                <div class="kv-fileinput-error file-error-message" style="display: none;"></div>
                                            </div>
                                        </div>
                                        <div class="kv-upload-progress hide"><div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                    0%
                                                </div>
                                            </div></div>
                                        <div class="input-group file-caption-main">
                                            <div tabindex="500" class="form-control file-caption  kv-fileinput-caption">
                                                <div class="file-caption-name"></div>
                                            </div>

                                            <div class="input-group-btn">

                                                <button type="button" tabindex="500" title="Abort ongoing upload" class="btn btn-default hide fileinput-cancel fileinput-cancel-button"><i class="glyphicon glyphicon-ban-circle"></i>  <span class="hidden-xs">Cancel</span></button>

                                                <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">浏览</span><input type="file" class="block2_right_pic3" name="block2_right_pic3" id="1537411953663"></div>
                                            </div>
                                        </div></div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;图片尺寸：165*99
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block2_right_pic3_info3" class="col-sm-2 control-label">板块二[右-列表3-标题+引言+链接]</label>

                                <div class="col-sm-8">


                                    <textarea name="block2_right_pic3_info3" class="form-control" rows="6" placeholder="输入 板块二[右-列表3-标题+引言+链接]"></textarea>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;填写格式：标题#引言#链接
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block4_banner1" class="col-sm-2 control-label">板块四[导航图标]</label>

                                <div class="col-sm-8">


                                    <div class="file-input file-input-new"><div class="file-preview ">
                                            <div class="close fileinput-remove">×</div>
                                            <div class="file-drop-disabled">
                                                <div class="file-preview-thumbnails">
                                                </div>
                                                <div class="clearfix"></div>    <div class="file-preview-status text-center text-success"></div>
                                                <div class="kv-fileinput-error file-error-message" style="display: none;"></div>
                                            </div>
                                        </div>
                                        <div class="kv-upload-progress hide"><div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                    0%
                                                </div>
                                            </div></div>
                                        <div class="input-group file-caption-main">
                                            <div tabindex="500" class="form-control file-caption  kv-fileinput-caption">
                                                <div class="file-caption-name"></div>
                                            </div>

                                            <div class="input-group-btn">

                                                <button type="button" tabindex="500" title="Abort ongoing upload" class="btn btn-default hide fileinput-cancel fileinput-cancel-button"><i class="glyphicon glyphicon-ban-circle"></i>  <span class="hidden-xs">Cancel</span></button>

                                                <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">浏览</span><input type="file" class="block4_banner1" name="block4_banner1" id="1537411953681"></div>
                                            </div>
                                        </div></div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;图片尺寸：1200*100
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block4_down_area1" class="col-sm-2 control-label">板块四[图片1]</label>

                                <div class="col-sm-8">


                                    <div class="file-input file-input-new"><div class="file-preview ">
                                            <div class="close fileinput-remove">×</div>
                                            <div class="file-drop-disabled">
                                                <div class="file-preview-thumbnails">
                                                </div>
                                                <div class="clearfix"></div>    <div class="file-preview-status text-center text-success"></div>
                                                <div class="kv-fileinput-error file-error-message" style="display: none;"></div>
                                            </div>
                                        </div>
                                        <div class="kv-upload-progress hide"><div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                    0%
                                                </div>
                                            </div></div>
                                        <div class="input-group file-caption-main">
                                            <div tabindex="500" class="form-control file-caption  kv-fileinput-caption">
                                                <div class="file-caption-name"></div>
                                            </div>

                                            <div class="input-group-btn">

                                                <button type="button" tabindex="500" title="Abort ongoing upload" class="btn btn-default hide fileinput-cancel fileinput-cancel-button"><i class="glyphicon glyphicon-ban-circle"></i>  <span class="hidden-xs">Cancel</span></button>

                                                <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">浏览</span><input type="file" class="block4_down_area1" name="block4_down_area1" id="1537411953640"></div>
                                            </div>
                                        </div></div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;图片尺寸：380*230
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block4_down_area1_info" class="col-sm-2 control-label">板块四[图片1-标题+链接]</label>

                                <div class="col-sm-8">


                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>

                                        <input type="text" id="block4_down_area1_info" name="block4_down_area1_info" value="" class="form-control block4_down_area1_info" placeholder="输入 板块四[图片1-标题+链接]">


                                    </div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;填写格式：标题#链接
</span>

                                </div>
                            </div>
                            <div class="form-group  ">

                                <label for="block4_down_area1_list_info" class="col-sm-2 control-label">板块四[图片1-下方列表]</label>

                                <div class="col-sm-8">


                                    <textarea name="block4_down_area1_list_info" class="form-control" rows="12" placeholder="输入 板块四[图片1-下方列表]"></textarea>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;填写格式：<br>标题#链接<br>标题#链接<br>标题#链接
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block4_down_area2" class="col-sm-2 control-label">板块四[图片2]</label>

                                <div class="col-sm-8">


                                    <div class="file-input file-input-new"><div class="file-preview ">
                                            <div class="close fileinput-remove">×</div>
                                            <div class="file-drop-disabled">
                                                <div class="file-preview-thumbnails">
                                                </div>
                                                <div class="clearfix"></div>    <div class="file-preview-status text-center text-success"></div>
                                                <div class="kv-fileinput-error file-error-message" style="display: none;"></div>
                                            </div>
                                        </div>
                                        <div class="kv-upload-progress hide"><div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                    0%
                                                </div>
                                            </div></div>
                                        <div class="input-group file-caption-main">
                                            <div tabindex="500" class="form-control file-caption  kv-fileinput-caption">
                                                <div class="file-caption-name"></div>
                                            </div>

                                            <div class="input-group-btn">

                                                <button type="button" tabindex="500" title="Abort ongoing upload" class="btn btn-default hide fileinput-cancel fileinput-cancel-button"><i class="glyphicon glyphicon-ban-circle"></i>  <span class="hidden-xs">Cancel</span></button>

                                                <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">浏览</span><input type="file" class="block4_down_area2" name="block4_down_area2" id="1537411953697"></div>
                                            </div>
                                        </div></div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;图片尺寸：380*230
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block4_down_area2_info" class="col-sm-2 control-label">板块四[图片2-标题+链接]</label>

                                <div class="col-sm-8">


                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>

                                        <input type="text" id="block4_down_area2_info" name="block4_down_area2_info" value="" class="form-control block4_down_area2_info" placeholder="输入 板块四[图片2-标题+链接]">


                                    </div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;填写格式：标题#链接
</span>

                                </div>
                            </div>
                            <div class="form-group  ">

                                <label for="block4_down_area2_list_info" class="col-sm-2 control-label">板块四[图片2-下方列表]</label>

                                <div class="col-sm-8">


                                    <textarea name="block4_down_area2_list_info" class="form-control" rows="12" placeholder="输入 板块四[图片2-下方列表]"></textarea>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;填写格式：<br>标题#链接<br>标题#链接<br>标题#链接
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block4_down_area3" class="col-sm-2 control-label">板块四[图片3]</label>

                                <div class="col-sm-8">


                                    <div class="file-input file-input-new"><div class="file-preview ">
                                            <div class="close fileinput-remove">×</div>
                                            <div class="file-drop-disabled">
                                                <div class="file-preview-thumbnails">
                                                </div>
                                                <div class="clearfix"></div>    <div class="file-preview-status text-center text-success"></div>
                                                <div class="kv-fileinput-error file-error-message" style="display: none;"></div>
                                            </div>
                                        </div>
                                        <div class="kv-upload-progress hide"><div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                    0%
                                                </div>
                                            </div></div>
                                        <div class="input-group file-caption-main">
                                            <div tabindex="500" class="form-control file-caption  kv-fileinput-caption">
                                                <div class="file-caption-name"></div>
                                            </div>

                                            <div class="input-group-btn">

                                                <button type="button" tabindex="500" title="Abort ongoing upload" class="btn btn-default hide fileinput-cancel fileinput-cancel-button"><i class="glyphicon glyphicon-ban-circle"></i>  <span class="hidden-xs">Cancel</span></button>

                                                <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">浏览</span><input type="file" class="block4_down_area3" name="block4_down_area3" id="1537411953709"></div>
                                            </div>
                                        </div></div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;图片尺寸：380*230
</span>

                                </div>
                            </div>

                            <div class="form-group  ">

                                <label for="block4_down_area3_info" class="col-sm-2 control-label">板块四[图片3-标题+链接]</label>

                                <div class="col-sm-8">


                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>

                                        <input type="text" id="block4_down_area3_info" name="block4_down_area3_info" value="" class="form-control block4_down_area3_info" placeholder="输入 板块四[图片3-标题+链接]">


                                    </div>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;填写格式：标题#链接
</span>

                                </div>
                            </div>
                            <div class="form-group  ">

                                <label for="block4_down_area3_list_info" class="col-sm-2 control-label">板块四[图片3-下方列表]</label>

                                <div class="col-sm-8">


                                    <textarea name="block4_down_area3_list_info" class="form-control" rows="12" placeholder="输入 板块四[图片3-下方列表]"></textarea>

                                    <span class="help-block">
    <i class="fa fa-info-circle"></i>&nbsp;填写格式：<br>标题#链接<br>标题#链接<br>标题#链接
</span>

                                </div>
                            </div>


                        </div>

                        <!-- /.box-body -->
                        <div class="box-footer">
                            <input type="hidden" name="_token" value="Gy203uwuVENFF0lJRcEHCJTR0xj1pSHliN70Jcbk">
                            <div class="col-md-2"></div>

                            <div class="col-md-8">
                                <div class="btn-group pull-left">
                                    <button type="reset" class="btn btn-warning pull-right">撤销</button>
                                </div>
                                <div class="btn-group pull-right">
                                    <button type="submit" class="btn btn-info pull-right">提交</button>
                                </div>

                            </div>

                        </div>
                    </form>
                </div><!-- /.box-body -->
            </div></div></div>

</section>