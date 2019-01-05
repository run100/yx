<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2018/3/5
 * Time: 下午4:46
 */

namespace App\Jobs;


use App\Models\Merchant;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Wanjia\Common\Job\AutoDelay;

class MerchantJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, AutoDelay;

    protected $act;
    protected $params;

    public function __construct($act, ... $params)
    {
        $this->act = $act;
        $this->params = $params;
    }

    public function handle()
    {
        $method = "onAct" . ucfirst(Str::camel($this->act));
        if (method_exists($this, $method)) {
            $this->$method(... $this->params);
        }
    }

    public function onActSyncInfo($id)
    {
        $merchant = Merchant::repository()->retrieveByPK($id);
        if ($merchant) {
            try {
                $merchant->refreshAuthorizerInfo(false);
                $merchant->pre_auth_code = null;
                $merchant->pre_auth_code_expire_at = null;
                $merchant->save();
            } catch (\Throwable $ex) {
                \Log::error($ex);
                $this->release($this->autoDelays());
            }
        }
    }

}
