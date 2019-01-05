<?php

namespace App\Model {

/**
 * App\Model\UserLoginLog
 *
 * @method static \App\Model\UserLoginLogRepository repository()
 */
	class UserLoginLog extends \Eloquent {}


/**
 * UserLoginLogRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Model\UserLoginLog findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Model\UserLoginLog[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Model\UserLoginLog findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Model\UserLoginLog[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Model\UserLoginLog retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Model\UserLoginLog[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class UserLoginLogRepository extends \Wanjia\Common\Database\Repository {}

}