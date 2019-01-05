<?php

if (env('APP_ENV') === 'local') {
    $online = [
        'url'       => "http://zt.365jia.cn/yar/online",
        'packager'  => 'msgpack',
        'token'     => 'f7015e77810ec13a5d6024e84864b679'
    ];
} else {
    $online = [
        'class'     => "\App\MicroServs\Servs\Online",
        'packager'  => 'msgpack'
    ];
}


return [
    'servs' => [
        'wanjia_user'   => [
            'url'   => env('MICRO_SERVS_WANJIA_USER_URL', 'http://365jia.cn/yar/user.php'),
            'token' => false           //please set token for security
        ],
        'shequ_user'    => [
            'url'   => env('MICRO_SERVS_WANJIA_USER_URL', 'https://365shequ.com/yar/user.php')
        ],
        //基于 PHP 实现的 区块 微服务
        'block'     => [
            'class'     => "\App\MicroServs\Servs\Block",
            'packager'  => 'msgpack'   //可选 php | json | msgpack, php/msgpack 支持传递对象，json只支持传递数组
        ],
        //操作线上数据
        'online'    => $online
    ]
];