<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/11/20
 * Time: 下午9:04
 */

namespace App\Http\Controllers\Web;


use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class TestController extends BaseController
{
    public function mobile(Request $request)
    {
        return view('zhuanti::examples/mobile');
    }

    public function clean(Request $request)
    {
        return view('zhuanti::examples/clean');
    }

    public function pc(Request $request)
    {
        return view('zhuanti::examples/pc');
    }

    public function weui(Request $request)
    {
        return view('zhuanti::examples/weui');
    }

    public function bootstrap(Request $request)
    {
        return view('zhuanti::examples/bootstrap');
    }
}