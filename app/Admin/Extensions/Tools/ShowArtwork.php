<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class ShowArtwork extends AbstractTool
{
    protected  $url;
    protected  $icon;
    function __construct($url,$icon,$text)
    {
        $this->url = $url;
        $this->icon = $icon;
        $this->text = $text;
    }

    public function render()
    {
        $url = $this->url;
        $icon = $this->icon;
        $text = $this->text;

        return <<<EOT
<div class="btn">
    <a class="btn btn-sm btn-twitter  pull-right" href="{$url}" target="_blank"><i class="fa {$icon}"></i> {$text}</a>
</div>
EOT;
    }
}