<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['as' => 'site.'], function () {
    Route::get('', ['uses' => 'Web\SiteController@index', 'as' => 'index']);
});


Route::group(['as' => 'wechat.', 'prefix' => 'wechat'], function () {
    Route::get('bind/{code}', ['uses' => 'Web\WechatController@bind', 'as' => 'bind']);
    Route::get('bind_callback', ['uses' => 'Web\WechatController@bindCallback', 'as' => 'bind_callback']);
    Route::get('jssdk_config', ['uses' => 'Web\WechatController@jssdkConfig', 'as' => 'jssdk_config']);
    Route::get('jssdk_config_v2', ['uses' => 'Web\WechatController@jssdkConfigV2', 'as' => 'jssdk_config_v2'])->middleware(['cache:E300']);
});




Route::get("test/weui", "Web\TestController@weui");
Route::get("test/mobile", "Web\TestController@mobile");
Route::get("test/pc", "Web\TestController@pc");
Route::get("test/clean", "Web\TestController@clean");
Route::get("test/bootstrap", "Web\TestController@bootstrap");

//公版新闻专题
\App\Lib\SiteUtils::exportPublicNewsRoutes('news{id}');

//公版投票专题
\App\Lib\SiteUtils::exportPublicVoteRoutes('tp-{id}');

//公版集字专题
\App\Lib\SiteUtils::exportPublicJiziRoutes('jz{id}');

//公版抽奖专题
\App\Lib\SiteUtils::exportPublicPrizesRoutes('cj{id}');

//公版砍价
\App\Lib\SiteUtils::exportPublicBargainRoutes('kj{id}');

//公版红包
\App\Lib\SiteUtils::exportPublicRedpaketRoutes('hb{id}');


\App\Lib\SiteUtils::exportTemplateRoutes(public_path('web'));