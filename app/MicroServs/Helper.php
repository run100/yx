<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/10/24
 * Time: 上午10:40
 */

namespace App\MicroServs;


use App\Lib\SiteUtils;

ini_set("yar.timeout",60000);

class Helper
{
    /**
     * 获得 服务的 client 实例(Yar_Client)
     *
     * @param $name
     * @return \yar_client
     */
    public function getClientInst($name)
    {
        return app("microservs.client.$name");
    }

    /**
     * 并发调用
     * 好处:
     *   假如每次$block->read(xxx)需要耗时1秒。某个 action 需要调用6次该服务，如果是阻塞式调用，则 action 处理时间至少为6秒;
     *   而如果以并发方式调用, 因为是多线程同时调用, 不考虑其他因素时 action 处理时间将接近1秒
     * 坏处:
     *   某次服务的调用不能依赖其他服务的返回结果; 比如 B 服务的 x1方法的调用需要依赖 A 服务的 y1方法返回值。则只能进行阻塞式调用。
     *
     * @param \Closure $process
     * @param boolean $wait     是否等待返回结果
     * @return array
     *
     * 调用示例:
     * $ret = MicroServs::concurrent(function($block, $user) {
     *     //$block 和 $user 会被分别自动注入 Block / User 服务实例; 其他服务同样只要写名字即可
     *
     *     $data = [];
     *     $data['block1'] = $block->read(1);  //注: 此处 read(1) 的返回值是个字符串占位符,并非服务返回的数据,下同
     *     $data['block2'] = $block->read(2);
     *     $data['block3'] = $block->read(3);
     *     $data['user'] = $user->info(666);
     *
     *     return $data;
     * });
     *
     * 以上匿名函数中的服务方法将会被同时调用; 并等待所有服务调用返回。
     * concurrent 返回值与匿名函数的返回值结构一致。
     * $ret 返回结果:
       <pre>
       Array
       (
           [block1] => ... //$block->read(1) 的调用结果
           [block2] => ... //$block->read(2) 的调用结果
           [block3] => ... //$block->read(3) 的调用结果
           [user]   => ... //$user->info(666) 的调用结果
       )
       </pre>
     */
    public function concurrent(\Closure $process, $wait = true)
    {
        $info = new \ReflectionFunction($process);
        $iParams = $info->getParameters();

        $params = [];
        $ret = [];
        $callback = function($response, $callinfo) use(&$ret) {
            $uniqid = $callinfo['uniqid'];
            $k = array_search($uniqid, $ret);
            if ($k !== false) {
                $ret[$k] = $response;
            }
        };


        $rfl = new \ReflectionClass(\Yar_Client::class);
        $rflOptions = $rfl->getProperty('_options');
        $rflUri = $rfl->getProperty('_uri');
        $rflOptions->setAccessible(true);
        $rflUri->setAccessible(true);
        foreach ($iParams as $p) {
            $c = app("microservs.client.{$p->getName()}");
            $params[] = new ConcurrentHelper($c, $rflUri->getValue($c), $rflOptions->getValue($c), $callback, $ret);
        }

        $ret = $process(... $params);

        if (!$wait) {
            return false;
        }

        \Yar_Concurrent_Client::loop();
        ksort($ret);

        $ret = array_filter($ret, function($item) {
            if (!is_string($item)) {
                return true;
            }

            return !preg_match('@^yar_concurrent_call:@', $item);
        });

        return $ret;
    }

    /**
     * 动态调用
     *
     * @param $name
     * @param $arguments
     * @return mixed
     *
     * 调用示例:
     * MicroServs::block();   //取得 Block 服务实例
     * MicroServs::user();    //取得 User 服务实例
     *
     */
    function __call($name, $arguments)
    {
        if (array_key_exists($name, config('microservs.servs'))) {
            return $this->getClientInst($name);
        }

        throw new \BadMethodCallException(
            "Undefined method '$name'."
        );
    }
}


class ConcurrentHelper
{
    protected $url;
    protected $callback;
    protected $ret;
    protected $options;
    protected $client;

    public function __construct(?Client $c, $url, $options, $callback, &$ret)
    {
        $this->url = $url;
        $this->callback = $callback;
        $this->options = $options;
        $this->ret = &$ret;
        $this->client = $c;
    }


    public function __call($name, $arguments)
    {
        $uniqid = wj_uuid();
        $uniqid = "yar_concurrent_call:$uniqid";

        $callback = function ($response, $callinfo) use ($uniqid) {
            $callinfo['uniqid'] = $uniqid;
            $cbk = $this->callback;
            $cbk($response, $callinfo);
        };

        $options = $this->options;

        $noncestr = sprintf('%.8f', microtime(true));
        $options[YAR_OPT_HEADER] = [
            'x-yar-sign: ' . $this->client->sign([
                'm' => $name,
                'p' => $arguments
            ], $noncestr),
            'x-yar-noncestr: ' . $noncestr
        ];
        \Yar_Concurrent_Client::call($this->url, $name, $arguments, $callback, null, $options);
        return $uniqid;
    }
}