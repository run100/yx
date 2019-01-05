<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/7/28
 * Time: 下午2:57
 */

namespace App\Http\Controllers\Web;


use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AutoTemplateController extends BaseController
{
    public function templatePage(Request $request)
    {
        $route = \Route::getCurrentRoute();
        $filename = @$route->action['__auto_template'];
        $base = @$route->action['__auto_template_base'];
        $action = @$route->action['__auto_template_data_action'];

        if (!$filename) {
            abort(404);
        }


        $filename = realpath($filename);

        //目录安全性检查
        if (strpos($filename, realpath(public_path('web'))) !== 0) {
            abort(404);
        }

        //扩展名检查
        if (pathinfo($filename, PATHINFO_EXTENSION) !== 'html') {
            abort(404);
        }

        $name = substr($filename, strlen((public_path('web/'))), -5);

        $name = 'web::' . str_replace('/', '.', $name);

        if ($action) {
            $action = explode('@', $action);
            $data = app($action[0])->{$action[1]}($request);
        } else {
            $data = [];
        }

        if (is_object($data) && ($data instanceof Response || $data instanceof \Symfony\Component\HttpFoundation\Response)) {
            return $data;
        }

        $content = view($name, $data);
        $content = preg_replace('@<head[^<>]*>@', "\$0\n    <base href=\"$base\"/>", $content);
        $content = preg_replace('@<meta *name="wanjia-perms".+?/>@i', '', $content, 1);
        $content = preg_replace('@<meta *name="wanjia-data".+?/>@i', '', $content, 1);

        //头部的base标签会影响前端锚点的基地址，在此处通过JS修正
        $js_fix_hash = <<<eot
<script type="text/javascript">
(function(helper) {
    document.getElementsByTagName('body')[0].onclick = function(e) {
        var target = null;
        if(e.path){
            for (var i = 0; i < e.path.length; i++) {
                if (e.path[i].tagName === 'A') {
                    target = e.path[i];
                    break;
                }
            }
        }
        
        if (!target) {
            return;
        }
        
        var link = target.getAttribute('href');
        if (/^#.+/.test(link)) {
            e.preventDefault();
            location.hash = link;
        }
    };
})();
</script>
eot;

        $content = preg_replace('@</body>@i', $js_fix_hash . '</body>', $content, 1);

        return response($content);
    }
}
