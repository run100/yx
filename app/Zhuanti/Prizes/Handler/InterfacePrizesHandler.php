<?php
namespace App\Zhuanti\Prizes\Handler;



use Illuminate\Support\Str;

class InterfacePrizesHandler extends PrizesHandler
{

    public function handle()
    {
        $params = '';
        $peizhi = $this->prizes->peizhi;
        empty($peizhi) && $peihzi =  'common';
        if (empty($peizhi)) {
            $peizhi = 'common';
        } elseif (Str::contains($peizhi, ':')) {
            $index = strpos($peizhi, ':');
            $params = Str::substr($peizhi, $index+1);
            $peizhi = Str::substr($peizhi, 0, $index);
            if (Str::contains($params, ',')) {
                $params = explode(',', $params);
            }
        }
        $method = 'act'.ucfirst(Str::camel($peizhi));
        if (method_exists($this, $method)) {
            return $this->$method($params);
        } else {
            \Log::error('InterfacePrizesHandler@'.$method.' Method Not Exist! Please Check Peizhi');
        }
    }

    /**
     * 与 CommonPrizesHandler 效果一样 兑奖码奖品
     * @param string|array $params
     * @return array
     */
    public function actCommon($params)
    {
        $commonHandel = new CommonPrizesHandler($this->project, $this->playerInfo, $this->prizes);
        return $commonHandel->handle();
    }

}