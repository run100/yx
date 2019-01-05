<?php

namespace App\Models\Hongbao {

/**
 * App\Models\Hongbao\HongbaoBilling
 *
 * @property integer $id
 * @property integer $project_id
 * @property string $bill_no 订单号
 * @property string $wx_no 微信订单号
 * @property string $openid 用户openid
 * @property float $money 红包金额
 * @property boolean $is_error 状态
 * @property string $data 微信返回数据
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoBilling whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoBilling whereProjectId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoBilling whereBillNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoBilling whereWxNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoBilling whereOpenid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoBilling whereMoney($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoBilling whereIsError($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoBilling whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoBilling whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoBilling whereUpdatedAt($value)
 * @method static \App\Models\Hongbao\HongbaoBillingRepository repository()
 */
	class HongbaoBilling extends \Eloquent {}


/**
 * HongbaoBillingRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoBilling findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoBilling[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByProjectId($project_id, $limiters = [], $options = [])
 * @method string getSQLByProjectId($project_id, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoBilling findOneByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoBilling[] findByProjectId($project_id, $limiters = [], $options = [])
 * @method int countByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByBillNo($bill_no, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByBillNo($bill_no, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByBillNo($bill_no, $limiters = [], $options = [])
 * @method string getSQLByBillNo($bill_no, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoBilling findOneByBillNo($bill_no, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoBilling[] findByBillNo($bill_no, $limiters = [], $options = [])
 * @method int countByBillNo($bill_no, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByWxNo($wx_no, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByWxNo($wx_no, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByWxNo($wx_no, $limiters = [], $options = [])
 * @method string getSQLByWxNo($wx_no, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoBilling findOneByWxNo($wx_no, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoBilling[] findByWxNo($wx_no, $limiters = [], $options = [])
 * @method int countByWxNo($wx_no, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByOpenid($openid, $limiters = [], $options = [])
 * @method string getSQLByOpenid($openid, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoBilling findOneByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoBilling[] findByOpenid($openid, $limiters = [], $options = [])
 * @method int countByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByMoney($money, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByMoney($money, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByMoney($money, $limiters = [], $options = [])
 * @method string getSQLByMoney($money, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoBilling findOneByMoney($money, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoBilling[] findByMoney($money, $limiters = [], $options = [])
 * @method int countByMoney($money, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByIsError($is_error, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByIsError($is_error, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByIsError($is_error, $limiters = [], $options = [])
 * @method string getSQLByIsError($is_error, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoBilling findOneByIsError($is_error, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoBilling[] findByIsError($is_error, $limiters = [], $options = [])
 * @method int countByIsError($is_error, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByData($data, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByData($data, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByData($data, $limiters = [], $options = [])
 * @method string getSQLByData($data, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoBilling findOneByData($data, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoBilling[] findByData($data, $limiters = [], $options = [])
 * @method int countByData($data, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoBilling findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoBilling[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoBilling findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoBilling[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoBilling findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoBilling[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoBilling findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoBilling[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoBilling retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoBilling[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class HongbaoBillingRepository extends \Wanjia\Common\Database\Repository {}


/**
 * App\Models\Hongbao\HongbaoLog
 *
 * @property integer $id
 * @property integer $project_id
 * @property integer $player_id
 * @property string $openid 用户openid
 * @property string $ip 来源IP
 * @property string $wx_name 微信昵称
 * @property boolean $is_win 是否中奖
 * @property float $money 中奖金额
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $win_text
 * @property-read \App\Models\Player $player
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoLog whereProjectId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoLog wherePlayerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoLog whereOpenid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoLog whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoLog whereWxName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoLog whereIsWin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoLog whereMoney($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Hongbao\HongbaoLog whereUpdatedAt($value)
 * @method static \App\Models\Hongbao\HongbaoLogRepository repository()
 */
	class HongbaoLog extends \Eloquent {}


/**
 * HongbaoLogRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoLog findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoLog[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByProjectId($project_id, $limiters = [], $options = [])
 * @method string getSQLByProjectId($project_id, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoLog findOneByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoLog[] findByProjectId($project_id, $limiters = [], $options = [])
 * @method int countByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByPlayerId($player_id, $limiters = [], $options = [])
 * @method string getSQLByPlayerId($player_id, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoLog findOneByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoLog[] findByPlayerId($player_id, $limiters = [], $options = [])
 * @method int countByPlayerId($player_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByOpenid($openid, $limiters = [], $options = [])
 * @method string getSQLByOpenid($openid, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoLog findOneByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoLog[] findByOpenid($openid, $limiters = [], $options = [])
 * @method int countByOpenid($openid, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByIp($ip, $limiters = [], $options = [])
 * @method string getSQLByIp($ip, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoLog findOneByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoLog[] findByIp($ip, $limiters = [], $options = [])
 * @method int countByIp($ip, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByWxName($wx_name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByWxName($wx_name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByWxName($wx_name, $limiters = [], $options = [])
 * @method string getSQLByWxName($wx_name, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoLog findOneByWxName($wx_name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoLog[] findByWxName($wx_name, $limiters = [], $options = [])
 * @method int countByWxName($wx_name, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByIsWin($is_win, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByIsWin($is_win, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByIsWin($is_win, $limiters = [], $options = [])
 * @method string getSQLByIsWin($is_win, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoLog findOneByIsWin($is_win, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoLog[] findByIsWin($is_win, $limiters = [], $options = [])
 * @method int countByIsWin($is_win, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByMoney($money, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByMoney($money, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByMoney($money, $limiters = [], $options = [])
 * @method string getSQLByMoney($money, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoLog findOneByMoney($money, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoLog[] findByMoney($money, $limiters = [], $options = [])
 * @method int countByMoney($money, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoLog findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoLog[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoLog findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoLog[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoLog findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoLog[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoLog findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoLog[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Hongbao\HongbaoLog retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Hongbao\HongbaoLog[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class HongbaoLogRepository extends \Wanjia\Common\Database\Repository {}

}