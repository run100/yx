<?php
/**
 * Created by PhpStorm.
 * User: staff
 * Date: 2018/8/27
 * Time: 上午9:08
 */

namespace App\Admin\Controllers;

use App\Models\Channel;
use App\Models\Project;
use App\Models\ProjectRepository;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Request;

class TongjiController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {
            $types = $this->typeTj();
            $typesing = $this->typeTj(true);
            $ret = [
                "all" => $types->sum("count"),
                "alling" => $typesing->sum("count"),
                "other" => isset($types[ProjectRepository::TYPE_OTHER]) ? $types[ProjectRepository::TYPE_OTHER]->count : 0,
                "othering" => isset($typesing[ProjectRepository::TYPE_OTHER]) ? $typesing[ProjectRepository::TYPE_OTHER]->count : 0,
                "vote" => isset($types[ProjectRepository::TYPE_VOTE]) ? $types[ProjectRepository::TYPE_VOTE]->count : 0,
                "voteing" => isset($typesing[ProjectRepository::TYPE_VOTE]) ? $typesing[ProjectRepository::TYPE_VOTE]->count : 0,
                "jizi" => isset($types[ProjectRepository::TYPE_JIZI]) ? $types[ProjectRepository::TYPE_JIZI]->count : 0,
                "jiziing" => isset($typesing[ProjectRepository::TYPE_JIZI]) ? $typesing[ProjectRepository::TYPE_JIZI]->count : 0,
                "cj" => isset($types[ProjectRepository::TYPE_CJ]) ? $types[ProjectRepository::TYPE_CJ]->count : 0,
                "cjing" => isset($typesing[ProjectRepository::TYPE_CJ]) ? $typesing[ProjectRepository::TYPE_CJ]->count : 0,
                "cut" => isset($types[ProjectRepository::TYPE_CUT]) ? $types[ProjectRepository::TYPE_CUT]->count : 0,
                "cuting" => isset($typesing[ProjectRepository::TYPE_CUT]) ? $typesing[ProjectRepository::TYPE_CUT]->count : 0,
                "branch" => wj_json_encode(Channel::repository()->getCacheChannel()->keyBy("name")->keys()->toArray()),
                "months" => wj_json_encode($this->getMonths()),
                "month_sjs" => wj_json_encode($this->monthTj())
            ];
            $content->header('专题统计');
            $content->row(view('admin::tongji', compact('ret')));
        });
    }

    private function typeTj($isIng = false)
    {
        $query = Project::selectRaw("count(id) as count, type")
            ->where("type", "!=", ProjectRepository::TYPE_CS);
        if ($isIng) {
            $now = date("Y-m-d H:i:s");
            $query->where("use_start_at", "<", $now);
            $query->where("use_end_at", ">", $now);
        }
        return $query->groupBy("type")->get()->keyBy("type");
    }

    public function getBranchTj(Request $request)
    {
        $type = (int)$request->get("type");
        $start = $request->get("start");
        $end = $request->get("end");
        if ($start) {
            $start = $start . " 00:00:00";
        }

        if ($end) {
            $end = $end . " 23:59:59";
        }

        if ($type) {
            $ret = $this->getTypeTime($type);
            $start = $ret["start"];
            $end = $ret["end"];
        }
        $query = Project::selectRaw("count(id) as count,channel_id");
        if ($start) {
            $query->where("created_at", ">", $start);
        }
        if ($end) {
            $query->where("created_at", "<", $end);
        }
        $res = $query->groupBy("channel_id")->get()->keyBy("channel_id");

        $channels = Channel::repository()->getCacheChannel();
        return wj_json_encode($channels->map(function ($item) use ($res) {
            $value = isset($res[$item['id']]) ? $res[$item['id']]->count : 0;
            return ["value" => $value, "name" => $item['name']];
        })->toArray());
    }

    private function getTypeTime($type)
    {
        $end = date("Y-m-d H:i:s");
        if ($type == 1) {
            $start = date("Y-m-d H:i:s", strtotime("-1 month"));
        }
        if ($type == 2) {
            $start = date("Y-m-d H:i:s", strtotime("-6 month"));
        }
        return ["start" => $start, "end" => $end];
    }

    private function getMonths()
    {
        $month = date("Y/m");
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = date("Y/m", strtotime("-" . $i . " month"));
        }
        return $months;
    }

    private function monthTj()
    {
        $end = date("Y-m-d", strtotime("-11 month")) . " 00:00:00";
        $entitys = Project::selectRaw("id, type, created_at")
            ->where("type", "!=", ProjectRepository::TYPE_CS)
            ->where("created_at", ">", $end)->get();
        $sjs = [];
        for ($i = 11; $i >= 0; $i--) {
            $st = strtotime(date("Y-m", strtotime("-" . $i . " month")) . "-01 00:00:00");
            $ed = strtotime(date("Y-m", strtotime("-" . ($i - 1) . " month")) . "-01 00:00:00");
            $sums = $entitys->filter(function ($item) use ($st, $ed) {
                $timespan = strtotime($item->created_at);
                return (($timespan > $st) && ($timespan < $ed));
            });
            $sjs[] = [
                $sums->where("type", "=", ProjectRepository::TYPE_OTHER)->count(),
                $sums->where("type", "=", ProjectRepository::TYPE_VOTE)->count(),
                $sums->where("type", "=", ProjectRepository::TYPE_JIZI)->count(),
                $sums->where("type", "=", ProjectRepository::TYPE_CJ)->count(),
                $sums->where("type", "=", ProjectRepository::TYPE_CUT)->count()

            ];
        }
        return $sjs;


    }


}