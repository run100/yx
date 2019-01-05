<?php

$router->addRoute(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'], '/yar/{serv}', function ($serv) {
    try {
        $api = app("microservs.api.$serv");
    } catch (ReflectionException $ex) {
        abort(404);
    }

    if (!property_exists($api, 'token')) {
        throw new Exception("Class " . get_class($api) . " must have a property named 'token' for security.");
    }

    //签名验证及防重放; 改为 false 可用于调试
    if (true && ($api->token !== false)) {
        $sign = @$_SERVER['HTTP_X_YAR_SIGN'];
        $noncestr = @$_SERVER['HTTP_X_YAR_NONCESTR'];

        //$noncestr传入的是时间戳，允许5分钟误差的情况下防Replay攻击
        if (abs(time() - $noncestr) > 300) {
            throw new Exception("Noncestr is invalid.");
        }


        $input = file_get_contents('php://input');
        $packager = strtolower(trim(substr($input, 82, 8)));    //协议头中最后8字节为packager
        if (($pos = strpos($packager, "\0")) !== false) {
            $packager = substr($packager, 0, $pos);
        }

        $input = substr($input, 90);    //摘掉 YAR 协议头 90B
        if ($packager === 'msgpack') {
            $input = chr(0x82)              //因为删掉了 transaction_id，3元素变2元素，0x83变为0x82
                . substr($input, 8);        //剩下的部分首字节0x83表示3元素的Map，紧跟的元素i表示transaction_id，不需要所以删掉。Key+Value+控制符总计7个字节
        } elseif ($packager === 'json') {
            $input = preg_replace('@^\{"i":\d+,@', '{', $input);  //移除 transaction_id
        } elseif ($packager === 'php') {
            $input = preg_replace('@^a:3:\{s:1:"i";i:\d+;@', 'a:2:{', $input);  //移除 transaction_id
        }

        $sign_check = md5($input . $api->token . $noncestr);
        if ($sign_check !== $sign) {
            throw new Exception("Sign check fail.");
        }
        //Log::debug("Sign: $sign, Noncestr: $noncestr, Input: $input");
    }

    $server = new \Yar_Server($api);

    ob_start();
    $server->handle();
    $ret = ob_get_clean();
    return $ret;
});

$router->addRoute(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'], 'callbacks/weixin_auth', 'App\Http\Controllers\Web\WechatController@events');
$router->addRoute(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'], 'callbacks/app/{appid}/event', 'App\Http\Controllers\Web\WechatController@events');
