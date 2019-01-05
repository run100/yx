<?php

Route::group([
    'as'            => 'yx2018.',
    'prefix'        => 'yx2018',
    'middleware'    => ['project']
], function () {
    //请求微信授权的页面
    Route::get('start', ['uses' => 'Controller@start', 'as' => 'start'])
        ->middleware(['web', 'wechat.oauth:snsapi_userinfo']);

    //更新微信资料(需要后台授权)
    Route::get('regen', ['uses' => 'Controller@regen', 'as' => 'regen'])
        ->middleware(['web', 'wechat.oauth:snsapi_userinfo']);

    //可缓存的页面
    Route::get('ranking', ['uses' => 'Controller@ranking', 'as' => 'ranking'])
        ->middleware('cache:30'); //报名首日数据变动过快，缓存调整为30秒
        //->middleware('cache:E600'); //间隔10分钟刷新缓存


    Route::get('search_name', ['uses' => 'Controller@searchName', 'as' => 'search_name']);
    Route::post('check_player_info', ['uses' => 'Controller@checkPlayerInfo', 'as' => 'check_player_info']);
    Route::post('pay_callback', ['uses' => 'Controller@payCallback', 'as' => 'pay_callback']);
    Route::get('ranking_head_pc', ['uses' => 'Controller@rankingHeadPc', 'as' => 'ranking_head_pc']);

    Route::get('test', ['uses' => 'Controller@test', 'as' => 'test']);
});

Route::group([
    'as'            => 'yx2018.',
    'prefix'        => 'yx2018',
    'middleware'    => ['project', 'web', 'wechat.oauth:check_only']
], function () {
    //需要微信认证的操作
    Route::get('status', ['uses' => 'Controller@status', 'as' => 'status']);
    Route::get('user', ['uses' => 'Controller@user', 'as' => 'user']);
    Route::post('save_players', ['uses' => 'Controller@savePlayers', 'as' => 'save_players']);
    Route::post('confirm_player', ['uses' => 'Controller@confirmPlayer', 'as' => 'confirm_player']);
    Route::post('make_order', ['uses' => 'Controller@makeOrder', 'as' => 'make_order']);
    Route::get('votelist', ['uses' => 'Controller@votelist', 'as' => 'votelist']);
    Route::get('ranking_head', ['uses' => 'Controller@rankingHead', 'as' => 'ranking_head']);

    Route::post('search_player', ['uses' => 'Controller@searchPlayer', 'as' => 'search_player']);
});




Route::group([
    'as'            => 'yx2018.',
    'prefix'        => config('admin.route.prefix'),
    'middleware'    => config('admin.route.middleware'),
], function () {
    Route::get('yx2018/stats', ['uses' => 'Controller@stats', 'as' => 'admin.stats']);
    Route::get('yx2018/billing', ['uses' => 'Controller@billing', 'as' => 'admin.billing']);
    Route::get('yx2018/sp200qrcode', ['uses' => 'Controller@sp200qrcode', 'as' => 'admin.sp200qrcode']);
    Route::match(
        ['GET', 'POST'],
        'yx2018/wx_update_perms',
        ['uses' => 'Controller@wxUpdatePerms', 'as' => 'admin.wx_update_perms']
    );
});