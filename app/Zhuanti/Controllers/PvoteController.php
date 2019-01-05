<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/11/20
 * Time: 下午9:04
 */

namespace App\Zhuanti\Controllers;

use App\Exceptions\UploadsStorageException;
use App\Lib\SiteUtils;
use App\Models\Player;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Wanjia\Common\Database\Limiter;

class PvoteController extends \App\Http\Controllers\BaseController
{
    const PAGE_SIZE = 20;

    public function index(Request $request)
    {
        $proj = $this->getProject();

        $rawOrderBy = $request->get('orderby');
        $defaultOrder = isset($proj->configs->vote->default_orderby) ? $proj->configs->vote->default_orderby : 'id';
        $orderBtns = ['id'=>'按编号', 'vote'=>'按票数'];
        $defaultOrder == 'vote' && $orderBtns = ['vote'=>'按票数','id'=>'按编号'];
        $rawOrderBy === null && $rawOrderBy = $defaultOrder;
        if ($rawOrderBy === 'vote') {
            $orderby = ['vote1 desc', 'id desc'];
        } else {
            $orderby = 'id desc';
        }
        $q = [];
        $q['checked'] = 1;
        if ($keyword = trim($request->get('keyword'))) {
            $searchField = '';
            foreach ($proj->configs->base_form_design as $v) {
                if ($v->field == 'name') {
                    $searchField = $v->key;
                    break;
                }
            }
            $regKeyword = substr($keyword, strlen($proj->configs->vote->vote_pre));
            if (is_numeric($regKeyword)) {
                $keyword = $regKeyword;
            }
            $q[] = Limiter::make(function (Builder $qb) use ($keyword, $searchField) {
                if (is_numeric($keyword)) {
                    $qb->where('ticket_no', '=', $keyword);
                } else {
                    $qb->orWhere($searchField, '=', $keyword);
                }
            }, Limiter::CALLBACK);
        }

        $pager = Player::repository()->getPagerByProjectId($proj->id, $q, [
            'page' => $request->get('page'),
            'size' => self::PAGE_SIZE,
            'orderby' => $orderby,
            'checked' => 1
        ]);
        $players = $pager->items();

        //允许的表单字段
        $forms = $proj->configs->base_form_design;
        $myform = $this->getFormByType($forms, 'indexs');

        $players_pre = isset($proj->configs->vote->vote_pre) ? $proj->configs->vote->vote_pre : '';
        return view('zhuanti::public_vote/index', compact('proj', 'players', 'pager', 'rawOrderBy', 'myform', 'players_pre', 'orderBtns'));
    }

    public function detail(Request $request)
    {
        $proj = $this->getProject();

        $player_id = $request->input('id');
        $proj = $this->getProject();

        $player = Player::repository()->findOneByProjectId($proj->id, ['ticket_no' => $player_id]);

        abort_if(!$player, 404);

        $conn = \DB::connection();
        $conn->select("set @curRank := 0");
        $ranks = $conn->selectOne("
            select rownum from 
            (select @curRank := @curRank +1 as rownum,ticket_no from zt_player  WHERE project_id = ? ORDER BY vote1 DESC, id ASC)
             as T1 where T1.ticket_no= ?
        ", [$proj->id , $player_id]);

        //允许的表单字段
        $forms = $proj->configs->base_form_design;
        $myform = $this->getFormByType($forms, 'details');
        $players_pre = isset($proj->configs->vote->vote_pre) ? $proj->configs->vote->vote_pre : '';
        return view('zhuanti::public_vote/detail', compact('proj', 'player', 'myform', 'ranks', 'players_pre'));
    }

    public function rank(Request $request)
    {
        $proj = $this->getProject();
        $myform = $proj->configs->base_form_design;

        $q = [];
        $q['checked'] = 1;
        if ($keyword = trim($request->get('keyword'))) {
            $q[] = Limiter::make(function (Builder $qb) use ($keyword) {
                $qb->where('ticket_no', '=', $keyword);
                $qb->orWhere('str1', '=', $keyword);
            }, Limiter::CALLBACK);
        }

        $pager = Player::repository()->getPagerByProjectId($proj->id, $q, [
            'page' => $request->get('page'),
            'size' => self::PAGE_SIZE,
            'orderby' => ['vote1 desc', 'id asc']
        ]);
        $players = $pager->items();

        $conn = \DB::connection();
        $conn->select("set @curRank := 0");
        $ranks = $conn->select("
            SELECT id, @curRank := @curRank + 1 AS rank
            FROM zt_player
            WHERE project_id = ?
                AND checked = 1
            ORDER BY vote1 DESC, id ASC
        ", [$proj->id]);
        $ranks = collect($ranks)->pluck('rank', 'id')->all();

        return view('zhuanti::public_vote/rank', compact('proj', 'players', 'pager', 'ranks', 'myform'));
    }

    public function register(Request $request) {
        $proj = $this->getProject();

        $forms = $proj->configs->base_form_design;

        $myform = $this->getFormByType($forms);

        //检测活动时效性
        if (isset($proj->configs->baoming->starttime) && (strtotime($proj->configs->baoming->starttime) > time())) {
            return response()->json(['msg' => '活动报名暂未开始,活动开始时间为：'.$proj->configs->baoming->starttime, 'code' => 1]);
        }

        if (isset($proj->configs->baoming->endtime) && (strtotime($proj->configs->baoming->endtime) <= time())) {
            return response()->json(['msg' => '活动报名已结束', 'code' => 1]);
        }
        $search = $proj->id == 123 ? ['str3' => $request->get('info_phone')] : ['uniqid' => wx_openid()];
        $exists = Player::repository()->countByProjectId($proj->id, $search);
        if ($exists) {
            $err = '每个'.($proj->id == 123 ? '手机号' :'微信号').'只能报一次，您已经报过名了!';
            return response()->json(['msg' => $err, 'code' => 1]);
        }

        $result = [];
        foreach ($myform as $form) {
            $field_name = 'info_'.$form->field;
            if ($form->type == 'openid') {
                $result[$field_name] = wx_openid();
            } elseif ($form->type == 'upload') {
                $picfile = $request->file($field_name);
                if (@$form->required === 'on') {
                    if (empty($picfile)) {
                        return response()->json(['msg' => $form->comment?:'请上传'.$form->name.'!', 'code' => 1]);
                    }
                }

                if ($picfile) {
                    try {
                        $result[$field_name] = move_to_uploads($picfile, [
                            'size' => 3 * 1024 * 1024,
                            'ext' => ['jpg', 'png', 'jpeg'],
                            'mime' => ['image/*']
                        ]);
                    } catch (UploadsStorageException $ex) {
                        if ($ex->getCode() === UploadsStorageException::CODE_SIZE_LIMITED) {
                            $err = '照片尺寸超限，需在3M 以内!';
                        } elseif ($ex->getCode() === UploadsStorageException::CODE_EXT_LIMITED) {
                            $err = '请上传正确的图片文件格式(jpg/png)!';
                        } elseif ($ex->getCode() === UploadsStorageException::CODE_MIME_LIMITED) {
                            $err = '请上传正确的图片文件格式(jpg/png)!';
                        } else {
                            $err = '图片保存失败!';
                        }
                        return response()->json(['msg' => $err, 'code' => 1]);
                    } catch (\Exception $e) {
                        return response()->json(['msg' => $e->getMessage(), 'code' => 1]);
                    }
                }
            } else {
                $req_val = $request->get($field_name);
                if (@$form->required === 'on') {
                    //现在只做基础判断
                    if (empty($req_val)) {
                        return response()->json(['msg' => $form->comment?:$form->name.'不能为空!', 'code' => 1]);
                    }
                }
                if ($form->type == 'phone') {
                    if (!SiteUtils::isPhone($req_val) && !preg_match("/^\d{4}\-?\d{7,8}$/", $req_val)) {
                        return response()->json(['msg' => '请输入手机号码或座机号码!', 'code' => 1]);
                    }
                }
                $result[$field_name] = $req_val;
            }
        }

        $player = new Player();
        $player->project()->associate($proj);
        $player->merchant()->associate($proj->merchant);
        $player->fill($result);
        $player->save();

        return response()->json(['msg' => '报名成功,小编正在玩命审核中', 'code' => 0]);
    }

    public function reg(Request $request)
    {
        $proj = $this->getProject();

        $forms = $proj->configs->base_form_design;

        $myform = $this->getFormByType($forms);

        return view('zhuanti::public_vote/reg', compact('proj', 'myform'));
    }

    public function rule(Request $request)
    {
        $proj = $this->getProject();

        return view('zhuanti::public_vote/rule', compact('proj'));
    }

    /**
     * 获取表单的类别
     * @inheritdoc 此功能暂只用来显示对应字段,后期拓展
     * @author:chenfei
     */
    private function getFormByType($forms = [], $type = 'registration')
    {
        if (empty($forms)) {
            return [];
        }

        $return = [];
        foreach ($forms as $vv) {
            $mytype = isset($vv->$type) ? $vv->$type : '';
            if ($mytype == 'on') {
                $return[] = $vv;
            }
        }
        return $return;
    }
}