<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2018/3/5
 * Time: 下午4:46
 */

namespace App\Jobs;


use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Wanjia\Common\Job\AutoDelay;

class PaymentJob implements ShouldQueue
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
        try {
            $method = "onAct" . ucfirst(Str::camel($this->act));
            if (method_exists($this, $method)) {
                $this->$method(... $this->params);
            }
        } catch (\Exception $e) {
            \Log::error($e);
        }
    }

    public function onActCloseTrade($trade_no)
    {
        $pay = Payment::repository()->findOneByTradeNo($trade_no);
        if (!$pay) {
            return;
        }

        try {
            $pay->merchant->wechat_app->payment->close($trade_no);
        } catch (\Throwable $ex) {
            \Log::error($ex);
            $this->release($this->autoDelays());
        }
    }

}
