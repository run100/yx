<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/10/30
 * Time: 下午4:16
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Wanjia\Common\Database\ExModel;

/**
 *
 * @property array $conf_base_form_design
 *
 */
class Project extends Model
{
    use ExModel;

    protected $table = 'project';


    protected $casts = [
        'configs'   => 'object'
    ];

    protected $dates = [
        'start_at',
        'end_at'
    ];

    protected $_options_cache = [];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Project $m) {
            if ($m->configs === null) {
                $m->configs = new \stdClass();
            }

            if ($m->exists && $m->isDirty('path')) {
                static::invalidateRoutingCache($m->getOriginal('path'), $m->path);
            }
            event('model.project.saving', $m);
        });

        static::saved(function (Project $m) {

            \RedisDB::set("prj:{$m->id}:conf", wj_json_encode($m->configs));
            \RedisDB::set("prj:{$m->id}:ver", $m->updated_at->getTimestamp());
        });

    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function players()
    {
        return $this->hasMany(Player::class, 'project_id', 'id')->with('project');
    }


    public function setCapacityAttribute($value)
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        $this->attributes['capacity'] = $value;

        return $this;
    }

    public function can($capacity)
    {
        $capacities = explode(',', $this->capacity);
        return in_array($capacity, $capacities);
    }


    public function listOptionsByField($field, $with_options = false)
    {
        if (@$this->_options_cache[$field]) {
            if ($with_options) {
                return $this->_options_cache[$field];
            }

            $options = [];
            foreach ($this->_options_cache[$field] as $opt) {
                $options[$opt->key] = $opt->name;
            }

            return $options;
        }

        $form_design = collect(@$this->conf_base_form_design ?: []);
        $field = $form_design->where('field', '=', $field)->values()->get(0);

        $ret = $field;

        if (!($ret = @$ret->options)) {
            return [];
        }

        if (!($ret = @$ret->{$field->type})) {
            return [];
        }

        if (!($ret = @$ret->options)) {
            return [];
        }

        $this->_options_cache[$field->field] = $ret;

        if ($with_options) {
            return $ret;
        }

        $options = [];
        foreach ($ret as $opt) {
            $options[$opt->key] = $opt->name;
        }

        return $options;
    }

    public function getCapacityArrAttribute()
    {
        if (!$this->capacity) {
            return [];
        }
        return explode(',', $this->capacity);
    }

    public function getManageUrlsAttribute()
    {
        if (!$this->conf_manage_urls) {
            return [];
        }

        $ret = preg_match_all('@\[([^\[\]]+)\]([^\[\]\s]+)@', $this->conf_manage_urls, $m);
        if ($ret) {
            return array_combine($m[2], $m[1]);
        }

        return [];
    }

    public function getAttribute($key)
    {
        if (($pos = strpos($key, 'conf_')) === 0) {
            $field = substr($key, 5);
            return @$this->configs->$field;
        }

        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        if (($pos = strpos($key, 'conf_')) === 0) {
            $field = substr($key, 5);

            $info = $this->configs;
            if (!$info) {
                $info = new \stdClass();
            }

            $info->$field = $value;
            $this->configs = $info;

            return $this;
        }

        if (($pos = strpos($key, 'configs.')) === 0) {
            $field = substr($key, 8);

            $info = $this->configs;
            if (!$info) {
                $info = new \stdClass();
            }

            //支持多级点赋值
            $nodes = explode('.', $field);
            $lastNode = array_pop($nodes);
            $node = $info;
            foreach ($nodes as $n) {
                if (!@$node->$n) {
                    $node->$n = new \stdClass();
                }
                $node = $node->$n;
            }
            $node->$lastNode = $value;

            $this->configs = $info;

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    public function hasSetMutator($key)
    {
        if (($pos = strpos($key, 'conf_')) === 0) {
            return true;
        }

        if (($pos = strpos($key, 'configs.')) === 0) {
            return true;
        }
        return parent::hasSetMutator($key);
    }

    public function hasGetMutator($key)
    {
        if (($pos = strpos($key, 'conf_')) === 0) {
            return true;
        }

        return parent::hasGetMutator($key);
    }

    /**
     * @param string $path
     * @return Project
     */
    public static function matchByPath($path = null, $usecache = true)
    {
        if ($path === null) {
            $request = \Request::instance();
            if ($request->attributes->has('project')) {
                return $request->attributes->get('project');
            }

            $path = $request->getPathInfo();
        }
        $path = preg_replace('@/\s*$@', '', $path);

        if ($usecache && ($ret = static::fetchRoutingCache($path))) {
            return $ret;
        }

        $dirs = [];

        $p = $path;
        while ($p && $p !== '/') {
            $dirs[] = $p;
            $p = dirname($p);
        }

        if (!$dirs) {
            return null;
        }

        $ret = Project::repository()->findByPath($dirs);
        $ret = $ret->sortBy(function ($item) {
            return strlen($item->path);
        }, SORT_NUMERIC, true)->values();

        $ret = @$ret->offsetGet(0) ?: null;

        if ($ret) {
            static::storeRoutingCache($path, $ret);
        }

        return $ret;
    }


    /**
     * @param $path string
     * @return static|bool
     */
    protected static function fetchRoutingCache($path)
    {
        //因为APC缓存分布式存储在多台FPM主机上，没办法做一对多清理；只能做自动过期检查策略(借助Redis提供的版本号)
        //为什么不存在Redis中做缓存?
        //Project 实例serialize后占用空间很大，实测频繁访问会吃满Redis服务器带宽，造成性能瓶颈。而APC基于进程间通信无此问题
        //基于以上论点，Redis不适合存储被频繁访问的“大数据”；此场景APC较合适，但也要注意，APC是易失型存储。

        $id = \RedisDB::hget('common:proj_routing', $path);
        if (!$id) {
            return false;
        }

        $meta = apcu_fetch("common:proj_cache:$id");
        if ($meta['ver'] < \RedisDB::get("prj:$id:ver")) {
            $proj = Project::repository()->retrieveByPK($id);
            static::storeRoutingCache($path, $proj);
        } else {
            $proj = $meta['obj'];
        }

        return $proj;
    }

    /**
     * @param $path string
     * @param $proj static
     * @return void
     */
    protected static function storeRoutingCache($path, $proj)
    {
        $proj->merchant;
        \RedisDB::hset('common:proj_routing', $path, $proj->id);
        apcu_store("common:proj_cache:{$proj->id}", [
            'ver'   => $proj->updated_at->getTimestamp(),
            'obj'   => $proj
        ], 600);
    }

    protected static function invalidateRoutingCache(... $paths)
    {
        $keys = [];

        /** @var \Redis $redis */
        $redis = \RedisDB::connection();
        $redis->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY);
        foreach ($paths as $path) {
            $scaned = $redis->hscan('common:proj_routing', null, "$path*", 9999) ?: [];
            $keys = array_merge($keys, array_keys($scaned));
        }
        $keys = array_values(array_unique(array_filter($keys)));

        if ($keys) {
            \RedisDB::hdel('common:proj_routing', ... $keys);
        }
    }

    /**
     * 是否为集字
     * @return int
     */
    public function isJizi()
    {
        return empty($this->path) ? 0 : preg_match('/^\/jz\d+$/', $this->path);
    }

    /**
     * 是否为抽奖
     * @return int
     */
    public function isPrize()
    {
        return empty($this->path) ? 0 : preg_match('/^\/cj\d+$/', $this->path);
    }


    /**
     * 是否为新闻模板
     * @return int
     */
    public function isNews()
    {
      return empty($this->path) ? 0 : preg_match('/^\/news\d+$/', $this->path);
    }

}