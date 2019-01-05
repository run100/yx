<?php
/**
 * Created by PhpStorm.
 * User: zhuzq
 * Date: 2018/10/19
 * Time: 10:28
 */

namespace App\Zhuanti\Controllers;


use App\Http\Controllers\BaseController;

class CommonController extends BaseController
{

    public function loginStatus()
    {
        return wj_json_message('');
    }

    public function loginStart()
    {
        $redireUrl = \Request::instance()->get('redirectUrl');
        $proj = $this->getProject();
        \Cookie::queue('js:'.substr($proj->path, 1).':login', 1, 0, null, null, false, false);
        $redireUrl = empty($redireUrl) ? $proj->path : urldecode($redireUrl);
        return redirect($redireUrl);
    }

}