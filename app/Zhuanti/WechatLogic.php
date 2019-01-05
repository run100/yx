<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/11/7
 * Time: 下午7:48
 */

namespace App\Zhuanti;


use App\Models\Merchant;

abstract class WechatLogic
{

    /**
     * @var Merchant
     */
    protected $merchant;

    /**
     * @param Merchant $merchant
     */
    public function setMerchant(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }


    /**
     * @param \SimpleXMLElement $msg
     * @return mixed
     */
    abstract public function handle($msg);

    public function getMatchResult($msg)
    {
        return $msg->__wj_match_result;
    }
}