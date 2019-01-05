<?php
namespace App\Zhuanti\Prizes\Handler;


use App\Models\Project;

abstract class PrizesHandler
{
    /**
     * @var Project
     */
    protected $project;

    protected $playerInfo;

    protected $prizes;

    // status 1 中奖 0 未中奖 -1未抽奖 -2没抽奖资格
    const STATUS_WIN = 1;
    const STATUS_NOT_WIN = 0;
    const STATUS_NOT_DRAW = -1;
    const STATUS_NOT_PERMISSION = -2;

    public function __construct(Project $project, $playerInfo, $prizes)
    {
        $this->project = $project;
        $this->playerInfo = $playerInfo;
        $this->prizes = $prizes;
    }

    /**
     * @return array
     */
    abstract public function handle();

}