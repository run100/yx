<?php

namespace App\Zhuanti\Prizes;


use App\Http\Controllers\BaseController;
use App\Zhuanti\Common\TPrizes;

class PrizesController extends BaseController
{
    use TPrizes;

    /**
     * 分享页
     */
    public function share()
    {
        $proj = $this->getProject();
        if ($proj->configs->draw->is_attention != 'Y') {
            abort(404);
        }
        $friendid = $this->getRouteParam('player_id');
        if (empty($friendid)) {
            abort(404);
        }
        $this->assign['player'] = PrizesOperator::instance($this->getProject()->id)->getPlayer($friendid, false);
        if (empty($this->assign['player'])) {
            abort(404);
        }
        $this->assign['playerKey'] = $friendid;
        session(['friendid' => $friendid]);
        return $this->render();
    }

    /**
     * 更多助力记录
     */
    public function zhulis()
    {
        $page = (int)\Request::instance()->get('page');
        $proj = $this->getProject();
        $data = [];
        if ($proj->configs->draw->is_zhuli != 'Y') {
            return wj_json_message($data);
        }
        $prizesOperator = PrizesOperator::instance($this->getProject()->id);
        $ticketNo = $prizesOperator->getPlayer(wx_openid(), true, 'ticket_no');
        if ($page > 0 && $ticketNo) {
            $data = $prizesOperator->getPlayerZhulis($ticketNo, $page);
        }
        return wj_json_message($data);
    }

    public function rule()
    {
        return $this->render('zhuanti::public_prizes/rule');
    }

}