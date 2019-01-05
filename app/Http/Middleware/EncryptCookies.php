<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as BaseEncrypter;

class EncryptCookies extends BaseEncrypter
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    public function isDisabled($name)
    {
        //带js:前缀的cookie用于前端控制不需要加密
        if (strpos($name, 'js:') === 0) {
            return true;
        }

        return parent::isDisabled($name);
    }


}
