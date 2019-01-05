<?php

namespace Wanjia\Common\Database {

/**
 * DefaultRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Model findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 */
	 abstract class DefaultRepository extends \Wanjia\Common\Database\Repository {}

}