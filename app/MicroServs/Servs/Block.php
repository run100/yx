<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/10/24
 * Time: 上午10:50
 */

namespace App\MicroServs\Servs;

use Illuminate\Support\Collection;

/**
 * Class Block
 * @package App\MicroServs\Servs
 * @deprecated 组版区块的 Yar 服务已用 Lua 重构，位置: lua/block.lua; 此处仅作参考
 */
class Block
{
    public $token = '69bcd149fc1c5685b81559ac2dc23e2a';

    /**
     * 读取组版数据
     * @param int $block_id 组版ID
     * @param int $type     区块样式筛选,默认全部,支持按位筛选; 具体参考 http://kenny.365jia.lab:8000/projects/365jia/wiki/CustomBlockGuide
     * @param int $ttl      缓存时间(秒)
     * @return array 组版数据
     *
     * 常量定义:
     * define('BLOCK_ITEM_TYPE_IMAGE', 1);         // 图片
     * define('BLOCK_ITEM_TYPE_CAPTION', 2);       // 标题
     * define('BLOCK_ITEM_TYPE_SUBCAPTION', 4);    // 二级标题
     * define('BLOCK_ITEM_TYPE_INTRO', 8);         // 引言
     * define('BLOCK_ITEM_TYPE_LIST', 16);         // 列表
     * define('BLOCK_ITEM_TYPE_VOTE', 32);         // 投票
     * define('BLOCK_ITEM_TYPE_CAT_LIST', 64);     // 带类别的列表
     * define('BLOCK_ITEM_TYPE_ICON_LIST', 128);   // 带图标的列表
     *
     * 调用演示:
     * $ret = $block->read(1, BLOCK_ITEM_TYPE_IMAGE|BLOCK_ITEM_TYPE_LIST, 600);
     * 取组版数据,并过滤出 images / lists 类型的数据
     *
     * 返回数据:
       <pre>
       Array
       (
            [lists] => Array
                (
                    [0] => stdClass Object
                        (
                            [id] => 2505584
                            [type] => 16
                            [seq] => 1
                            [updated_at] => 2017-06-13 10:43:30
                            [created_at] => 2017-06-07 15:53:28  //以下字段来自反序列化 data 的内容
                            [title] => 之心城#278
                            [link] =>
                            [break] => true
                            [icon] => 0
                            [color] => 0
                            [bold] => false
                            [clean_title] => 之心城               //#分割数据中干净的 title
                            [extras] => Array                    //解析#分割数据的其他字段
                                (
                                    [0] => 278
                                )

                            [stype] => lists                     //type 对应的类型编号 字符串表示形式
                        )
                        ...
                )
            [images] => Array
                (
                    [0] => stdClass Object
                        (
                            [id] => 2505584
                            [type] => 1
                            [seq] => 1
                            [updated_at] => 2017-06-13 10:43:30
                            [created_at] => 2017-06-07 15:53:28
                            [title] => 之心城#278
                            [link] =>
                            [break] => true
                            [icon] => 0
                            [color] => 0
                            [bold] => false
                            [clean_title] => 之心城
                            [extras] => Array
                                (
                                    [0] => 278
                                )

                            [stype] => images
                        )
                        ...
                )
           ...
       )
       </pre>
     *
     * 调用演示2:
     * $block->read(1);
     *
     * 等同于:
     * $block->read(1, null, 60);
     *
     */
    public function read($block_id, $type = null, $ttl = 60)
    {
        $cache_key = $this->getCacheKey($block_id, $type, $ttl);
        return \Cache::remember($cache_key, $ttl / 60, function () use ($block_id, $type) {
            return $this->readRaw($block_id, $type);
        });
    }

    /**
     * 刷新区块缓存
     * @param int $block_id 组版ID
     * @param int $type     区块样式筛选,默认全部,支持按位筛选; 具体参考 http://kenny.365jia.lab:8000/projects/365jia/wiki/CustomBlockGuide
     * @param int $ttl      缓存时间(秒)
     * @return bool
     *
     * 调用演示:
     * $block->invalidate(1);
     *
     * 等同于:
     * $block->invalidate(1, null, 60);
     *
     * 将清除 Redis 中 key:
     * microservs:block:id1_type_ttl60
     */
    public function invalidate($block_id, $type = null, $ttl = 60)
    {
        \Cache::forget($this->getCacheKey($block_id, $type, $ttl));
        return true;
    }

    /**
     * 读取组版数据(无缓存模式); 考虑执行效率,不建议直接使用
     * @param int $block_id  组版ID
     * @param int $type      区块样式筛选,默认全部,支持按位筛选; 具体参考 http://kenny.365jia.lab:8000/projects/365jia/wiki/CustomBlockGuide
     * @return array 组版数据
     *
     * 调用方式参考 read
     */
    public function readRaw($block_id, $type = null)
    {
        $db = \DB::connection('365jia');
        $ret = $db->select('
            select id, type, data, seq, updated_at, created_at
            from t_custom_block_item
            where custom_block_id = :block_id
            order by type, seq
            limit :limit
        ', [
            ':block_id' => (int)$block_id,
            ':limit'    => $type === null ? 500 : 100
        ]);

        $types = [
            BLOCK_ITEM_TYPE_IMAGE      => 'images',
            BLOCK_ITEM_TYPE_CAPTION    => 'captions',
            BLOCK_ITEM_TYPE_SUBCAPTION => 'subcaptions',
            BLOCK_ITEM_TYPE_INTRO      => 'intros',
            BLOCK_ITEM_TYPE_LIST       => 'lists',
            BLOCK_ITEM_TYPE_VOTE       => 'votes',
            BLOCK_ITEM_TYPE_CAT_LIST   => 'catlists',
            BLOCK_ITEM_TYPE_ICON_LIST  => 'iconlists',
        ];

        $ret = collect($ret)->each(function (&$item) use ($types) {
            $data = unserialize($item->data);
            foreach ($data as $k => $v) {
                $item->{$k} = $v;
            }
            unset($item->data);

            $pos = strpos($item->title, '#');
            if ($pos !== false) {
                $item->clean_title = substr($item->title, 0, $pos);
                $item->extras = explode('#', substr($item->title, $pos + 1));
            }

            $item->stype = $types[$item->type];
        });

        if ($type) {
            $ret = $ret->filter(function ($item) use ($type) {
                return $item->type & $type;
            });
        }

        $ret = $ret->groupBy('stype')->map(function (?Collection $item) {
            return $item->all();
        });

        return $ret->all();
    }

    protected function getCacheKey($block, $type, $ttl)
    {
        return "microservs:block:id{$block}_type{$type}_ttl{$ttl}";
    }
}