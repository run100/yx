<?php

namespace App\Zhuanti\WechatLogics;


use App\Zhuanti\WechatLogic;
use EasyWeChat\Message\Image;


class Material extends WechatLogic
{

    protected $mediaId;

    public function __construct($mediaId)
    {
        $this->mediaId = trim($mediaId);
    }

    /**
     * @param \SimpleXMLElement $msg
     * @return mixed
     */
    public function handle($msg)
    {
        return new Image(['media_id' => $this->mediaId]);
    }


}