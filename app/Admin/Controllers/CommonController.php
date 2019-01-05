<?php
namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\UploadedFile;
use Wanjia\Common\Exceptions\AppException;
use App\Exceptions\UploadsStorageException;


class CommonController extends Controller
{
    /**
     * ueditor 编辑器上传图片
     */
    public function ueUpload(Request $request)
    {
        $action = $request->get('action');
        switch ($action) {
            case 'config':
                $ret = $this->getUEConfig();
                break;
            case 'uploadimage':
                $file = $request->file('upfile');
                try {
                    $pic = move_to_uploads($file, [
                        'ext' => ['jpg', 'png', 'jpeg', 'gif'],
                        'mime' => ['image/*'],
                        'dir' => 'ueditor'
                    ]);
                    $ret = [ "state" => 'SUCCESS',"url" => uploads_url($pic) ];
                } catch (UploadsStorageException $ex) {
                    if ($ex->getCode() === UploadsStorageException::CODE_SIZE_LIMITED) {
                        $err = '照片尺寸超限，需在5M 以内!';
                    } elseif ($ex->getCode() === UploadsStorageException::CODE_EXT_LIMITED) {
                        $err = '请上传正确的图片文件格式(jpg/png)!';
                    } elseif ($ex->getCode() === UploadsStorageException::CODE_MIME_LIMITED) {
                        $err = '请上传正确的图片文件格式(jpg/png)!';
                    } else {
                        $err = '图片保存失败!';
                    }
                    $ret = [ "state" => $err ];
                } catch (\Exception $e) {
                    $ret = [ "state" => $e->getMessage() ];
                }
                break;
            default:
                $ret = [ "state" => '方法未定义' ];
                break;
        }
        return response()->json($ret);
    }

    protected function getUEConfig() :array
    {
        $config = [];
        $config['imageActionName'] = 'uploadimage';
        $config['imageFieldName'] = 'upfile';
        $config['imageMaxSize'] = 1024 * 1024 * 5 ;
        $config['imageAllowFiles'] = [".png", ".jpg", ".jpeg", ".gif"];
        $config['imageInsertAlign'] = 'none';
        $config['imageCompressEnable'] = true;
        $config['imageCompressBorder'] = 1600;
        $config['imageUrlPrefix'] = '';
        return $config;
    }

}