<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/10/26
 * Time: 下午5:02
 */

namespace App\Http\Response;


use Illuminate\Http\Response;

class RedirectMessageResponse extends Response
{
    protected $delay = 0;
    protected $title;
    protected $msg;
    protected $url;
    protected $is_alert = false;
    protected $is_confirm = false;

    public function __construct($msg = '', $status = 200, array $headers = array())
    {
        $this->msg = $msg;

        parent::__construct('', $status, $headers);
    }

    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    public function to($url)
    {
        $this->url = $url;
        return $this;
    }

    public function delay($delay)
    {
        $this->delay = $delay;
        return $this;
    }

    public function with_alert()
    {
        $this->is_alert = true;
        $this->delay = -1;
        return $this;
    }

    public function with_confirm()
    {
        $this->is_confirm = true;
        $this->delay = -1;
        return $this;
    }

    public function sendContent()
    {
        echo view('redirect_message', [
            'title'     => $this->title,
            'msg'       => $this->msg,
            'url'       => $this->url,
            'delay'     => $this->delay,
            'is_alert'  => $this->is_alert,
            'is_confirm'=> $this->is_confirm
        ]);
        return $this;
    }
}