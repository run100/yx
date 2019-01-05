<?php

namespace App\Models\Yx {

/**
 * App\Models\Yx\YxPurpose
 *
 * @property integer $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $sort
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxPurpose whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxPurpose whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxPurpose whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxPurpose whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxPurpose whereSort($value)
 * @method static \App\Models\Yx\YxPurposeRepository repository()
 */
	class YxPurpose extends \Eloquent {}


/**
 * YxPurposeRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxPurpose findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPurpose[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByName($name, $limiters = [], $options = [])
 * @method string getSQLByName($name, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxPurpose findOneByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPurpose[] findByName($name, $limiters = [], $options = [])
 * @method int countByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxPurpose findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPurpose[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxPurpose findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPurpose[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBySort($sort, $limiters = [], $options = [])
 * @method string getSQLBySort($sort, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxPurpose findOneBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPurpose[] findBySort($sort, $limiters = [], $options = [])
 * @method int countBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Yx\YxPurpose findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPurpose[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Yx\YxPurpose findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPurpose[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Yx\YxPurpose retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPurpose[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class YxPurposeRepository extends \Wanjia\Common\Database\Repository {}


/**
 * App\Models\Yx\YxFunc
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property boolean $is_feature
 * @property string $picture
 * @property string $background
 * @property string $desc
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $sort
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] $classicCases
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] $tempCases
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] $children
 * @property-read \App\Models\Yx\YxFunc $parent
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxFunc whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxFunc whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxFunc whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxFunc whereIsFeature($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxFunc wherePicture($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxFunc whereBackground($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxFunc whereDesc($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxFunc whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxFunc whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxFunc whereSort($value)
 * @method static \App\Models\Yx\YxFuncRepository repository()
 */
	class YxFunc extends \Eloquent {}


/**
 * YxFuncRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxFunc findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByName($name, $limiters = [], $options = [])
 * @method string getSQLByName($name, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxFunc findOneByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] findByName($name, $limiters = [], $options = [])
 * @method int countByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByParentId($parent_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByParentId($parent_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByParentId($parent_id, $limiters = [], $options = [])
 * @method string getSQLByParentId($parent_id, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxFunc findOneByParentId($parent_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] findByParentId($parent_id, $limiters = [], $options = [])
 * @method int countByParentId($parent_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByIsFeature($is_feature, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByIsFeature($is_feature, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByIsFeature($is_feature, $limiters = [], $options = [])
 * @method string getSQLByIsFeature($is_feature, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxFunc findOneByIsFeature($is_feature, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] findByIsFeature($is_feature, $limiters = [], $options = [])
 * @method int countByIsFeature($is_feature, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByPicture($picture, $limiters = [], $options = [])
 * @method string getSQLByPicture($picture, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxFunc findOneByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] findByPicture($picture, $limiters = [], $options = [])
 * @method int countByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByBackground($background, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByBackground($background, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByBackground($background, $limiters = [], $options = [])
 * @method string getSQLByBackground($background, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxFunc findOneByBackground($background, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] findByBackground($background, $limiters = [], $options = [])
 * @method int countByBackground($background, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByDesc($desc, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByDesc($desc, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByDesc($desc, $limiters = [], $options = [])
 * @method string getSQLByDesc($desc, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxFunc findOneByDesc($desc, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] findByDesc($desc, $limiters = [], $options = [])
 * @method int countByDesc($desc, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxFunc findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxFunc findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBySort($sort, $limiters = [], $options = [])
 * @method string getSQLBySort($sort, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxFunc findOneBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] findBySort($sort, $limiters = [], $options = [])
 * @method int countBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Yx\YxFunc findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Yx\YxFunc findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Yx\YxFunc retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class YxFuncRepository extends \Wanjia\Common\Database\Repository {}


/**
 * App\Models\Yx\YxBanner
 *
 * @property int $id
 * @property string|null $picture
 * @property int $category
 * @property string|null $url
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner wherePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner whereUrl($value)
 * @mixin \Eloquent
 * @method static \App\Models\Yx\YxBannerRepository repository()
 */
	class YxBanner extends \Eloquent {}


/**
 * App\Models\Yx\YxBanner
 *
 * @property int $id
 * @property string|null $picture
 * @property int $category
 * @property string|null $url
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner wherePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner whereUrl($value)
 * @mixin \Eloquent
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxBanner findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBanner[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByPicture($picture, $limiters = [], $options = [])
 * @method string getSQLByPicture($picture, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxBanner findOneByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBanner[] findByPicture($picture, $limiters = [], $options = [])
 * @method int countByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCategory($category, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCategory($category, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCategory($category, $limiters = [], $options = [])
 * @method string getSQLByCategory($category, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxBanner findOneByCategory($category, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBanner[] findByCategory($category, $limiters = [], $options = [])
 * @method int countByCategory($category, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUrl($url, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUrl($url, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUrl($url, $limiters = [], $options = [])
 * @method string getSQLByUrl($url, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxBanner findOneByUrl($url, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBanner[] findByUrl($url, $limiters = [], $options = [])
 * @method int countByUrl($url, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxBanner findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBanner[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxBanner findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBanner[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Yx\YxBanner findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBanner[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Yx\YxBanner findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBanner[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Yx\YxBanner retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBanner[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class YxBannerRepository extends \Wanjia\Common\Database\Repository {}


/**
 * App\Models\Yx\YxBusiness
 *
 * @property integer $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $sort
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxBusiness whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxBusiness whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxBusiness whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxBusiness whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxBusiness whereSort($value)
 * @method static \App\Models\Yx\YxBusinessRepository repository()
 */
	class YxBusiness extends \Eloquent {}


/**
 * YxBusinessRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxBusiness findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBusiness[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByName($name, $limiters = [], $options = [])
 * @method string getSQLByName($name, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxBusiness findOneByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBusiness[] findByName($name, $limiters = [], $options = [])
 * @method int countByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxBusiness findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBusiness[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxBusiness findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBusiness[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBySort($sort, $limiters = [], $options = [])
 * @method string getSQLBySort($sort, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxBusiness findOneBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBusiness[] findBySort($sort, $limiters = [], $options = [])
 * @method int countBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Yx\YxBusiness findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBusiness[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Yx\YxBusiness findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBusiness[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Yx\YxBusiness retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxBusiness[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class YxBusinessRepository extends \Wanjia\Common\Database\Repository {}


/**
 * App\Models\Yx\YxClassicCase
 *
 * @property integer $id
 * @property string $name
 * @property string $desc
 * @property string $keywords
 * @property string $url
 * @property string $cases
 * @property string $banner
 * @property string $picture
 * @property integer $business_id
 * @property integer $purpose_id
 * @property integer $attentions
 * @property integer $participants
 * @property string $flow1
 * @property string $flow2
 * @property string $flow3
 * @property string $flow4
 * @property boolean $is_top
 * @property boolean $is_index
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Yx\YxBusiness $business
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] $funcs
 * @property-read \App\Models\Yx\YxPurpose $purpose
 * @property-read mixed $qrcode
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereDesc($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereKeywords($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereCases($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereBanner($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase wherePicture($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereBusinessId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase wherePurposeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereAttentions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereParticipants($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereFlow1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereFlow2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereFlow3($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereFlow4($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereIsTop($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereIsIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxClassicCase whereUpdatedAt($value)
 * @method static \App\Models\Yx\YxClassicCaseRepository repository()
 */
	class YxClassicCase extends \Eloquent {}


/**
 * YxClassicCaseRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByName($name, $limiters = [], $options = [])
 * @method string getSQLByName($name, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByName($name, $limiters = [], $options = [])
 * @method int countByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByDesc($desc, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByDesc($desc, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByDesc($desc, $limiters = [], $options = [])
 * @method string getSQLByDesc($desc, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByDesc($desc, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByDesc($desc, $limiters = [], $options = [])
 * @method int countByDesc($desc, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByKeywords($keywords, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByKeywords($keywords, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByKeywords($keywords, $limiters = [], $options = [])
 * @method string getSQLByKeywords($keywords, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByKeywords($keywords, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByKeywords($keywords, $limiters = [], $options = [])
 * @method int countByKeywords($keywords, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUrl($url, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUrl($url, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUrl($url, $limiters = [], $options = [])
 * @method string getSQLByUrl($url, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByUrl($url, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByUrl($url, $limiters = [], $options = [])
 * @method int countByUrl($url, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCases($cases, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCases($cases, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCases($cases, $limiters = [], $options = [])
 * @method string getSQLByCases($cases, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByCases($cases, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByCases($cases, $limiters = [], $options = [])
 * @method int countByCases($cases, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByBanner($banner, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByBanner($banner, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByBanner($banner, $limiters = [], $options = [])
 * @method string getSQLByBanner($banner, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByBanner($banner, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByBanner($banner, $limiters = [], $options = [])
 * @method int countByBanner($banner, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByPicture($picture, $limiters = [], $options = [])
 * @method string getSQLByPicture($picture, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByPicture($picture, $limiters = [], $options = [])
 * @method int countByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByBusinessId($business_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByBusinessId($business_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByBusinessId($business_id, $limiters = [], $options = [])
 * @method string getSQLByBusinessId($business_id, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByBusinessId($business_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByBusinessId($business_id, $limiters = [], $options = [])
 * @method int countByBusinessId($business_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByPurposeId($purpose_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByPurposeId($purpose_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByPurposeId($purpose_id, $limiters = [], $options = [])
 * @method string getSQLByPurposeId($purpose_id, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByPurposeId($purpose_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByPurposeId($purpose_id, $limiters = [], $options = [])
 * @method int countByPurposeId($purpose_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByAttentions($attentions, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByAttentions($attentions, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByAttentions($attentions, $limiters = [], $options = [])
 * @method string getSQLByAttentions($attentions, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByAttentions($attentions, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByAttentions($attentions, $limiters = [], $options = [])
 * @method int countByAttentions($attentions, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByParticipants($participants, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByParticipants($participants, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByParticipants($participants, $limiters = [], $options = [])
 * @method string getSQLByParticipants($participants, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByParticipants($participants, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByParticipants($participants, $limiters = [], $options = [])
 * @method int countByParticipants($participants, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByFlow1($flow1, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByFlow1($flow1, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByFlow1($flow1, $limiters = [], $options = [])
 * @method string getSQLByFlow1($flow1, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByFlow1($flow1, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByFlow1($flow1, $limiters = [], $options = [])
 * @method int countByFlow1($flow1, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByFlow2($flow2, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByFlow2($flow2, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByFlow2($flow2, $limiters = [], $options = [])
 * @method string getSQLByFlow2($flow2, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByFlow2($flow2, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByFlow2($flow2, $limiters = [], $options = [])
 * @method int countByFlow2($flow2, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByFlow3($flow3, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByFlow3($flow3, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByFlow3($flow3, $limiters = [], $options = [])
 * @method string getSQLByFlow3($flow3, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByFlow3($flow3, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByFlow3($flow3, $limiters = [], $options = [])
 * @method int countByFlow3($flow3, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByFlow4($flow4, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByFlow4($flow4, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByFlow4($flow4, $limiters = [], $options = [])
 * @method string getSQLByFlow4($flow4, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByFlow4($flow4, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByFlow4($flow4, $limiters = [], $options = [])
 * @method int countByFlow4($flow4, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByIsTop($is_top, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByIsTop($is_top, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByIsTop($is_top, $limiters = [], $options = [])
 * @method string getSQLByIsTop($is_top, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByIsTop($is_top, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByIsTop($is_top, $limiters = [], $options = [])
 * @method int countByIsTop($is_top, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByIsIndex($is_index, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByIsIndex($is_index, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByIsIndex($is_index, $limiters = [], $options = [])
 * @method string getSQLByIsIndex($is_index, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByIsIndex($is_index, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByIsIndex($is_index, $limiters = [], $options = [])
 * @method int countByIsIndex($is_index, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Yx\YxClassicCase retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxClassicCase[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class YxClassicCaseRepository extends \Wanjia\Common\Database\Repository {}


/**
 * App\Models\Yx\YxPartner
 *
 * @property integer $id
 * @property string $name
 * @property integer $sort
 * @property string $picture
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxPartner whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxPartner whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxPartner whereSort($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxPartner wherePicture($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxPartner whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxPartner whereUpdatedAt($value)
 * @method static \App\Models\Yx\YxPartnerRepository repository()
 */
	class YxPartner extends \Eloquent {}


/**
 * YxPartnerRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxPartner findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPartner[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByName($name, $limiters = [], $options = [])
 * @method string getSQLByName($name, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxPartner findOneByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPartner[] findByName($name, $limiters = [], $options = [])
 * @method int countByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBySort($sort, $limiters = [], $options = [])
 * @method string getSQLBySort($sort, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxPartner findOneBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPartner[] findBySort($sort, $limiters = [], $options = [])
 * @method int countBySort($sort, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByPicture($picture, $limiters = [], $options = [])
 * @method string getSQLByPicture($picture, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxPartner findOneByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPartner[] findByPicture($picture, $limiters = [], $options = [])
 * @method int countByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxPartner findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPartner[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxPartner findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPartner[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Yx\YxPartner findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPartner[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Yx\YxPartner findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPartner[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Yx\YxPartner retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxPartner[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class YxPartnerRepository extends \Wanjia\Common\Database\Repository {}


/**
 * App\Models\Yx\YxTempCase
 *
 * @property integer $id
 * @property string $name
 * @property string $desc
 * @property string $keywords
 * @property string $url
 * @property string $cases
 * @property string $picture
 * @property integer $business_id
 * @property integer $purpose_id
 * @property boolean $is_top
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Yx\YxBusiness $business
 * @property-read \App\Models\Yx\YxPurpose $purpose
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxFunc[] $funcs
 * @property-read mixed $qrcode
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxTempCase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxTempCase whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxTempCase whereDesc($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxTempCase whereKeywords($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxTempCase whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxTempCase whereCases($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxTempCase wherePicture($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxTempCase whereBusinessId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxTempCase wherePurposeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxTempCase whereIsTop($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxTempCase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Yx\YxTempCase whereUpdatedAt($value)
 * @method static \App\Models\Yx\YxTempCaseRepository repository()
 */
	class YxTempCase extends \Eloquent {}


/**
 * YxTempCaseRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxTempCase findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByName($name, $limiters = [], $options = [])
 * @method string getSQLByName($name, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxTempCase findOneByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] findByName($name, $limiters = [], $options = [])
 * @method int countByName($name, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByDesc($desc, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByDesc($desc, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByDesc($desc, $limiters = [], $options = [])
 * @method string getSQLByDesc($desc, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxTempCase findOneByDesc($desc, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] findByDesc($desc, $limiters = [], $options = [])
 * @method int countByDesc($desc, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByKeywords($keywords, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByKeywords($keywords, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByKeywords($keywords, $limiters = [], $options = [])
 * @method string getSQLByKeywords($keywords, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxTempCase findOneByKeywords($keywords, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] findByKeywords($keywords, $limiters = [], $options = [])
 * @method int countByKeywords($keywords, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUrl($url, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUrl($url, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUrl($url, $limiters = [], $options = [])
 * @method string getSQLByUrl($url, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxTempCase findOneByUrl($url, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] findByUrl($url, $limiters = [], $options = [])
 * @method int countByUrl($url, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCases($cases, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCases($cases, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCases($cases, $limiters = [], $options = [])
 * @method string getSQLByCases($cases, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxTempCase findOneByCases($cases, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] findByCases($cases, $limiters = [], $options = [])
 * @method int countByCases($cases, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByPicture($picture, $limiters = [], $options = [])
 * @method string getSQLByPicture($picture, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxTempCase findOneByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] findByPicture($picture, $limiters = [], $options = [])
 * @method int countByPicture($picture, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByBusinessId($business_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByBusinessId($business_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByBusinessId($business_id, $limiters = [], $options = [])
 * @method string getSQLByBusinessId($business_id, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxTempCase findOneByBusinessId($business_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] findByBusinessId($business_id, $limiters = [], $options = [])
 * @method int countByBusinessId($business_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByPurposeId($purpose_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByPurposeId($purpose_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByPurposeId($purpose_id, $limiters = [], $options = [])
 * @method string getSQLByPurposeId($purpose_id, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxTempCase findOneByPurposeId($purpose_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] findByPurposeId($purpose_id, $limiters = [], $options = [])
 * @method int countByPurposeId($purpose_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByIsTop($is_top, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByIsTop($is_top, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByIsTop($is_top, $limiters = [], $options = [])
 * @method string getSQLByIsTop($is_top, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxTempCase findOneByIsTop($is_top, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] findByIsTop($is_top, $limiters = [], $options = [])
 * @method int countByIsTop($is_top, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxTempCase findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\Yx\YxTempCase findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\Yx\YxTempCase findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\Yx\YxTempCase findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\Yx\YxTempCase retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\Yx\YxTempCase[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class YxTempCaseRepository extends \Wanjia\Common\Database\Repository {}

}