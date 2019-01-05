<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/5/23
 * Time: 上午9:02
 */

namespace App\Http\Controllers\Web;



use App\Models\Merchant;
use Barryvdh\Debugbar\Controllers\BaseController;
use Illuminate\Http\Request;

class SiteController extends BaseController
{
    public function index(Request $request)
    {
        return redirect()->to('http://365jia.cn');
    }
}