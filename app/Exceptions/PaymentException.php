<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/12/15
 * Time: 下午1:03
 */

namespace App\Exceptions;

use Throwable;

class PaymentException extends \Exception
{
    const CODE_NO_TRADE = 1;
    const CODE_TRADE_PROCESSED = 2;
    const CODE_ERR_FEE = 3;

    protected $notify;

    /**
     * @return \stdClass
     */
    public function getNotify()
    {
        return $this->notify;
    }

    public function __construct(string $message = "", int $code = 0, $notify = null, Throwable $previous = null)
    {
        $this->notify = $notify;

        parent::__construct($message, $code, $previous);
    }


}