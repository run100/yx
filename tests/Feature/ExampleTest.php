<?php

namespace Tests\Feature;

use Tests\TestCase;


class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $request = Request::create(
            '/yx2018/start',
            'GET'
        );

        $response = app(Kernel::class)->handle($request);

    }

    public function testbuildImgTest()
    {
        $host = config("app.url");
        $fontPfMedium = public_path("/font/PingFang-SC-Medium.ttf");
        $bgPic = $host . "/cjx2019/images/share_bg.jpg";
        $avatar = "http://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83ep8YUnk8Z9LASQRAd2JH0pQFIZlqKLbgZWQPAoibzWqEjENx7CrZKOvCQM6sjRFb45iaYb3ZficOxCRg/132";
        $nickName = "徐洁大大";
        $no = 9999;
        $openid = md5("o8hpSuCXgWctyeoptu6sfQlfVcQE");
        $filename = $openid . ".jpg";

        $dir = public_path('/uploads/buildcjx2019/');
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $path = $dir . $filename;

        $imageBg = new \Imagick($bgPic);

        $imageWexin = new \Imagick($avatar);
        $imageWexin->roundCorners(10, 10);
        $imageWexin->thumbnailImage(154, 154);

        $imageBg->compositeImage(
            $imageWexin,
            \Imagick::COMPOSITE_OVER,
            246,
            274
        );

        $image_text = new \ImagickDraw();
        $image_text->setStrokeWidth(3);
        $image_text->setTextEncoding('UTF-8');
        $image_text->setGravity(\Imagick::GRAVITY_NORTHWEST);
        $image_text->setFont($fontPfMedium);

        $noLen = strlen($no);
        $no_x = 300 - 10 * $noLen;

        $image_text->setFontSize(34);
        $image_text->setFillColor('#FFFFFF');
        $image_text->setFontSize(28);
        $image_text->annotation($no_x, 616, $no);

        $noLen = strlen($no);
        $no_x = 340 - 20 * $noLen;
        $image_text->setFillColor('#FFFFFF');
        $image_text->setFontSize(28);
        $image_text->annotation($no_x, 580, $nickName);

        $imageBg->drawImage($image_text);

        $imageBg->writeImage($path);

        echo $path;

    }
}
