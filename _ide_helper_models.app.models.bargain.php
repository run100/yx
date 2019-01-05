<?php

namespace App\Models\Bargain {

/**
 * App\Models\Bargain\BargainLog
 *
 * @property integer $id
 * @property integer $project_id 项目ID
 * @property integer $merchant_id 客户ID
 * @property integer $player_id 选手ID
 * @property float $price 砍掉的价格
 * @property integer $operator_uid 操作者
 * @property string $ip 来源IP
 * @property string $openid 邀请人openid
 * @property string $name 邀请人昵称
 * @property string $zhuli_openid 助力者openid
 * @property string $zhuli_name 助力者昵称
 * @property string $note 备注
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain\BargainLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain\BargainLog whereProjectId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain\BargainLog whereMerchantId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain\BargainLog wherePlayerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain\BargainLog wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain\BargainLog whereOperatorUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain\BargainLog whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain\BargainLog whereOpenid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain\BargainLog whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain\BargainLog whereZhuliOpenid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain\BargainLog whereZhuliName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain\BargainLog whereNote($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain\BargainLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain\BargainLog whereUpdatedAt($value)
 * @method static \App\Models\Bargain\BargainLogRepository repository()
 */
	class BargainLog extends \Eloquent {}


/**
 * BargainLogRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByProjectId($project_id, $limiters = [], $options = [])
 * @method string getSQLByProjectId($project_id, $limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOneByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] findByProjectId($project_id, $limiters = [], $options = [])
 * @method int countByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method string getSQLByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOneByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] findByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method int countByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByPlayerId($player_id, $limiters = [], $options = [])
 * @method string getSQLByPlayerId($player_id, $limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOneByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] findByPlayerId($player_id, $limiters = [], $options = [])
 * @method int countByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByPrice($price, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByPrice($price, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByPrice($price, $limiters = [], $options = [])
 * @method string getSQLByPrice($price, $limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOneByPrice($price, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] findByPrice($price, $limiters = [], $options = [])
 * @method int countByPrice($price, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method string getSQLByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOneByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] findByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method int countByOperatorUid($operator_uid, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByIp($ip, $limiters = [], $options = [])
 * @method string getSQLByIp($ip, $limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOneByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] findByIp($ip, $limiters = [], $options = [])
 * @method int countByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByOpenid($openid, $limiters = [], $options = [])
 * @method string getSQLByOpenid($openid, $limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOneByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] findByOpenid($openid, $limiters = [], $options = [])
 * @method int countByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByName($name, $limiters = [], $options = [])
 * @method string getSQLByName($name, $limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOneByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] findByName($name, $limiters = [], $options = [])
 * @method int countByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByZhuliOpenid($zhuli_openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByZhuliOpenid($zhuli_openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByZhuliOpenid($zhuli_openid, $limiters = [], $options = [])
 * @method string getSQLByZhuliOpenid($zhuli_openid, $limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOneByZhuliOpenid($zhuli_openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] findByZhuliOpenid($zhuli_openid, $limiters = [], $options = [])
 * @method int countByZhuliOpenid($zhuli_openid, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByZhuliName($zhuli_name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByZhuliName($zhuli_name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByZhuliName($zhuli_name, $limiters = [], $options = [])
 * @method string getSQLByZhuliName($zhuli_name, $limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOneByZhuliName($zhuli_name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] findByZhuliName($zhuli_name, $limiters = [], $options = [])
 * @method int countByZhuliName($zhuli_name, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByNote($note, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByNote($note, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByNote($note, $limiters = [], $options = [])
 * @method string getSQLByNote($note, $limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOneByNote($note, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] findByNote($note, $limiters = [], $options = [])
 * @method int countByNote($note, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Bargain\BargainLog retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Bargain\BargainLog[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class BargainLogRepository extends \Wanjia\Common\Database\Repository {}

}