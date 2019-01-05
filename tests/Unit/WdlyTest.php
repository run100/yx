<?php

namespace Tests\Unit;

use App\Features\Wdly2018;
use Tests\TestCase;
use App\Models\Player;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;


class WdlyTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        print_r(wj_parse_sfz('340822199305290020'));

        $info = wx_info();
        $player_id = '0026';

        $player = Player::repository()->findOneByProjectId(90, ['ticket_no' => $player_id]);

        abort_if(!$player, 404);
//        dd($player)
        $player->info_cover = $this->makeCover($player, $info);
        $player->save();
        dd(public_path($player->info_cover));
        return compact('player');
    }

    protected function makeCover($player, $info)
    {
        $path = '/var/www/storage/app/public/2018/0420/d84843749bedc1c8da58c545a3a3c708.png';
//        dd($path);
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        //创建临时文件用于存储二维码
        $tmpfile = tempnam('/tmp', 'ZT');

        $info['headimgurl'] = 'http://thirdwx.qlogo.cn/mmopen/vi_32/ORJaR9vwpaIXnELJFPhsOgp7KKbdlLKl99clwiad6jY4eOYwhIclSgibTQj3LatKLIjdPmNIG4o81vVeQTs9LExg/132';
        if ($info['headimgurl']) {
            $ch = curl_init($info['headimgurl']);
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
        $image_pic->cropThumbnailImage(100, 100);
        //做圆角; 有可能部分环境下的Imagick不支持圆角，如Mac系统上就不支持
//        if (method_exists($image_pic, 'roundCorners')) {
//            $image_pic->roundCorners(49, 49);
//        }
        @unlink($tmpfile);


        //读取背景
        $image = new \Imagick(public_path('web/wdly2018/images/tem_cover.png'));


        //贴选手头像
        $image->compositeImage(
            $image_pic,
            \Imagick::COMPOSITE_OVER,
            34,
            1009
        );

        $text_w = mb_strlen('天然呆');
        $text = '';
        $pos = 0;
        $lines = 0;
        while ($pos < $text_w) {
            $lines ++;
            $line = mb_strimwidth('天然呆', $pos, 16);
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
        $image_text->annotation(195, 1048, sprintf(" %s ", $nickname));
        $image_text->annotation(135, 1230, sprintf(" %s ", 'wdly'.$player->ticket_no));
        $image->drawImage($image_text);

        $filename = make_uniq_filename('png');

        $image->writeImage(uploads_path($filename));
        return $filename;
    }
}
