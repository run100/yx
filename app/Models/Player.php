<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Wanjia\Common\Database\ExModel;
use Wanjia\Common\Exceptions\AppException;

class Player extends Model
{
    use ExModel;

    protected $casts = [
        'info'  => 'object',
    ];

    protected $table = 'player';
    protected static $unguarded = true;

    protected $_vote_pending_operator;

    protected $broadcast_enabled = true;

    protected static function boot()
    {
        static::creating(function (Player $m) {
            if ($m->broadcast_enabled) {
                $m->loadMissing(['project', 'merchant']);
                event('model.player.creating', [$m->project->path, $m]);
            }
        });
        static::updating(function (Player $m) {
            if ($m->broadcast_enabled) {
                $m->loadMissing(['project', 'merchant']);
                event('model.player.updating', [$m->project->path, $m]);
            }
        });
        static::deleting(function (Player $m) {
            if ($m->broadcast_enabled) {
                $m->loadMissing(['project', 'merchant']);
                event('model.player.deleting', [$m->project->path, $m]);
            }
        });

        static::saving(function (Player $m) {
            if ($m->isDirty('info')) {
                $m->regenerateMeta();
            }

            if ($conf_baoming = @$m->project->conf_baoming) {
                if ($conf_baoming->ticket_mode === 'auto' && !$m->ticket_no) {
                    $m->ticket_no = \RedisDB::incr("prj:{$m->project_id}:auto_counter");

                    if ($conf_baoming->ticket_length) {
                        $m->ticket_no = sprintf("%0{$conf_baoming->ticket_length}d", $m->ticket_no);
                    }
                }
            }

            //投票字段以增量方式更新
            foreach (['vote1', 'vote2', 'vote3'] as $vote_field) {
                if ($m->isDirty($vote_field)) {
                    $old = $m->getOriginal($vote_field);
                    $incr = $m->$vote_field - $old;
                    $m->$vote_field = $old;
                    $m->incrBy($vote_field, $incr, false);
                }
            }
        });


        static::saved(function (Player $m) {

            //记录增票日志
            foreach ($m->_pending_incrs as $field => $exp) {
                if (!in_array($field, ['vote1', 'vote2', 'vote3'])) {
                    continue;
                }

                if (!$m->_vote_pending_operator) {
                    throw new AppException("Not permitted for null operator.", 1001);
                }

                //记录日志
                $log = new VoteLog();
                $log->project()->associate($m->project);
                $log->fill([
                    'player_id'     => $m->id,
                    'merchant_id'   => 0,
                    'field'         => $field,
                    'incr'          => $exp->_incr
                ]);
                $log->fill($m->_vote_pending_operator);
                $log->save();
            }

            $m->_vote_pending_operator = null;
        });

        static::created(function (Player $m) {
            if ($m->broadcast_enabled) {
                $m->loadMissing(['project', 'merchant']);
                event('model.player.created', [$m->project->path, $m]);
            }
        });

        static::updated(function (Player $m) {
            if ($m->broadcast_enabled) {
                $m->loadMissing(['project', 'merchant']);
                event('model.player.updated', [$m->project->path, $m]);
            }
        });

        static::deleted(function (Player $m) {
            if ($m->broadcast_enabled) {
                $m->loadMissing(['project', 'merchant']);
                event('model.player.deleted', [$m->project->path, $m]);
            }
        });

        parent::boot();
    }

    public function enableBroadcast($enable = true)
    {
        $this->broadcast_enabled = $enable;
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function displayField($field, $format = false)
    {

        $form_design = collect(@$this->form_design);

        $field = $form_design->where('field', '=', $field)->values()->get(0);
        if (!$field) {
            return '';
        }

        if (strpos($field->key, 'vote') === 0) {
            $value = $this->{$field->key};
        } else {
            $value = @$this->{"info_{$field->field}"};
        }

        if (!$format) {
            return $value;
        }

        switch ($field->type) {
            case 'gender':
                return @$this->listGender()[$value] ?: '未知';
            case 'passport':
                if (!$value) {
                    return '';
                }
                $value = explode(':', $value);
                return (@$this->listPassportType()[$value[0]] ?: '其他') . ':' . $value[1];
            case 'city':
                return wj_city_fullname($value, ' ', false) ?: '未知地区';
            case 'datetime':
                if ($field->options->datetime->input_type === 'range') {
                    if (!$value) {
                        return '';
                    }

                    return "{$value['start']} ~ {$value['end']}";
                }

                return $value;
            default:
                if (in_array($field->type, ['checkbox', 'radio', 'select']) && @$field->options) {
                    $options = @$field->options->{$field->type}->options;
                    $options = collect(wj_obj2arr($options))
                        ->whereIn('key', is_array($value) ? $value : [$value])
                        ->pluck('name', 'key')
                        ->implode(',');
                    return $options ?: '未知';
                }
                return $value;
        }
    }

    public function listOptionsByField($field)
    {
        if (@$this->_options_cache[$field]) {
            return $this->_options_cache[$field];
        }

        $form_design = collect(@$this->form_design);
        $field = $form_design->where('field', '=', $field)->values()->get(0);

        $ret = $field;

        if (!($ret = @$ret->options)) {
            return [];
        }

        if (!($ret = @$ret->{$field->type})) {
            return [];
        }

        if (!($ret = @$ret->options)) {
            return [];
        }

        return $this->_options_cache[$field] = collect($ret)->pluck('name', 'key')->all();
    }

    public function listChecked()
    {
        return [
            0   => '待审核',
            1   => '审核通过',
            2   => '审核未通过'
        ];
    }

    public function listGender()
    {
        return [
            'M'     => '男',
            'W'     => '女'
        ];
    }

    public function listPassportType()
    {
        return [
            'SFZ'   => '身份证',
            'GAT'   => '港澳台通行证',
            'TBZ'   => '台胞证',
            'HUZ'   => '护照',
            'OTH'   => '其他'
        ];
    }

    public function regenerateMeta()
    {
        $fields = $this->form_design;
        $this->fill([
            'str1'      => '',
            'str2'      => '',
            'str3'      => '',
            'str4'      => '',
            'str5'      => '',
            'str6'      => '',
            'str7'      => '',
            'str8'      => '',
            'str9'      => '',
            'str10'      => '',
            'int1'      => 0,
            'int2'      => 0,
            'int3'      => 0,
            'int4'      => 0,
            'int5'      => 0,
        ]);

        $attrs = [];
        foreach ($fields as $field) {
            if ($field->key && strpos($field->key, 'vote') !== 0) {
                $val = $this->{"info_{$field->field}"};

                if (is_array($val) && strpos($field->key, 'str') === 0) {
                    if (count($val)) {
                        $val = ':' . implode(':', $val) . ':';
                    } else {
                        $val = '';
                    }
                }

                if (!$val) {
                    if (strpos($field->key, 'str') === 0) {
                        $val = '';
                    }
                    if (strpos($field->key, 'int') === 0) {
                        $val = 0;
                    }
                }
                $attrs[$field->key] = $val;
            }
        }

        $this->setAttributes($attrs);
    }

    public function withOperatorUid($uid)
    {
        if (!$this->_vote_pending_operator) {
            $this->_vote_pending_operator = [];
        }

        $this->_vote_pending_operator['operator_uid'] = $uid;
        return $this;
    }

    public function withOperatorIp($ip)
    {
        if (!$this->_vote_pending_operator) {
            $this->_vote_pending_operator = [];
        }

        $this->_vote_pending_operator['ip'] = $ip;
        return $this;
    }

    public function withOperatorPhone($phone)
    {
        if (!$this->_vote_pending_operator) {
            $this->_vote_pending_operator = [];
        }

        $this->_vote_pending_operator['phone'] = $phone;
        return $this;
    }

    public function withOperatorOpenId($merchant, $openid)
    {
        if (!$this->_vote_pending_operator) {
            $this->_vote_pending_operator = [];
        }

        $this->_vote_pending_operator['openid'] = $openid;
        $this->_vote_pending_operator['merchant_id'] = $merchant->id;
        return $this;
    }

    public function withOperatorNote($note)
    {
        if (!$this->_vote_pending_operator) {
            $this->_vote_pending_operator = [];
        }

        $this->_vote_pending_operator['note'] = $note;
        return $this;
    }

    public function withOperatorForce($force = true)
    {
        if (!$this->_vote_pending_operator) {
            $this->_vote_pending_operator = [];
        }

        $this->_vote_pending_operator['force'] = $force;
        return $this;
    }


    public function getFormDesignAttribute() : array
    {
        return @$this->project->conf_base_form_design ?: [];
    }

    public function getAttribute($key)
    {
        if (($pos = strpos($key, 'info_')) === 0) {
            $field = substr($key, 5);

            $form_design = collect(@$this->form_design);
            $fieldObj = $form_design->where('field', '=', $field)->values()->get(0);
            if ($fieldObj && $fieldObj->type === 'datetime') {
                if ($fieldObj->options->datetime->input_type === 'range') {
                    $ret = [
                        'start' => $this->{"info_{$fieldObj->field}_start"},
                        'end'   => $this->{"info_{$fieldObj->field}_end"}
                    ];

                    if (!$ret['start'] && !$ret['end']) {
                        return null;
                    }

                    return $ret;
                }
            } elseif ($fieldObj && $fieldObj->type === 'uploads') {
                return json_decode($this->info->$field, true);
            }

            return @$this->info->$field;
        } elseif (($pos = strpos($key, 'fmt_info_')) === 0) {
            $field = substr($key, 9);
            return $this->displayField($field, true);
        }

        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        if (($pos = strpos($key, 'info_')) === 0) {
            $field = substr($key, 5);

            $info = $this->info;
            if (!$info) {
                $info = new \stdClass();
            }

            $info->$field = is_array($value) ? json_encode($value) : $value;
            $this->info = $info;

            return $this;
        }

        if (($pos = strpos($key, 'info.')) === 0) {
            $field = substr($key, 5);

            $info = $this->info;
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

            $this->info = $info;

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    public function hasSetMutator($key)
    {
        if (($pos = strpos($key, 'info_')) === 0) {
            return true;
        }

        if (($pos = strpos($key, 'info.')) === 0) {
            return true;
        }

        return parent::hasSetMutator($key);
    }

    public function hasGetMutator($key)
    {
        if (($pos = strpos($key, 'info_')) === 0) {
            return true;
        }

        return parent::hasGetMutator($key);
    }
}
