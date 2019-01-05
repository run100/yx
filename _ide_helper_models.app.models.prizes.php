<?php

namespace App\Models\Prizes {

/**
 * App\Models\Prizes\PrizesLog
 *
 * @property integer $id
 * @property integer $player_id 选手 ID
 * @property integer $project_id 项目 ID
 * @property string $field 奖品KEY
 * @property boolean $is_draw 是否领取
 * @property string $draw_info 领奖信息
 * @property string $name 奖品名称
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property boolean $is_win 是否中奖
 * @property string $openid 微信 OPenID
 * @property string $tip 中奖提示
 * @property string $ip IP
 * @property integer $operator_uid 操作者 iD
 * @property boolean $type 奖品类型
 * @property string $wx_name 微信名
 * @property-read mixed $win_text
 * @property-read mixed $draw_text
 * @property-read \App\Models\Player $player
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog wherePlayerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog whereProjectId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog whereField($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog whereIsDraw($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog whereDrawInfo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog whereIsWin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog whereOpenid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog whereTip($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog whereOperatorUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\PrizesLog whereWxName($value)
 * @method static \App\Models\Prizes\PrizesLogRepository repository()
 */
	class PrizesLog extends \Eloquent {}


/**
 * PrizesLogRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByPlayerId($player_id, $limiters = [], $options = [])
 * @method string getSQLByPlayerId($player_id, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findByPlayerId($player_id, $limiters = [], $options = [])
 * @method int countByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByProjectId($project_id, $limiters = [], $options = [])
 * @method string getSQLByProjectId($project_id, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findByProjectId($project_id, $limiters = [], $options = [])
 * @method int countByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByField($field, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByField($field, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByField($field, $limiters = [], $options = [])
 * @method string getSQLByField($field, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneByField($field, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findByField($field, $limiters = [], $options = [])
 * @method int countByField($field, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByIsDraw($is_draw, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByIsDraw($is_draw, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByIsDraw($is_draw, $limiters = [], $options = [])
 * @method string getSQLByIsDraw($is_draw, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneByIsDraw($is_draw, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findByIsDraw($is_draw, $limiters = [], $options = [])
 * @method int countByIsDraw($is_draw, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByDrawInfo($draw_info, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByDrawInfo($draw_info, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByDrawInfo($draw_info, $limiters = [], $options = [])
 * @method string getSQLByDrawInfo($draw_info, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneByDrawInfo($draw_info, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findByDrawInfo($draw_info, $limiters = [], $options = [])
 * @method int countByDrawInfo($draw_info, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByName($name, $limiters = [], $options = [])
 * @method string getSQLByName($name, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findByName($name, $limiters = [], $options = [])
 * @method int countByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByIsWin($is_win, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByIsWin($is_win, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByIsWin($is_win, $limiters = [], $options = [])
 * @method string getSQLByIsWin($is_win, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneByIsWin($is_win, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findByIsWin($is_win, $limiters = [], $options = [])
 * @method int countByIsWin($is_win, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByOpenid($openid, $limiters = [], $options = [])
 * @method string getSQLByOpenid($openid, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findByOpenid($openid, $limiters = [], $options = [])
 * @method int countByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByTip($tip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByTip($tip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByTip($tip, $limiters = [], $options = [])
 * @method string getSQLByTip($tip, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneByTip($tip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findByTip($tip, $limiters = [], $options = [])
 * @method int countByTip($tip, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByIp($ip, $limiters = [], $options = [])
 * @method string getSQLByIp($ip, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findByIp($ip, $limiters = [], $options = [])
 * @method int countByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method string getSQLByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method int countByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByType($type, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByType($type, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByType($type, $limiters = [], $options = [])
 * @method string getSQLByType($type, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneByType($type, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findByType($type, $limiters = [], $options = [])
 * @method int countByType($type, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByWxName($wx_name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByWxName($wx_name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByWxName($wx_name, $limiters = [], $options = [])
 * @method string getSQLByWxName($wx_name, $limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneByWxName($wx_name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findByWxName($wx_name, $limiters = [], $options = [])
 * @method int countByWxName($wx_name, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Prizes\PrizesLog retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\PrizesLog[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class PrizesLogRepository extends \Wanjia\Common\Database\Repository {}


/**
 * App\Models\Prizes\ZhuliLog
 *
 * @property integer $id
 * @property integer $project_id 项目ID
 * @property integer $player_id 选手ID
 * @property string $openid 选手OpenID
 * @property string $zhuli_name 助力者昵称
 * @property string $zhuli_openid 助力者OpenID
 * @property integer $operator_uid 操作者
 * @property string $ip 来源IP
 * @property string $note 备注
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\ZhuliLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\ZhuliLog whereProjectId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\ZhuliLog wherePlayerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\ZhuliLog whereOpenid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\ZhuliLog whereZhuliName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\ZhuliLog whereZhuliOpenid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\ZhuliLog whereOperatorUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\ZhuliLog whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\ZhuliLog whereNote($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\ZhuliLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Prizes\ZhuliLog whereUpdatedAt($value)
 * @method static \App\Models\Prizes\ZhuliLogRepository repository()
 */
	class ZhuliLog extends \Eloquent {}


/**
 * ZhuliLogRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Prizes\ZhuliLog findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\ZhuliLog[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByProjectId($project_id, $limiters = [], $options = [])
 * @method string getSQLByProjectId($project_id, $limiters = [], $options = [])
 * @method \App\Models\Prizes\ZhuliLog findOneByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\ZhuliLog[] findByProjectId($project_id, $limiters = [], $options = [])
 * @method int countByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByPlayerId($player_id, $limiters = [], $options = [])
 * @method string getSQLByPlayerId($player_id, $limiters = [], $options = [])
 * @method \App\Models\Prizes\ZhuliLog findOneByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\ZhuliLog[] findByPlayerId($player_id, $limiters = [], $options = [])
 * @method int countByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByOpenid($openid, $limiters = [], $options = [])
 * @method string getSQLByOpenid($openid, $limiters = [], $options = [])
 * @method \App\Models\Prizes\ZhuliLog findOneByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\ZhuliLog[] findByOpenid($openid, $limiters = [], $options = [])
 * @method int countByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByZhuliName($zhuli_name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByZhuliName($zhuli_name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByZhuliName($zhuli_name, $limiters = [], $options = [])
 * @method string getSQLByZhuliName($zhuli_name, $limiters = [], $options = [])
 * @method \App\Models\Prizes\ZhuliLog findOneByZhuliName($zhuli_name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\ZhuliLog[] findByZhuliName($zhuli_name, $limiters = [], $options = [])
 * @method int countByZhuliName($zhuli_name, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByZhuliOpenid($zhuli_openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByZhuliOpenid($zhuli_openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByZhuliOpenid($zhuli_openid, $limiters = [], $options = [])
 * @method string getSQLByZhuliOpenid($zhuli_openid, $limiters = [], $options = [])
 * @method \App\Models\Prizes\ZhuliLog findOneByZhuliOpenid($zhuli_openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\ZhuliLog[] findByZhuliOpenid($zhuli_openid, $limiters = [], $options = [])
 * @method int countByZhuliOpenid($zhuli_openid, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method string getSQLByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \App\Models\Prizes\ZhuliLog findOneByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\ZhuliLog[] findByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method int countByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByIp($ip, $limiters = [], $options = [])
 * @method string getSQLByIp($ip, $limiters = [], $options = [])
 * @method \App\Models\Prizes\ZhuliLog findOneByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\ZhuliLog[] findByIp($ip, $limiters = [], $options = [])
 * @method int countByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByNote($note, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByNote($note, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByNote($note, $limiters = [], $options = [])
 * @method string getSQLByNote($note, $limiters = [], $options = [])
 * @method \App\Models\Prizes\ZhuliLog findOneByNote($note, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\ZhuliLog[] findByNote($note, $limiters = [], $options = [])
 * @method int countByNote($note, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Prizes\ZhuliLog findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\ZhuliLog[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Prizes\ZhuliLog findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\ZhuliLog[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Prizes\ZhuliLog findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\ZhuliLog[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Prizes\ZhuliLog findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\ZhuliLog[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Prizes\ZhuliLog retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Prizes\ZhuliLog[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class ZhuliLogRepository extends \Wanjia\Common\Database\Repository {}

}