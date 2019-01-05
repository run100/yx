<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2018/1/23
 * Time: 下午3:40
 */

namespace App\MicroServs\Servs;
use App\Models\Project;

/**
 * Class Online
 * @package App\MicroServs\Servs
 * 用于操作线上数据的微服务
 */
class Online
{
    public $token = 'f7015e77810ec13a5d6024e84864b679';

    /**
     * 获取微信下发给第三方平台的 ticket
     */
    public function readWechatComponentTicket()
    {
        return \EasyWeChat::open_platform()->verify_ticket->getTicket();
    }

    /**
     * 更新线上Project配置
     */
    public function uploadProject(Project $proj)
    {
        $old = Project::matchByPath($proj->path);

        //如果之前上传过则只更新conf_base_form_design/capacity字段
        if ($old) {
            if ($old->configs->dev_id == $proj->id) {
                $confs = $old->configs;
                $confs->base_form_design = $proj->conf_base_form_design;
                $old->configs = $confs;
                $old->capacity = $proj->capacity;
                $old->conf_dev_id = $proj->id;
                $old->conf_manage_urls = $proj->conf_manage_urls;
                return $old->save();
            } else {
                return 0;
            }
        } else {
            $proj_id = $proj->id;

            $proj = $proj->replicate(['merchant_id', 'channel_id']);
            $proj->merchant_id = 2;
            $proj->channel_id = 6;
            $proj->conf_dev_id = $proj_id;
            return $proj->save();
        }
    }
    /**
     * 更新线上Project配置
     */
    public function downloadProject($path)
    {
        $proj = Project::matchByPath($path, false);
        return $proj;
    }

    /**
     * 支持下载Redis数据到本地，用于调试
     * @param $pattern   Key Pattern; 支持*查询
     * @return array
     */
    public function readRemoteRedis($pattern)
    {
        $keys = \RedisDB::keys($pattern);
//        if (count($keys) > 20000) {
//            return [
//                'code'  => 1,
//                'msg'   => 'Key个数超过20000，不能同步'
//            ];
//        }

        $data = [];
        foreach ($keys as $k) {
            $data[$k] = base64_encode(\RedisDB::dump($k));
        }
        return [
            'code'  => 0,
            'msg'   => 'ok',
            'data'  => $data
        ];
    }
}