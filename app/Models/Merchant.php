<?php

namespace App\Models;

use App\Jobs\MerchantJob;
use App\Models\Wechat\AutoReply;
use App\Models\Wechat\Menu;
use EasyWeChat\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wanjia\Common\Database\ExModel;
use Wanjia\Common\Exceptions\AppException;
use Doctrine\Common\Cache\FilesystemCache;

/**
 *
 * @property boolean $conf_wechat_enable_subscribe_reply
 * @property string $conf_wechat_subscribe_reply
 *
 */
class Merchant extends Model
{
    use ExModel;

    protected $table = 'merchant';
    protected $dates = ['pre_auth_code_expire_at'];


    protected $casts = [
        'extras'    => 'object',
        'configs'   => 'object'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Merchant $m) {
            if ($m->isDirty('pre_auth_code')) {
                $m->pre_auth_code_expire_at = $m->freshTimestamp()->addDay();
            }
        });
    }


    public function listType()
    {
        return [
            1     => '第三方授权',
            2     => '直连开发'
        ];
    }


    public function valid_auto_replys()
    {
        return $this->hasMany(AutoReply::class, 'merchant_id', 'id')
            ->where('wechat_auto_reply.start_at', '<=', \DB::raw('current_timestamp'))
            ->where('wechat_auto_reply.end_at', '>=', \DB::raw('current_timestamp'));
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'merchant_id', 'id');
    }

    /**
     * 预授权地址
     */
    public function getPreAuthUrlAttribute() : string
    {
        if (!$this->pre_auth_code) {
            return '';
        }

        return route('wechat.bind', ['code' => $this->pre_auth_code]);
    }

    /**
     * 是否已授权
     */
    public function getIsAuthedAttribute() : bool
    {
        return !!$this->appid && !!$this->refresh_token;
    }

    public function getStatusStrAttribute() : string
    {
        if ($this->is_authed) {
            return '已授权';
        } else {
            return '待授权';
        }
    }

    /**
     * 微信 App
     * @throws AppException
     */
    public function getWechatAppAttribute() : Application
    {
        if ($this->type == 2) {
            $options = [
                'debug'     => config('wechat.debug'),
                'app_id'    => $this->appid,
                'secret'    => $this->conf_dev_secret,
                'token'     => $this->conf_dev_token,
                'aes_key'   => $this->conf_dev_aes_key ?: null,
                'cache' => new FilesystemCache(storage_path('easywechat'))
            ];
            $app = new Application($options);
        } else {
            if (!$this->is_authed) {
                throw new AppException('公众号尚未授权给开放平台', 101);
            }

            $app = \EasyWeChat::open_platform()->createAuthorizerApplication($this->appid, $this->refresh_token);
        }

        if ($this->mch_key) {
            $merchant = $app->merchant;
            $merchant->app_id = $this->appid;
            $merchant->merchant_id = $this->clear_mch_id;
            $merchant->key = $this->clear_key;
            $merchant->device_info = 'WEB';
            unset($merchant->cert_path);
            unset($merchant->key_path);
        }


        return $app;
    }


    /**
     * 带支付证书的微信 App
     */
    public function getWechatCertedAppAttribute() : Application
    {
        $app = $this->wechat_app;

        if ($this->mch_key) {

            $cert_path = storage_path('secured/wxcerts/')
                . md5("{$this->appid}.{$this->clear_mch_id}.{$this->clear_key}");

            $cert_file = "$cert_path/cert.pem"; //p12解出的证书文件
            $key_file = "$cert_path/key.pem";   //p12解出的私钥文件
            $hash_file = "$cert_path/p12.sha";  //p12指纹，用于检测p12证书有没有更新

            if (!is_dir($cert_path)) {
                $sePath = storage_path('secured');
                if (!is_dir($sePath)) {
                    mkdir($sePath);
                }
                $wxPath = storage_path('secured/wxcerts');
                if (!is_dir($wxPath)) {
                    mkdir($wxPath);
                }
                mkdir($cert_path);
            }

            if (!is_file($hash_file)) {
                file_put_contents($hash_file, '');
            }

            //检查p12指纹，若不同则重建 key和cert
            $sha = sha1($this->clear_p12);
            if (file_get_contents($hash_file) !== $sha) {
                $results = array();
                if (openssl_pkcs12_read($this->clear_p12, $results, $this->clear_mch_id)) {
                    file_put_contents($cert_file, $results['cert']);
                    file_put_contents($key_file, $results['pkey']);
                }
                file_put_contents($hash_file, $sha);
            }

            $app->merchant->cert_path = $cert_file;
            $app->merchant->key_path = $key_file;
        }


        return $app;
    }

    /**
     * 公众号头像
     * @return string
     */
    public function getWechatHeadImgAttribute() : ?string
    {
        return @$this->extras->authorizer_info->head_img;
    }

    /**
     * 公众号二维码(微信不支持跨站引用)
     * @return string
     */
    public function getWechatQrcodeUrlAttribute() : ?string
    {
        return @$this->extras->authorizer_info->qrcode_url;
    }

    /**
     * 公众号二维码包含的 URL 地址(可用 QrCode 扩展重新生成)
     * @return string
     */
    public function getWechatQrcodeTextAttribute() : ?string
    {
        return @$this->extras->authorizer_info->qrcode_text;
    }

    /**
     * 微信原始 ID
     * @return string
     */
    public function getWechatUsernameAttribute() : ?string
    {
        return @$this->extras->authorizer_info->user_name;
    }

    /**
     * 公众号名称
     * @return string
     */
    public function getWechatNicknameAttribute() : ?string
    {
        return @$this->extras->authorizer_info->nick_name;
    }

    /**
     * 微信号
     * @return string
     */
    public function getWechatAliasAttribute() : ?string
    {
        return @$this->extras->authorizer_info->alias;
    }


    /**
     * 主体
     * @return string
     */
    public function getWechatPrincipalAttribute() : ?string
    {
        return @$this->extras->authorizer_info->principal_name;
    }

    /**
     * 微信帐号类型
     * @return int
     */
    public function getWechatAccountTypeIdAttribute() : ?int
    {
        return @$this->extras->authorizer_info->service_type_info->id;
    }

    public function getWechatAccountTypeAttribute() : string
    {
        return [
            0   => '订阅号',
            1   => '订阅号', //历史老帐号升级
            2   => '服务号'
        ][$this->wechat_account_type_id] ?? '未知类型';
    }

    /**
     * 帐号能力
     * @return \stdClass
     */
    public function getWechatBusinessInfoAttribute() : ?\stdClass
    {
        return @$this->extras->authorizer_info->business_info;
    }

    public function getWechatBusinessInfoStrAttribute() : string
    {
        $bi = $this->wechat_business_info;
        $ret = [];
        if (@$bi->open_pay) {
            $ret[] = '支付';
        }
        if (@$bi->open_shake) {
            $ret[] = '摇一摇';
        }
        if (@$bi->open_scan) {
            $ret[] = '扫商品';
        }
        if (@$bi->open_store) {
            $ret[] = '门店';
        }
        if (@$bi->open_card) {
            $ret[] = '卡券';
        }
        return implode(',', $ret);
    }

    /**
     * 已授权限
     * @return array
     */
    public function getWechatFuncInfoAttribute() : array
    {
        return collect(@$this->extras->authorization_info->func_info)
            ->pluck('funcscope_category')
            ->pluck('id')
            ->values()
            ->all();
    }


    public function getWechatFuncInfoStrAttribute() : string
    {
        $funcs = [
            1 => '消息管理',
            2 => '用户管理',
            3 => '帐号服务',
            4 => '网页服务',
            5 => '微信小店',
            6 => '微信多客服',
            7 => '群发与通知',
            8 => '微信卡券',
            9 => '微信扫一扫',
            10 => '微信连WIFI',
            11 => '素材管理',
            12 => '微信摇周边',
            13 => '微信门店',
            15 => '自定义菜单',
            17 => '帐号管理',
            18 => '开发管理与数据分析',
            19 => '客服消息管理',
            22 => '城市服务接口',
            23 => '广告管理',
            24 => '开放平台帐号管理',
            25 => '开放平台帐号管理',
            26 => '微信电子发票',
            30 => '小程序基本信息设置',
            31 => '小程序认证'
        ];

        $bi = $this->wechat_func_info;
        $arr = collect($funcs)->filter(function($v, $k) use($bi) {
            return in_array($k, $bi);
        })->all();

        return implode(',', $arr);
    }


    public function refreshAuthorizerInfo($async = false)
    {
        if ($async) {
            dispatch(new MerchantJob('sync_info', $this->id));
            return;
        }

        $app = $this->wechat_app;

        if ($this->type == 1) {
            $platform = \EasyWeChat::open_platform();
            $author = $platform->getAuthorizerInfo($this->appid)->toArray();

            $qrcode = read_qrcode($author['authorizer_info']['qrcode_url']);
            $author['authorizer_info']['qrcode_text'] = $qrcode;
        } else {
            $author = [];
            $ret = $app->qrcode->forever("zhuanti.admin");
            $author['authorizer_info']['qrcode_url'] = $app->qrcode->url($ret['ticket']);
            $author['authorizer_info']['qrcode_text'] = $ret['url'];
        }

        $author['authorizer_info']['reply_settings'] = $app->reply->current();
        $this->extras = $author;
    }

    protected $clear_mch_key;

    public function getClearMchIdAttribute()
    {
        $this->prepareMchKey();

        if (!$this->clear_mch_key) {
            return '';
        }

        return $this->clear_mch_key[0];
    }

    public function getClearKeyAttribute()
    {
        $this->prepareMchKey();

        if (!$this->clear_mch_key) {
            return '';
        }

        return $this->clear_mch_key[1];
    }

    public function getClearP12Attribute()
    {
        $this->prepareMchKey();

        if (!$this->clear_mch_key) {
            return '';
        }

        return $this->clear_mch_key[2];
    }

    protected function prepareMchKey()
    {
        if ($this->isDirty('mch_key') || !$this->clear_mch_key) {
            $this->clear_mch_key = explode(':', decrypt($this->mch_key), 3);
        }
    }

    public function getAttribute($key)
    {
        if (($pos = strpos($key, 'conf_')) === 0) {
            $field = substr($key, 5);
            return @$this->configs->$field;
        }

        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        if (($pos = strpos($key, 'conf_')) === 0) {
            $field = substr($key, 5);

            $info = $this->configs;
            if (!$info) {
                $info = new \stdClass();
            }

            $info->$field = $value;
            $this->configs = $info;

            return $this;
        }


        if (($pos = strpos($key, 'configs.')) === 0) {
            $field = substr($key, 8);

            $info = $this->configs;
            if (!$info) {
                $info = new \stdClass();
            }

            //支持多级点赋值
            $nodes = explode('.', $field);
            $lastNode = array_pop($nodes);
            $node = $info;
            foreach ($nodes as $n) {
                if (!@$node->$n) {
                    $node->$n = new \stdClass();
                }
                $node = $node->$n;
            }
            $node->$lastNode = $value;

            $this->configs = $info;

            return $this;
        }



        return parent::setAttribute($key, $value);
    }

    public function hasSetMutator($key)
    {
        if (($pos = strpos($key, 'conf_')) === 0) {
            return true;
        }

        if (($pos = strpos($key, 'configs.')) === 0) {
            return true;
        }

        return parent::hasSetMutator($key);
    }

    public function hasGetMutator($key)
    {
        if (($pos = strpos($key, 'conf_')) === 0) {
            return true;
        }

        return parent::hasGetMutator($key);
    }

    public static function matchByPath($path = null, $with_default = true)
    {
        $project = Project::matchByPath($path);
        if ($project) {
            $merchant = $project->merchant;
        } elseif ($with_default) {
            $merchant = static::repository()->findOneByAppid('wx879e8ff74bf25932');
        }

        return $merchant;
    }

}
