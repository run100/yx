<?php

namespace App\Providers;

use App\MicroServs\Client;
use App\MicroServs\Helper;
use Illuminate\Support\ServiceProvider;

class MicroServsProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!defined('BLOCK_ITEM_TYPE_IMAGE')) {
            define('BLOCK_ITEM_TYPE_IMAGE', 1);         // 图片
            define('BLOCK_ITEM_TYPE_CAPTION', 2);       // 标题
            define('BLOCK_ITEM_TYPE_SUBCAPTION', 4);    // 二级标题
            define('BLOCK_ITEM_TYPE_INTRO', 8);         // 引言
            define('BLOCK_ITEM_TYPE_LIST', 16);         // 列表
            define('BLOCK_ITEM_TYPE_VOTE', 32);         // 投票
            define('BLOCK_ITEM_TYPE_CAT_LIST', 64);     // 带类别的列表
            define('BLOCK_ITEM_TYPE_ICON_LIST', 128);   // 带图标的列表
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('microservs', function () {
            return $this->app->make(Helper::class);
        });

        foreach (config('microservs.servs') as $name => $cls) {
            $app = $this->app;

            if (@$cls['class']) {
                $app->singleton("microservs.api.$name", function () use ($app, $cls) {
                    return $app->make($cls['class']);
                });
            }

            $app->singleton("microservs.client.$name", function () use ($app, $name, $cls) {
                if (@$cls['class']) {
                    $host = @$cls['host'] ?: 'localhost';
                    $client = new Client("http://$host/yar/$name");
                    $client->token = @app("microservs.api.$name")->token ?: false;
                } else {
                    $client = new Client($cls['url']);
                    $client->token = @$cls['token'] ?: false;
                }


                $client->setOpt(YAR_OPT_PACKAGER, @$cls['packager'] ?: 'php');
                return $client;
            });
        }
    }
}

