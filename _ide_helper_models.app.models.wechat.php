<?php

namespace App\Models\Wechat {

/**
 * App\Models\Wechat\Material
 *
 * @property integer $id
 * @property integer $merchant_id
 * @property string $media_id
 * @property string $file
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Merchant $merchant
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Material whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Material whereMerchantId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Material whereMediaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Material whereFile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Material whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Material whereUpdatedAt($value)
 * @method static \App\Models\Wechat\MaterialRepository repository()
 */
	class Material extends \Eloquent {}


/**
 * MaterialRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Material findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Material[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method string getSQLByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Material findOneByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Material[] findByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method int countByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByMediaId($media_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByMediaId($media_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByMediaId($media_id, $limiters = [], $options = [])
 * @method string getSQLByMediaId($media_id, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Material findOneByMediaId($media_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Material[] findByMediaId($media_id, $limiters = [], $options = [])
 * @method int countByMediaId($media_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByFile($file, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByFile($file, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByFile($file, $limiters = [], $options = [])
 * @method string getSQLByFile($file, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Material findOneByFile($file, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Material[] findByFile($file, $limiters = [], $options = [])
 * @method int countByFile($file, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Material findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Material[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Material findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Material[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Wechat\Material findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Material[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Wechat\Material findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Material[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Wechat\Material retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Material[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class MaterialRepository extends \Wanjia\Common\Database\Repository {}


/**
 * App\Models\Wechat\Menu
 *
 * @property integer $id
 * @property integer $merchant_id
 * @property integer $parent_id
 * @property integer $order
 * @property string $title
 * @property string $uri
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $type
 * @property string $target
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Menu[] $children
 * @property-read \App\Models\Wechat\Menu $parent
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Menu whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Menu whereMerchantId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Menu whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Menu whereOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Menu whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Menu whereUri($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Menu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Menu whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\Menu whereTarget($value)
 * @method static \App\Models\Wechat\MenuRepository repository()
 */
	class Menu extends \Eloquent {}


/**
 * MenuRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Menu findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Menu[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method string getSQLByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Menu findOneByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Menu[] findByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method int countByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByParentId($parent_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByParentId($parent_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByParentId($parent_id, $limiters = [], $options = [])
 * @method string getSQLByParentId($parent_id, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Menu findOneByParentId($parent_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Menu[] findByParentId($parent_id, $limiters = [], $options = [])
 * @method int countByParentId($parent_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByOrder($order, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByOrder($order, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByOrder($order, $limiters = [], $options = [])
 * @method string getSQLByOrder($order, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Menu findOneByOrder($order, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Menu[] findByOrder($order, $limiters = [], $options = [])
 * @method int countByOrder($order, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByTitle($title, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByTitle($title, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByTitle($title, $limiters = [], $options = [])
 * @method string getSQLByTitle($title, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Menu findOneByTitle($title, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Menu[] findByTitle($title, $limiters = [], $options = [])
 * @method int countByTitle($title, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUri($uri, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUri($uri, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUri($uri, $limiters = [], $options = [])
 * @method string getSQLByUri($uri, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Menu findOneByUri($uri, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Menu[] findByUri($uri, $limiters = [], $options = [])
 * @method int countByUri($uri, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Menu findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Menu[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Menu findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Menu[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByType($type, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByType($type, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByType($type, $limiters = [], $options = [])
 * @method string getSQLByType($type, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Menu findOneByType($type, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Menu[] findByType($type, $limiters = [], $options = [])
 * @method int countByType($type, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByTarget($target, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByTarget($target, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByTarget($target, $limiters = [], $options = [])
 * @method string getSQLByTarget($target, $limiters = [], $options = [])
 * @method \App\Models\Wechat\Menu findOneByTarget($target, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Menu[] findByTarget($target, $limiters = [], $options = [])
 * @method int countByTarget($target, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Wechat\Menu findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Menu[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Wechat\Menu findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Menu[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Wechat\Menu retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\Menu[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class MenuRepository extends \Wanjia\Common\Database\Repository {}


/**
 * App\Models\Wechat\AutoReply
 *
 * @property integer $id
 * @property integer $merchant_id
 * @property string $match_mode
 * @property string $keyword
 * @property string $reply_mode
 * @property string $reply
 * @property string $start_at
 * @property string $end_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $tmode
 * @property-read mixed $rmode
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\AutoReply whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\AutoReply whereMerchantId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\AutoReply whereMatchMode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\AutoReply whereKeyword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\AutoReply whereReplyMode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\AutoReply whereReply($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\AutoReply whereStartAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\AutoReply whereEndAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\AutoReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Wechat\AutoReply whereUpdatedAt($value)
 * @method static \App\Models\Wechat\AutoReplyRepository repository()
 */
	class AutoReply extends \Eloquent {}


/**
 * AutoReplyRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Wechat\AutoReply findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\AutoReply[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method string getSQLByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \App\Models\Wechat\AutoReply findOneByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\AutoReply[] findByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method int countByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByMatchMode($match_mode, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByMatchMode($match_mode, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByMatchMode($match_mode, $limiters = [], $options = [])
 * @method string getSQLByMatchMode($match_mode, $limiters = [], $options = [])
 * @method \App\Models\Wechat\AutoReply findOneByMatchMode($match_mode, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\AutoReply[] findByMatchMode($match_mode, $limiters = [], $options = [])
 * @method int countByMatchMode($match_mode, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByKeyword($keyword, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByKeyword($keyword, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByKeyword($keyword, $limiters = [], $options = [])
 * @method string getSQLByKeyword($keyword, $limiters = [], $options = [])
 * @method \App\Models\Wechat\AutoReply findOneByKeyword($keyword, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\AutoReply[] findByKeyword($keyword, $limiters = [], $options = [])
 * @method int countByKeyword($keyword, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByReplyMode($reply_mode, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByReplyMode($reply_mode, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByReplyMode($reply_mode, $limiters = [], $options = [])
 * @method string getSQLByReplyMode($reply_mode, $limiters = [], $options = [])
 * @method \App\Models\Wechat\AutoReply findOneByReplyMode($reply_mode, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\AutoReply[] findByReplyMode($reply_mode, $limiters = [], $options = [])
 * @method int countByReplyMode($reply_mode, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByReply($reply, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByReply($reply, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByReply($reply, $limiters = [], $options = [])
 * @method string getSQLByReply($reply, $limiters = [], $options = [])
 * @method \App\Models\Wechat\AutoReply findOneByReply($reply, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\AutoReply[] findByReply($reply, $limiters = [], $options = [])
 * @method int countByReply($reply, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByStartAt($start_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByStartAt($start_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByStartAt($start_at, $limiters = [], $options = [])
 * @method string getSQLByStartAt($start_at, $limiters = [], $options = [])
 * @method \App\Models\Wechat\AutoReply findOneByStartAt($start_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\AutoReply[] findByStartAt($start_at, $limiters = [], $options = [])
 * @method int countByStartAt($start_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByEndAt($end_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByEndAt($end_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByEndAt($end_at, $limiters = [], $options = [])
 * @method string getSQLByEndAt($end_at, $limiters = [], $options = [])
 * @method \App\Models\Wechat\AutoReply findOneByEndAt($end_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\AutoReply[] findByEndAt($end_at, $limiters = [], $options = [])
 * @method int countByEndAt($end_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Wechat\AutoReply findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\AutoReply[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Wechat\AutoReply findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\AutoReply[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Wechat\AutoReply findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\AutoReply[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Wechat\AutoReply findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\AutoReply[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Wechat\AutoReply retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Wechat\AutoReply[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class AutoReplyRepository extends \Wanjia\Common\Database\Repository {}

}