<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/11/22
 * Time: 上午9:18
 */

namespace App\Exceptions;


use Throwable;

class RedirectMessageException extends \Exception
{
    protected $url;

    protected $delay;

    public function __construct($msg, $url, $delay = 3)
    {
        parent::__construct($msg, 0, null);

        $this->url = $url;
        $this->delay = $delay;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return int
     */
    public function getDelay(): int
    {
        return $this->delay;
    }

    /**
     * @param int $delay
     * @return $this
     */
    public function setDelay(int $delay)
    {
        $this->delay = $delay;
        return $this;
    }


}