<?php

$conf = [
    'use_ide_helper'    => true,
    'module_support'    => true,
    'helpers_support'   => true,
    'module_namespace'  => 'App\Features',
    'module_base'       => 'src',
    'module_dirs'       => 'controllers,routes,config,views'
];

if ($conf['module_support']) {
    $conf['cache'] = [
        'configs'     => glob(base_path('src/*/config.php')),
        'resources'   => glob(base_path('src/*/resources')),
        'migrations'  => glob(base_path('src/*/migrations')),
        'providers'   => glob(base_path('src/*/ServiceProvider.php')),
        'helpers'     => glob(base_path('src/*/helpers.php')),
        'routes'      => glob(base_path('src/*/routes'))
    ];
}

return $conf;
