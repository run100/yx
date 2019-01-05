<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2018/1/23
 * Time: 上午11:17
 */

namespace App\MicroServs;


class Client extends \Yar_Client
{
    public $token = false;

    public function __call($name, $arguments)
    {
        $noncestr = sprintf('%.8f', microtime(true));
        $this->setOpt(YAR_OPT_HEADER, array(
            'x-yar-sign: ' . $this->sign([
                'm' => $name,
                'p' => $arguments
            ], $noncestr),
            'x-yar-noncestr: ' . $noncestr
        ));

        return parent::__call($name, $arguments);
    }


    public function sign($arguments, $noncestr)
    {
        if (!$this->token) {
            return false;
        }

        $packagers = [
            'msgpack'   => 'msgpack_pack',
            'json'      => 'json_encode',
            'php'       => 'serialize'
        ];

        $packager = $packagers[$this->_options[YAR_OPT_PACKAGER] ?: 'msgpack'];

        $text = $packager($arguments) . $this->token . $noncestr;
        return md5($text);
    }
}