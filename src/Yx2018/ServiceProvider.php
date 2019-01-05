<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2018/3/6
 * Time: 上午11:05
 */

namespace App\Features\Yx2018;

use App\Features\Yx2018\Controllers\Controller;
use App\Features\Yx2018\Jobs\DataSyncJob;
use App\Models\Player;
use Illuminate\Support\ServiceProvider as LaravelProvider;
use Illuminate\Support\Str;

/**
 * 这里只是做了个数据事件监听，实际上ServiceProvider能做的事不止这些;
 * 比如App\Providers\AppServiceProvider中对blade语法进行扩展; 比如 plugins/wanjia-common 中用ServiceProvider对项目源码进行模块化拆分
 */
class ServiceProvider extends LaravelProvider
{
    protected $phoneDirtyPlayer = [];

    /**
     * Player表索引对应的字段是什么; 避免在查询中写 str1\str2 不明白什么含义
     */
    public static function getPlayerIndex($field)
    {
        $indexes = [
            'openid'    => 'str1',
            'name'      => 'str2',
            'passport'  => 'str3',
            'phone'     => 'str4',
            'supply_loc'=> 'str5',
            'gender'    => 'str6',
            'size'      => 'str7',
            'line'      => 'str8',
            'is_master' => 'str9',
            'is_union'  => 'str10',
            'age'       => 'int1'
        ];
        return $indexes[$field];
    }

    /**
     * ServiceProvider入口在这里
     */
    public function boot()
    {
        $this->listenPlayerModelEvent();
    }

    /**
     * 监听Player数据库事件
     */
    protected function listenPlayerModelEvent()
    {
        \Event::listen('model.player.*', function ($event, $payload) {
            list($path, $player) = $payload;
            if ($path !== '/yx2018') {
                return;
            }

            $event = substr($event, 13);
            $method = "onEventPlayer" . ucfirst(Str::camel($event));

            if (method_exists($this, $method)) {
                $this->$method($player);
            }
        });
    }

    /**
     * 当Player对象将要修改还未写入到数据库
     */
    protected function onEventPlayerUpdating(Player $player)
    {
        $phoneIndex = static::getPlayerIndex('phone');
        $passportIndex = static::getPlayerIndex('passport');
        $lineIndex = static::getPlayerIndex('line');
        $openidIndex = static::getPlayerIndex('openid');
        $nameIndex = static::getPlayerIndex('name');

        if ($player->isDirty($nameIndex) || $player->isDirty($passportIndex)) {
            $lua = Controller::getLuaRedis();
            $player->info_hash = $lua->hashPlayer($player->info_name, $player->info_passport);
        }

        if ($player->isDirty($lineIndex)) {
            raise_validation_error('msg', '报名中不允许修改目标终点');
        }

        if ($player->isDirty($openidIndex)) {
            raise_validation_error('msg', 'OpenID不允许修改');
        }

        if ($player->isDirty($phoneIndex)) {
            $count = Player::repository()->countByProjectId($player->project_id, [
                $phoneIndex      => $player->$phoneIndex
            ]);

            if ($count) {
                raise_validation_error('msg', '手机号已被占用');
            }

            $this->phoneDirtyPlayer[$player->str4] = [$player, $player->getOriginal('phone')];
        }
        if ($player->isDirty($passportIndex)) {
            $count = Player::repository()->countByProjectId($player->project_id, [
                $passportIndex   => $player->$passportIndex
            ]);

            if ($count) {
                raise_validation_error('msg', '证件号已被占用');
            }
        }

        if ($player->isDirty('ticket_no')) {
            if ($player->ticket_no) {
                $count = Player::repository()->countByProjectId($player->project_id, [
                    'ticket_no'      => $player->ticket_no
                ]);

                if ($count) {
                    raise_validation_error('msg', '毅行编号已被占用');
                }

                $player->checked = 1;
            } else {
                $player->ticket_no = null;
            }
        }
    }

    /**
     * 当Player对象将要被删除
     */
    protected function onEventPlayerDeleting()
    {
        raise_validation_error('message', '不允许删除');
    }

    /**
     * 当Player对象被插入到数据库
     */
    protected function onEventPlayerCreated(Player $player)
    {
        $job = new DataSyncJob('player_to_redis', $player->id);
        $job->onQueue('yx2018_data');
        dispatch($job);
    }

    /**
     * 当Player对象被更新到数据库
     */
    protected function onEventPlayerUpdated(Player $player)
    {
        $phoneDirty = @$this->phoneDirtyPlayer[$player->str4];

        if ($phoneDirty && ($phoneDirty[0] === $player)) {
            $oldphone = $phoneDirty[1];
        } else {
            $oldphone = $player->str4;
        }
        
        $job = new DataSyncJob('player_upto_redis', $oldphone, $player->id);
        $job->onQueue('yx2018_data');
        dispatch($job);
    }

}