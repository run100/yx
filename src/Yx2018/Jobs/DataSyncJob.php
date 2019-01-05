<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2018/3/3
 * Time: 下午4:38
 */

namespace App\Features\Yx2018\Jobs;


use App\Features\Yx2018\ServiceProvider;
use App\Models\Player;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Wanjia\Common\Job\AutoDelay;
use App\Features\Yx2018\Controllers\Controller;

class DataSyncJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, AutoDelay;

    protected $act;
    protected $params;
    /** @var Project */
    protected $project;

    public function __construct($act, ... $params)
    {
        $this->act = $act;
        $this->params = $params;
    }

    public function onActAddLine($msg)
    {
        \Log::error($msg);
    }


    /**
     * @param Project $project
     */
    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    /**
     * 任务处理的入口在这里
     */
    public function handle()
    {
        $method = "onAct" . ucfirst(Str::camel($this->act));
        if (method_exists($this, $method)) {
            $this->$method(... $this->params);
        }
    }

    /**
     * Redis中的选手数据转存到数据库
     */
    public function onActPlayerToDb($phones, $project_id, $merchant_id)
    {
        $lua = Controller::getLuaRedis();

        foreach ($phones as $phone) {
            $info = $lua->getPlayerAndParse($phone);

            $player = new Player();
            $player->project_id = $project_id;
            $player->merchant_id = $merchant_id;
            $player->info = $info;
            //本次数据库操作不发广播事件; ServiceProvider中监听了数据库修改事件，发广播会导致DB中的数据反流到Redis中，如果有执行时间差，可能会导致数据不一致，甚至丢数据
            $player->enableBroadcast(false);
            $player->save();
        }
    }

    /**
     * 数据库的选手数据更新到Redis: 对应后台新增选手
     */
    public function onActPlayerToRedis($playerid)
    {
        $player = Player::repository()->retrieveByPK($playerid);

        $lua = Controller::getLuaRedis();
        $info = $player->info;
        $info->ticket_no = $player->ticket_no;
        $lua->savePlayer(wj_json_encode($info));
    }

    /**
     * 数据库的选手数据更新到Redis: 对应后台更改选手
     */
    public function onActPlayerUptoRedis($phone, $playerid)
    {
        $player = Player::repository()->retrieveByPK($playerid);
        $lua = Controller::getLuaRedis();
        $info = $player->info;
        $info->ticket_no = $player->ticket_no;
        $info->checked = $player->checked;
        $lua->removePlayer($phone);
        $lua->savePlayer(wj_json_encode($info));
    }

    /**
     * 制作推广海报
     */
    public function onActMakeCover($threadid, $nickname, $headurl)
    {
        try {
            $this->makeCover($threadid, $nickname, $headurl);
        } catch (\Throwable $ex) {
            \Log::error($ex);

            //出错后延时重试; autoDelays中定义了根据重试次数选择不同的延迟时间
            $this->release($this->autoDelays());
        }
    }

    /**
     * 制作推广海报
     */
    public function onActMakeHonor($threadid, $nickname, $headurl)
    {
        try {
            $this->makeHonor($threadid, $nickname, $headurl);
        } catch (\Throwable $ex) {
            \Log::error($ex);

            //出错后延时重试; autoDelays中定义了根据重试次数选择不同的延迟时间
            $this->release($this->autoDelays());
        }
    }

    /**
     * 制作捐助证书
     */
    public function onActMakeCert($threadid, $name)
    {
        try {
            $this->makeCert($threadid, $name);
        } catch (\Throwable $ex) {
            \Log::error($ex);
            $this->release($this->autoDelays());
        }
    }

    /**
     * 制作推广海报过程
     */
    public function makeCover($threadid, $nickname, $headurl)
    {
        $path = uploads_path("yx2018/covers/$threadid.png");
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        //创建临时文件用于存储二维码
        $tmpfile = tempnam('/tmp', 'ZT');

        //制作二维码并写入临时文件
        \QrCode::format('png')
            ->size(202)
            ->margin(0)
            ->generate(
                route('yx2018.start', ['from'   => "cover:$threadid", 'act' => 'vote', 'threadid' => $threadid]),
                $tmpfile
            );

        //读取二维码图片
        $image_qr = new \Imagick($tmpfile);
        @unlink($tmpfile);

        if ($headurl) {
            $ch = curl_init($headurl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $head = curl_exec($ch);
            if (curl_errno($ch)) {
                $ex = new \Exception(curl_error($ch), curl_errno($ch));
                curl_close($ch);
                @unlink($tmpfile);
                throw $ex;
            }
            curl_close($ch);
        } else {
            $head = file_get_contents(public_path('web/yx2018/images/ico_default_avatar.jpg'));
        }
        file_put_contents($tmpfile, $head);

        //读取选手图片
        $image_pic = new \Imagick($tmpfile);
        //缩放后中心截取方式的缩略图
        $image_pic->cropThumbnailImage(96, 96);
        //做圆角; 有可能部分环境下的Imagick不支持圆角，如Mac系统上就不支持
        if (method_exists($image_pic, 'roundCorners')) {
            $image_pic->roundCorners(49, 49);
        }
        @unlink($tmpfile);


        //读取背景
        $image = new \Imagick(public_path('web/yx2018/images/tmp_cover.png'));

        //贴二维码
        $image->compositeImage($image_qr, \Imagick::COMPOSITE_OVER, 219, 751);

        //贴选手头像
        $image->compositeImage(
            $image_pic,
            \Imagick::COMPOSITE_OVER,
            222 + floor((98 - $image_pic->getImageWidth()) / 2),
            572 + floor((98 - $image_pic->getImageHeight()) / 2)
        );

        /**
         * 贴字: 算文字位置(PHP端做自动换行)
         * 有多种算法，用imagettfbbox计算字符像素宽度最准确，但是要一个字符串一个字符算，效率比较低。
         * 下面这种算法，是通过字符串宽度计算得到一个大致准确的结果。
         * 关于字符的几个概念:
         * 1. 字符编码: 针对汉字等多字节在内存中如何存储的问题；汉字有多种编码方式，如GBK用固定的2个字节表示一个汉字，而UTF8是可变长度的编码单个字符的字节数是1~6
         *      大部分汉字在UTF8编码下占用3个字节，相关资料:
         *      https://baike.baidu.com/item/%E7%BC%96%E7%A0%81/80092
         *      https://baike.baidu.com/item/UTF-8/481798
         * 2. 字节数: 字符串在内存中占用的空间; ABC123等是单字节，即占用1个字节的内存，汉字是多字节；如: "哈哈haha" 在UTF8下字节数是10，在GBK下字节数是8
         * 3. 字符串长度: 指字符串中有多少个完整的字符，如： "哈哈haha" 长度为6
         * 4. 字符串宽度: 针对于大部分的等宽字体(也有非等宽字体每个字符宽度都不同)，汉字被设计为两个英文字符宽度，所以汉字宽度为2，英文字符宽度为1;
         *      加起来就是字符串宽度，如: "哈哈haha" 宽度为8
         * 5. 字符串像素宽度: 指字符串显示在显示器上的时候所占的像素宽度; 这跟 字号 和 字体 有关
         *
         * PHP中:
         * 字符串      = 内存中的字节序列; 字符串的默认编码就是PHP程序文件的编码方式，字符串变量本身不包含自己的编码信息，当然只有一种编码是正确的
         *              被当成错误的编码解析出来就是常规意义上的乱码; 所以字符串乱码并不是数据出了问题，而是数据被当成错误的编码方式来处理了
         * 字节数      = strlen         http://php.net/manual/zh/function.strlen.php
         * 字符串长度   = mb_strlen      http://php.net/manual/zh/function.mb-strlen.php
         * 字符串宽度   = mb_strwidth    http://php.net/manual/zh/function.mb-strwidth.php
         * 字符串像素宽度 = imagettfbbox http://php.net/manual/zh/function.imagettfbbox.php
         *
         * 以下算法的过程就是，已知某区域每行能显示的字符串宽度为X;
         * 利用 mb_strimwidth 每次截取 X 宽度的字符串并与换行符连接
         * http://php.net/manual/zh/function.mb-strimwidth.php
         */
        $text_w = mb_strlen($nickname);
        $text = '';
        $pos = 0;
        $lines = 0;
        while ($pos < $text_w) {
            $lines ++;
            $line = mb_strimwidth($nickname, $pos, 16);
            $text .= $line . PHP_EOL . " ";
            $pos += mb_strlen($line);

            //限制两行
            if ($lines >= 2) {
                break;
            }
        }
        $nickname = trim($text);

        //垂直定位
        $y = 582;
        if ($lines <= 1) {
            $y += 20;
        }

        $image_text = new \ImagickDraw();
        $image_text->setStrokeWidth(1);
        $image_text->setTextEncoding('UTF-8');
        $image_text->setGravity(\Imagick::GRAVITY_NORTHWEST);
        $image_text->setFontSize(28);
        $image_text->setFillColor('#ef3c00');
        $image_text->setFont(resource_path('DroidSansFallback.ttf'));
        $image_text->annotation(325, $y, sprintf(" %s ", $nickname));
        $image->drawImage($image_text);

        //生成的图片写入文件系统
        $image->writeImage($path);
    }



    /**
     * 制作毅行称号
     */
    public function makeHonor($threadid, $nickname, $headurl)
    {
        static $bgs = [];

        $path = uploads_path("yx2018/covers/honor_$threadid.jpg");
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        //创建临时文件用于存储二维码
        $tmpfile = tempnam('/tmp', 'ZT');

        if ($headurl) {
            $ch = curl_init($headurl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $head = curl_exec($ch);
            if (curl_errno($ch)) {
                $ex = new \Exception(curl_error($ch), curl_errno($ch));
                curl_close($ch);
                @unlink($tmpfile);
                throw $ex;
            }
            curl_close($ch);
        } else {
            $head = file_get_contents(public_path('web/yx2018/images/ico_default_avatar.jpg'));
        }
        file_put_contents($tmpfile, $head);

        //读取选手图片
        $image_pic = new \Imagick($tmpfile);
        //缩放后中心截取方式的缩略图
        $image_pic->cropThumbnailImage(92, 92);
        @unlink($tmpfile);


        $honor = rand(1, 5);
        $image = @$bgs[$honor];

        if (!$image) {
            //读取背景
            $image = new \Imagick(public_path("web/yx2018/images/tmp_honor_$honor.png"));
            $bgs[$honor] = $image;
        }

        $image = clone $image;

        //贴选手头像
        $image->compositeImage(
            $image_pic,
            \Imagick::COMPOSITE_OVER,
            91 + floor((98 - $image_pic->getImageWidth()) / 2),
            285 + floor((98 - $image_pic->getImageHeight()) / 2)
        );

        $image_text = new \ImagickDraw();
        $image_text->setStrokeWidth(1);
        $image_text->setTextEncoding('UTF-8');
        $image_text->setGravity(\Imagick::GRAVITY_NORTHWEST);
        $image_text->setFontSize(22);
        $image_text->setFillColor('#3c4930');
        $image_text->setFont(resource_path('DroidSansFallback.ttf'));
        $image_text->annotation(80, 386, sprintf(" %s ", mb_strimwidth($nickname, 0, 18, '...')));
        $image->drawImage($image_text);

        //生成的图片写入文件系统
        $image->setCompressionQuality(60);
        $image->writeImage($path);
    }

    /**
     * 制作推广捐助证书的过程
     * 同海报，只不过背景不同以及不用贴头像
     */
    public function makeCert($threadid, $name)
    {
        $path = uploads_path("yx2018/covers/cert_$threadid.png");
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        //创建临时文件用于存储二维码
        $tmpfile = tempnam('/tmp', 'ZT');

        //制作二维码并写入临时文件
        \QrCode::format('png')
            ->size(131)
            ->margin(0)
            ->generate(
                route('yx2018.start', ['from'   => "cert:$threadid", 'act' => 'vote', 'threadid' => $threadid]),
                $tmpfile
            );

        //读取二维码图片
        $image_qr = new \Imagick($tmpfile);
        @unlink($tmpfile);

        //读取背景
        $image = new \Imagick(public_path('web/yx2018/images/voyager_certificate_img.png'));

        //贴二维码
        $image->compositeImage($image_qr, \Imagick::COMPOSITE_OVER, 129, 702);

        //贴字
        $image_text = new \ImagickDraw();
        $image_text->setStrokeWidth(1);
        $image_text->setTextEncoding('UTF-8');
        $image_text->setGravity(\Imagick::GRAVITY_NORTH);
        $image_text->setFontSize(32);
        $image_text->setFillColor('#b71c22');
        $image_text->setFont(resource_path('DroidSansFallback.ttf'));
        $image_text->annotation(0, 510, sprintf(" %s ", $name));
        $image->drawImage($image_text);

        $image->writeImage($path);
    }
}
