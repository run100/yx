<?php

use Illuminate\Routing\Router;

$attributes = [
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => 'Encore\Admin\Controllers',
    'middleware'    => config('admin.route.middleware'),
];

Route::group($attributes, function ($router) {

    /* @var \Illuminate\Routing\Router $router */
    $router->group([], function ($router) {

        /* @var \Illuminate\Routing\Router $router */
        $router->resource('auth/roles', 'RoleController');
        $router->resource('auth/permissions', 'PermissionController');
        $router->resource('auth/menu', 'MenuController', ['except' => ['create']]);
        $router->resource('auth/logs', 'LogController', ['only' => ['index', 'destroy']]);
    });

    $router->get('auth/login', 'AuthController@getLogin');
    $router->post('auth/login', 'AuthController@postLogin');
    $router->get('auth/logout', 'AuthController@getLogout');
    $router->get('auth/setting', 'AuthController@getSetting');
    $router->put('auth/setting', 'AuthController@putSetting');
});

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->resource('auth/users', 'UserController');
    //vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
    // 管理首页
    $router->get('/', 'HomeController@weclome');
    // End:管理首页
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^


    //vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
    // 客户管理
    $router->resource('merchants', 'MerchantController');
    $router->post('merchants/{merchant}/refresh_auth', ['uses' => 'MerchantController@refreshAuth', 'as' => 'merchants.refresh_auth']);
    $router->get('merchants/{merchant}/manage', ['uses' => 'MerchantController@manage', 'as' => 'merchants.manage']);
    $router->get('merchants/{merchant}/refresh_info', ['uses' => 'MerchantController@refreshInfo', 'as' => 'merchants.refresh_info']);
    $router->post('merchants/{merchant}/update_subscribe_reply', ['uses' => 'MerchantController@updateSubscribeReply', 'as' => 'merchants.update_subscribe_reply']);
    $router->post('merchants/{merchant}/reply', ['uses' => 'MerchantController@storeReply', 'as' => 'merchants.reply']);
    $router->post('merchants/{merchant}/reply/{reply}', ['uses' => 'MerchantController@storeReply', 'as' => 'merchants.reply.update']);
    $router->delete('merchants/{merchant}/reply/{reply}', ['uses' => 'MerchantController@deleteReply', 'as' => 'merchants.reply.delete']);
    $router->get('merchants/{merchant}/reply/{reply}/edit', ['uses' => 'MerchantController@editReply', 'as' => 'merchants.reply.edit']);
    $router->post('merchants/{merchant}/menu', ['uses' => 'MerchantController@storeMenu', 'as' => 'merchants.menu']);
    $router->post('merchants/{merchant}/menu/{menu}', ['uses' => 'MerchantController@storeMenu', 'as' => 'merchants.menu.update']);
    $router->delete('merchants/{merchant}/menu/{menu}', ['uses' => 'MerchantController@deleteMenu', 'as' => 'merchants.menu.delete']);
    $router->get('merchants/{merchant}/menu/{menu}/edit', ['uses' => 'MerchantController@editMenu', 'as' => 'merchants.menu.edit']);
    $router->get('merchants/{merchant}/menu/publish', ['uses' => 'MerchantController@publishMenu', 'as' => 'merchants.menu.publish']);
    // End:客户管理
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

    //vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
    // 项目管理
    $router->resource('projects', 'ProjectController');
    $router->match(['get', 'post'],'projects/{project}/design_form', ['uses' => 'ProjectController@designForm', 'as' => 'projects.design_form']);
    //导出作品
    $router->match(['get', 'post'], 'projects/{project}/players/artimage', ['uses'=>'PlayerController@artImage', 'as'=>'player.artimage']);
    $router->resource('projects/{project}/players', 'PlayerController');
    $router->match(['get', 'put'], 'projects/{project}/rules', ['uses' => 'ProjectController@rules', 'as' => 'projects.rules']);
    $router->post('projects/{project}/gen_cookie_url', ['uses' => 'ProjectController@genCookieUrl', 'as' => 'projects.gen_cookie_url']);
    $router->resource('projects/{project}/vote_logs', 'VoteLogController');
    $router->resource('projects/{project}/hongbao_logs', 'HongbaoLogController');
    // End:项目管理
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    

    //频道管理
    $router->resource('channels', 'ChannelController');

    //文件上传
    $router->match(['get', 'post'], 'common/ueupload', ['uses'=>'CommonController@ueupload', 'as'=>'common.ueupload']);

    //集字
    $router->get('projects/{project_id}/jizi/logs', ['uses'=>'JiziController@logs', 'as'=>'jizi.logs']);
    $router->match(['get', 'post'],'projects/{project_id}/jizi/setting', ['uses'=>'JiziController@setting', 'as'=>'jizi.setting']);

    //抽奖
    $router->match(['get', 'post'], 'prizes/{project}/majia', ['uses'=>'PrizesController@majia', 'as'=>'prizes.majia']);
    $router->match(['get', 'post'], 'prizes/{project}/setting', ['uses'=>'PrizesController@setting', 'as'=>'prizes.setting']);
    $router->post('prizes/{project}/majia/del', ['uses'=>'PrizesController@del', 'as'=>'prizes.del']);
    $router->get('prizes/{project}/logs', ['uses'=>'PrizesController@logs', 'as'=>'prizes.logs']);
    $router->match(['get', 'put'],'prizes/{project}/logs/{id}/edit', ['uses'=>'PrizesController@logsEdit', 'as'=>'prizes.logs_edit']);
    $router->get('prizes/{project}/zhuli_logs', ['uses'=>'PrizesController@zhuliLogs', 'as'=>'prizes.zhuli_logs']);

    //统计报表
    $router->get('datareport/{project}', ['uses'=>'ReportController@index', 'as'=>'report.index']);
    $router->get('datareport/{project}/datas', ['uses'=>'ReportController@datas', 'as'=>'report.datas']);
    $router->get('datareport/{project}/datasdetail', ['uses'=>'ReportController@datasdetail', 'as'=>'report.datasdetail']);

    //选手上传图片的角度旋转
    $router->post('player/img_rotate', ['uses'=>'PlayerController@imgRotate', 'as'=>'player.img_rotate']);
    //砍价
    $router->get('bargain/{project}/logs', ['uses'=>'BargainController@logs', 'as'=>'bargain.logs']);
    $router->get('bargain/{project}/exchange_qrcode_download', ['uses'=>'BargainController@exchangeQrCodeDownload', 'as'=>'bargain.exchange_qrcode_download']);
    //微信素材
    $router->resource('merchants/{merchant}/material', 'WechatMaterialController');

    //营销管理
    $router->resource('yxbusiness', 'Yx\YxBusinessController');
    $router->resource('yxpurpose', 'Yx\YxPurposeController');
    $router->resource('yxfunction', 'Yx\YxFunctionController');
    $router->resource('yxpartner', 'Yx\YxPartnerController');
    $router->resource('yxtempcase', 'Yx\YxTempCaseController');
    $router->resource('yxclassiccase', 'Yx\YxClassicCaseController');
    $router->resource('yxbanner', 'Yx\YxBannerController');
    //审核选手
    $router->post('player/update_status', ['uses'=>'PlayerController@updateStatus', 'as'=>'player.update_status']);

    $router->get('tongji', ['uses'=>'TongjiController@index', 'as'=>'tongji.index']);
    $router->get('tongji_branch', ['uses'=>'TongjiController@getBranchTj', 'as'=>'tongji.branch']);

    //公版新闻
    $router->get('news/{project}/blocks', ['uses'=>'NewsController@blocks', 'as'=>'news.blocks']);
    $router->match(['get', 'post'],'news/{project}/block/{block_id}', ['uses'=>'NewsController@block', 'as'=>'news.block']);
    $router->match(['get', 'post'],'news/{project}/blocks/{id}/edit', ['uses'=>'NewsController@detail', 'as'=>'news.detail']);
    $router->match(['get', 'post'],'news/{id}/blocks/delete', ['uses'=>'NewsController@delete', 'as'=>'news.delete']);

    //红包
    $router->match(['get', 'post'], 'hongbao/{project}/setting', ['uses'=>'HongbaoController@setting', 'as'=>'hongbao.setting']);
    $router->get('hongbao/{project}/logs', ['uses'=>'HongbaoController@logs', 'as'=>'hongbao.logs']);
    $router->get('hongbao/{project}/zhulis', ['uses'=>'HongbaoController@zhulis', 'as'=>'hongbao.zhulis']);
    $router->get('hongbao/{project}/billings', ['uses'=>'HongbaoController@billings', 'as'=>'hongbao.billings']);
    $router->match(['get', 'post'], 'hongbao/{project}/majia', ['uses'=>'HongbaoController@majia', 'as'=>'hongbao.majia']);
    $router->post('hongbao/{project}/majia/del', ['uses'=>'HongbaoController@del', 'as'=>'hongbao.del']);


});
