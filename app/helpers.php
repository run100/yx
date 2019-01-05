<?php
/**
 * 专题项目 缓存KEY前缀
 * @param $projectId
 * @return string
 */
function proj_redis_ns($projectId)
{
    return 'prj:'.$projectId;
}

/**
 * 带 msg 的跳转
 * @param string $msg 消息
 * @param string $url 跳转地址
 * @param int $delay 延时
 * @return App\Http\Response\RedirectMessageResponse
 */
function redirect_message($msg, $url, $delay = 3)
{
    throw new \App\Exceptions\RedirectMessageException($msg, $url, $delay);
}


function linkto($name, $url, $target = '_self')
{
    return "<a href=\"$url\" target=\"$target\">$name</a>";
}

function script_in_php($script)
{
    return preg_replace('@^\s*[<]script>([\s\S]+)[<]\/script>\s*$@', '$1', $script);
}


function uploads_path($path)
{
    if (is_lumen()) {
        return "/var/www/public/uploads/" . $path;
    }

    return Storage::disk('uploads')->path($path);
}

function uploads_url($path)
{
    if (is_lumen()) {
        return config('app.url') . '/uploads/' . $path;
    }

    return Storage::disk('uploads')->url($path);
}

function move_to_uploads(\Illuminate\Http\UploadedFile $file, $limits = [])
{


    if (@$limits['size'] && $file->getSize() > $limits['size']) {
        throw new \App\Exceptions\UploadsStorageException("文件尺寸超限", 1);
    }

    if (@$limits['ext'] && !in_array(strtolower($file->getClientOriginalExtension()), $limits['ext'])) {
        throw new \App\Exceptions\UploadsStorageException("文件扩展名受限", 2);
    }

    $file_mime = $file->getMimeType();
    if (@$limits['mime']) {
        $valid = false;
        foreach ($limits['mime'] as $mime) {
            if (fnmatch($mime, $file_mime)) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            throw new \App\Exceptions\UploadsStorageException("文件 MIME 类型受限", 3);
        }
    }

    $limits['optimize'] = @$limits['optimize'] ?: [
        'resolution'    => '1920x1920',     //默认最大分辨率, 超过会被缩放
        'size'          => 500 * 1024       //默认最大文件尺寸, 超过会被压缩
    ];

    //如果是图片则限制宽高；以及限制文件尺寸
    $ext = $file->getClientOriginalExtension();
    if (@$limits['optimize'] && fnmatch('image/*', $file_mime) && $file->getSize() > $limits['optimize']['size']) {
        $img = new \Imagick($file->getRealPath());
        $wh = explode('x', $limits['optimize']['resolution']);
        $w = (int)$wh[0];
        $h = (int)$wh[1];
        if (max($img->getImageHeight(), $img->getImageWidth()) > min($w, $h)) {
            $img->resizeImage($w, $h, \Imagick::FILTER_CUBIC, 1, true);
        }

        $blob = $img->getImageBlob();
        if (strlen($blob) > $limits['optimize']['size']) {
            $img->setImageFormat("jpeg");
            $img->setOption('jpeg:extent', ((int)($limits['optimize']['size'] / 1024)) . 'KB');
            $ext = 'jpg';
            $blob = $img->getImageBlob();
        }

        file_put_contents($file->getRealPath(), $blob);
    }

    $filename = ( @$limits['dir'] ? @$limits['dir'] . '/' : '' ) . make_uniq_filename($ext);
    try {
        $file->move(dirname(uploads_path($filename)), basename($filename));
    } catch (\Exception $ex) {
        throw new \App\Exceptions\UploadsStorageException("文件存储失败", 4, $ex);
    }
    return $filename;
}

function make_uniq_filename($type)
{
    return date('Y/md/') . md5(wj_uuid()) . '.' . $type;
}

function route_current_url($with_query = null, $with_host = false)
{
    if ($with_query !== false) {
        $qs = \Request::getQueryString();
        parse_str($qs, $arr);
        if (is_array($with_query)) {
            $arr = array_merge($arr, $with_query);
        }
        $qs = http_build_query($arr);
        if ($qs) {
            $qs = "?$qs";
        }
    } else {
        $qs = '';
    }

    $path = \Request::getBaseUrl().\Request::getPathInfo().$qs;

    if ($with_host) {
        return \Request::getSchemeAndHttpHost().$path;
    } else {
        return $path;
    }
}

function is_lumen()
{
    return strpos(App::version(), 'Lumen') !== false;
}


/**
 * @return \Overtrue\Socialite\User
 */
function wx_user()
{
    return session('wechat.oauth_user');
}

function wx_info($without_security = true)
{
    $user = wx_user();

    if (!$user) {
        return false;
    }

    $info = wx_user()->getOriginal();
    if ($without_security) {
        unset($info['access_token'], $info['refresh_token'], $info['openid']);
    }
    return $info;
}

function wx_openid()
{
    $user = wx_user();

    if (!$user) {
        return false;
    }

    if (!$user->id) {
        return false;
    }

    return wx_user()->id;
}

function proj($id = null)
{
    if ($id) {
        return \App\Models\Project::repository()->retrieveByPK($id);
    }

    $request = \Request::instance();
    if ($request) {
        if ($request->attributes->has('project')) {
            return $request->attributes->get('project');
        }

        $path = $request->getPathInfo();
        return \App\Models\Project::matchByPath($path);
    }

    return null;
}

function proj_id()
{
    return proj()->id;
}

//生成订单号
function make_order_no($prefix = 'ZT')
{
    $id = \RedisDB::incr("global:payment:order_counter");
    return strtoupper(
        sprintf(
            '%s%04s%s%s',
            $prefix,
            base_convert($id % pow(36, 4), 10, 36), //4位36进制数可支持日订单100W
            date('Ymd'),                                                 //日期
            config('app.env') === 'local' ? 'D' : 'P'                           //测试订单以D结尾，线上订单以P结尾
        )
    );
}

//生成交易流水号
function make_trade_no($order_no)
{
    $id = \RedisDB::incr("global:payment:trade_counter");
    return strtoupper(
        sprintf(
            '%s%02s',
            $order_no,
            base_convert($id % pow(36, 2), 10, 36)
        )
    );
}


function ms($name)
{
    return \MicroServs::getClientInst($name);
}

function ms_concurrent($func, $wait = true)
{
    return \MicroServs::concurrent($func, $wait);
}

function ms_exception(\Yar_Server_Exception $ex)
{
    throw new Wanjia\Common\Exceptions\AppException($ex->getMessage(), $ex->getCode());
}

if (!function_exists('mb_sprintf')) {
    function mb_sprintf($format)
    {
        $argv = func_get_args() ;
        array_shift($argv) ;
        return mb_vsprintf($format, $argv) ;
    }
}

if (!function_exists('mb_vsprintf')) {
    /**
     * Works with all encodings in format and arguments.
     * Supported: Sign, padding, alignment, width and precision.
     * Not supported: Argument swapping.
     */
    function mb_vsprintf($format, $argv, $encoding=null)
    {
        if (is_null($encoding))
            $encoding = mb_internal_encoding();

        // Use UTF-8 in the format so we can use the u flag in preg_split
        $format = mb_convert_encoding($format, 'UTF-8', $encoding);

        $newformat = ""; // build a new format in UTF-8
        $newargv = array(); // unhandled args in unchanged encoding

        while ($format !== "") {

            // Split the format in two parts: $pre and $post by the first %-directive
            // We get also the matched groups
            list ($pre, $sign, $filler, $align, $size, $precision, $type, $post) =
                preg_split("!\%(\+?)('.|[0 ]|)(-?)([1-9][0-9]*|)(\.[1-9][0-9]*|)([%a-zA-Z])!u",
                    $format, 2, PREG_SPLIT_DELIM_CAPTURE) ;

            $newformat .= mb_convert_encoding($pre, $encoding, 'UTF-8');

            if ($type == '') {
                // didn't match. do nothing. this is the last iteration.
            }
            elseif ($type == '%') {
                // an escaped %
                $newformat .= '%%';
            }
            elseif ($type == 's') {
                $arg = array_shift($argv);
                $arg = mb_convert_encoding($arg, 'UTF-8', $encoding);
                $padding_pre = '';
                $padding_post = '';

                // truncate $arg
                if ($precision !== '') {
                    $precision = intval(substr($precision,1));
                    if ($precision > 0 && mb_strwidth($arg, $encoding) > $precision)
                        $arg = mb_substr($precision,0,$precision,$encoding);
                }

                // define padding
                if ($size > 0) {
                    $arglen = mb_strwidth($arg, $encoding);
                    if ($arglen < $size) {
                        if($filler==='')
                            $filler = ' ';
                        if ($align == '-')
                            $padding_post = str_repeat($filler, $size - $arglen);
                        else
                            $padding_pre = str_repeat($filler, $size - $arglen);
                    }
                }

                // escape % and pass it forward
                $newformat .= $padding_pre . str_replace('%', '%%', $arg) . $padding_post;
            }
            else {
                // another type, pass forward
                $newformat .= "%$sign$filler$align$size$precision$type";
                $newargv[] = array_shift($argv);
            }
            $format = strval($post);
        }
        // Convert new format back from UTF-8 to the original encoding
        $newformat = mb_convert_encoding($newformat, $encoding, 'UTF-8');
        return vsprintf($newformat, $newargv);
    }
}


if (!function_exists('mb_str_pad')) {
    function mb_str_pad($str, $pad_length, $pad_string, $pad_type = STR_PAD_RIGHT, $encoding = null)
    {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }

        $w = mb_strwidth($str, $encoding);
        $len = strlen($str);
        $pad_length += $len - $w;
        return str_pad($str, $pad_length, $pad_string, $pad_type);
    }
}

if (!function_exists('read_qrcode')) {
    function read_qrcode($img)
    {
        if (is_string($img)) {
            if (preg_match('@^(https?:/)?/@i', $img)) {
                $tmpfile = tempnam('/tmp', 'QR');
                file_put_contents($tmpfile, file_get_contents($img));

                //如果是微信远程二维码；缩放后再识别，可以提高识别率和效率(原图太大了)
                if (preg_match('@mmbiz\.qpic\.cn/mmbiz@', $img)) {
                    $im = imagecreatefromjpeg($tmpfile);
                    $imn = imagecreatetruecolor(300, 300);
                    imagecopyresampled($imn, $im, 0, 0, 0, 0, 300, 300, imagesx($im), imagesy($im));
                    imagejpeg($imn, $tmpfile);
                }

                $img = $tmpfile;
            }
        }

        $decoder        = new \PHPZxing\PHPZxingDecoder([
            'pure_barcode'  => true
        ]);
        $decodedData    = current($decoder->decode($img));
        if ($decodedData instanceof \PHPZxing\ZxingImage) {
            $url = $decodedData->getImageValue();
        } else {
            $url = null;
        }

        if ($tmpfile && is_file($tmpfile)) {
            @unlink($tmpfile);
        }
        return $url;
    }
}


/**
 *@param $length 字符串截取函数
 *@return String
 */
if (!function_exists('mb_text_bytecut')) {
  function mb_text_bytecut($text, $length = 30, $postfix = false)
  {
    if ($postfix) {
      $text = mb_strcut(trim($text), 0, ($length - 1) * 3, 'utf-8') . '...';
    } else {
      $text = mb_strcut(trim($text), 0, $length * 3, 'utf-8');
    }
    return $text;
  }
}

/**
 * @return \Redis
 */
function wj_redis()
{
    return \RedisDB::connection('default');
}
