<?php

define('USE_LUMEN', 1);

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/../bootstrap/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/micro.php';

Illuminate\Http\Request::setTrustedProxies(['0.0.0.0/0']);
Illuminate\Http\Request::setTrustedHeaderName(Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO, 'X_CLIENT_SCHEME');
if (in_array(strtolower(@$_SERVER['HTTP_X_CLIENT_SCHEME']), array('https', 'on', 'ssl', '1'), true)
    || !empty(@$_SERVER['HTTPS']) && 'off' !== strtolower(@$_SERVER['HTTPS'])) {
    header('Content-Security-Policy: upgrade-insecure-requests');
}
/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/
$app->run();