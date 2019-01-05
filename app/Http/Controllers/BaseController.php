<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/10/23
 * Time: 下午9:48
 */

namespace App\Http\Controllers;


use App\Models\Project;
use Wanjia\Common\Exceptions\AppException;

class BaseController extends \Illuminate\Routing\Controller
{


    protected $viewNamespace = 'zhuanti';

    protected $assign = [];

    /**
     * @return Project
     */
    public function getProject()
    {
        return \Request::instance()->attributes->get('project');
    }


    /**
     * 获取路由的参数
     * @param $field string
     * @param null $default
     * @return object|string
     */
    public function getRouteParam($field, $default = null)
    {
        return \Request::route()->parameter($field, $default);
    }

    /**
     * 渲染 view
     * @param $viewUrl string|array
     * @param $assign array
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function render($viewUrl = '', $assign = [])
    {
        if (is_array($viewUrl)) {
            $assign = $viewUrl;
            $viewUrl = '';
        }
        if ($viewUrl === '') {
            $controllerAction = explode('@', \Request::route()->action['controller']);
            $className = strtolower(
                str_replace('Controller', '', substr($controllerAction[0], strripos($controllerAction[0], '\\')+1))
            );
            $viewUrl = $this->viewNamespace.'::public_'.$className.'/'.$controllerAction[1];
        }
        !empty($assign) && $this->assign = array_merge($this->assign, $assign);
        !isset($this->assign['proj']) && $this->assign['proj'] = $this->getProject();
        !isset($this->assign['wxshare']) && $this->setWxShareData();
        return view($viewUrl, $this->assign);
    }

    /**
     * 抛出一个APPException由Handler处理
     * @param string|array $msg
     * @param int $code
     * @throws AppException
     */
    protected function fail($msg = '', $code = 1)
    {
        throw new AppException($msg, $code);
    }

    /**
     * 设置微信分享的相关参数
     * @param null $link
     */
    protected function setWxShareData($link=null)
    {
        $proj = $this->getProject();
        $this->assign['wxshare'] = [
            'title' => $proj->configs->share_title,
            'share' => $proj->configs->share_desc,
            'link' => !empty($link) ? $link : $proj->path,
            'img' => isset($proj->configs->share_image) ? '/uploads/'.$proj->configs->share_image : '',
            'url' => route('wechat.jssdk_config_v2', [], false)
        ];
    }

    /**
     * 用于定制专题的数据合并
     * @param array $data
     * @return array
     */
    protected function mergeAssign($data = [])
    {
        $this->assign['proj'] = $this->getProject();
        $this->setWxShareData();
        !empty($data) && $this->assign = array_merge($this->assign, $data);
        return $this->assign;
    }

}