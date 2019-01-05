<?php

namespace App\Exceptions;



use Throwable;

class LogException extends \Exception
{

    private $projectId;

    public function __construct($message, $code, $projectId = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->projectId = $projectId;
    }

    /**
     * 获取集字专题项目的 ProjectId
     * @return int
     */
    public function getProjectId()
    {
        return (int)$this->projectId;
    }

}