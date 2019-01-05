<?php

namespace App\Models\Jizi {

/**
 * App\Models\Jizi\JiziLog
 *
 * @property integer $id
 * @property integer $project_id 项目ID
 * @property integer $merchant_id 客户ID
 * @property integer $player_id 选手ID
 * @property integer $send_id 赠送者 ID
 * @property integer $operator_uid 操作者
 * @property string $ip 来源IP
 * @property string $openid 来源OpenID
 * @property string $field 字段
 * @property string $content 集字的内容
 * @property string $note 备注
 * @property boolean $type 0加、1减
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Player $player
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Jizi\JiziLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Jizi\JiziLog whereProjectId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Jizi\JiziLog whereMerchantId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Jizi\JiziLog wherePlayerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Jizi\JiziLog whereSendId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Jizi\JiziLog whereOperatorUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Jizi\JiziLog whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Jizi\JiziLog whereOpenid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Jizi\JiziLog whereField($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Jizi\JiziLog whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Jizi\JiziLog whereNote($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Jizi\JiziLog whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Jizi\JiziLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Jizi\JiziLog whereUpdatedAt($value)
 * @method static \App\Models\Jizi\JiziLogRepository repository()
 */
	class JiziLog extends \Eloquent {}


/**
 * JiziLogRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByProjectId($project_id, $limiters = [], $options = [])
 * @method string getSQLByProjectId($project_id, $limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOneByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] findByProjectId($project_id, $limiters = [], $options = [])
 * @method int countByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method string getSQLByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOneByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] findByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method int countByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByPlayerId($player_id, $limiters = [], $options = [])
 * @method string getSQLByPlayerId($player_id, $limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOneByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] findByPlayerId($player_id, $limiters = [], $options = [])
 * @method int countByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBySendId($send_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBySendId($send_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBySendId($send_id, $limiters = [], $options = [])
 * @method string getSQLBySendId($send_id, $limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOneBySendId($send_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] findBySendId($send_id, $limiters = [], $options = [])
 * @method int countBySendId($send_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method string getSQLByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOneByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] findByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method int countByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByIp($ip, $limiters = [], $options = [])
 * @method string getSQLByIp($ip, $limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOneByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] findByIp($ip, $limiters = [], $options = [])
 * @method int countByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByOpenid($openid, $limiters = [], $options = [])
 * @method string getSQLByOpenid($openid, $limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOneByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] findByOpenid($openid, $limiters = [], $options = [])
 * @method int countByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByField($field, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByField($field, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByField($field, $limiters = [], $options = [])
 * @method string getSQLByField($field, $limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOneByField($field, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] findByField($field, $limiters = [], $options = [])
 * @method int countByField($field, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByContent($content, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByContent($content, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByContent($content, $limiters = [], $options = [])
 * @method string getSQLByContent($content, $limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOneByContent($content, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] findByContent($content, $limiters = [], $options = [])
 * @method int countByContent($content, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByNote($note, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByNote($note, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByNote($note, $limiters = [], $options = [])
 * @method string getSQLByNote($note, $limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOneByNote($note, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] findByNote($note, $limiters = [], $options = [])
 * @method int countByNote($note, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByType($type, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByType($type, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByType($type, $limiters = [], $options = [])
 * @method string getSQLByType($type, $limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOneByType($type, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] findByType($type, $limiters = [], $options = [])
 * @method int countByType($type, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Jizi\JiziLog retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Jizi\JiziLog[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class JiziLogRepository extends \Wanjia\Common\Database\Repository {}

}