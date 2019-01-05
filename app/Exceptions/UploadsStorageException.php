<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/12/15
 * Time: 下午1:03
 */

namespace App\Exceptions;

class UploadsStorageException extends \Exception
{
    const CODE_SIZE_LIMITED = 1;
    const CODE_EXT_LIMITED = 2;
    const CODE_MIME_LIMITED = 3;

}