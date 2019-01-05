<?php

namespace App\Models\News {

/**
 * App\Models\News\Blocks
 *
 * @property integer $id
 * @property integer $project_id 项目ID
 * @property integer $merchant_id 客户ID
 * @property string $channel_id
 * @property integer $block_id 板块ID
 * @property string $block1
 * @property string $block2
 * @property string $block3
 * @property string $block4
 * @property string $block5
 * @property string $block6
 * @property string $block7
 * @property string $block8
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Project $myproject
 * @method static \Illuminate\Database\Query\Builder|\App\Models\News\Blocks whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\News\Blocks whereProjectId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\News\Blocks whereMerchantId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\News\Blocks whereChannelId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\News\Blocks whereBlockId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\News\Blocks whereBlock1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\News\Blocks whereBlock2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\News\Blocks whereBlock3($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\News\Blocks whereBlock4($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\News\Blocks whereBlock5($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\News\Blocks whereBlock6($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\News\Blocks whereBlock7($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\News\Blocks whereBlock8($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\News\Blocks whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\News\Blocks whereUpdatedAt($value)
 * @method static \App\Models\News\BlocksRepository repository()
 */
	class Blocks extends \Eloquent {}


/**
 * BlocksRepository
 *
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderById($id, $limiters = [], $options = [])
 * @method string getSQLById($id, $limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneById($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findById($id, $limiters = [], $options = [])
 * @method int countById($id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByProjectId($project_id, $limiters = [], $options = [])
 * @method string getSQLByProjectId($project_id, $limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findByProjectId($project_id, $limiters = [], $options = [])
 * @method int countByProjectId($project_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method string getSQLByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method int countByMerchantId($merchant_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByChannelId($channel_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByChannelId($channel_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByChannelId($channel_id, $limiters = [], $options = [])
 * @method string getSQLByChannelId($channel_id, $limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneByChannelId($channel_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findByChannelId($channel_id, $limiters = [], $options = [])
 * @method int countByChannelId($channel_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByBlockId($block_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByBlockId($block_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByBlockId($block_id, $limiters = [], $options = [])
 * @method string getSQLByBlockId($block_id, $limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneByBlockId($block_id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findByBlockId($block_id, $limiters = [], $options = [])
 * @method int countByBlockId($block_id, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByBlock1($block1, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByBlock1($block1, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByBlock1($block1, $limiters = [], $options = [])
 * @method string getSQLByBlock1($block1, $limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneByBlock1($block1, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findByBlock1($block1, $limiters = [], $options = [])
 * @method int countByBlock1($block1, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByBlock2($block2, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByBlock2($block2, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByBlock2($block2, $limiters = [], $options = [])
 * @method string getSQLByBlock2($block2, $limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneByBlock2($block2, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findByBlock2($block2, $limiters = [], $options = [])
 * @method int countByBlock2($block2, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByBlock3($block3, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByBlock3($block3, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByBlock3($block3, $limiters = [], $options = [])
 * @method string getSQLByBlock3($block3, $limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneByBlock3($block3, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findByBlock3($block3, $limiters = [], $options = [])
 * @method int countByBlock3($block3, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByBlock4($block4, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByBlock4($block4, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByBlock4($block4, $limiters = [], $options = [])
 * @method string getSQLByBlock4($block4, $limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneByBlock4($block4, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findByBlock4($block4, $limiters = [], $options = [])
 * @method int countByBlock4($block4, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByBlock5($block5, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByBlock5($block5, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByBlock5($block5, $limiters = [], $options = [])
 * @method string getSQLByBlock5($block5, $limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneByBlock5($block5, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findByBlock5($block5, $limiters = [], $options = [])
 * @method int countByBlock5($block5, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByBlock6($block6, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByBlock6($block6, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByBlock6($block6, $limiters = [], $options = [])
 * @method string getSQLByBlock6($block6, $limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneByBlock6($block6, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findByBlock6($block6, $limiters = [], $options = [])
 * @method int countByBlock6($block6, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByBlock7($block7, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByBlock7($block7, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByBlock7($block7, $limiters = [], $options = [])
 * @method string getSQLByBlock7($block7, $limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneByBlock7($block7, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findByBlock7($block7, $limiters = [], $options = [])
 * @method int countByBlock7($block7, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByBlock8($block8, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByBlock8($block8, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByBlock8($block8, $limiters = [], $options = [])
 * @method string getSQLByBlock8($block8, $limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneByBlock8($block8, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findByBlock8($block8, $limiters = [], $options = [])
 * @method int countByBlock8($block8, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByCreatedAt($created_at, $limiters = [], $options = [])
 * @method string getSQLByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findByCreatedAt($created_at, $limiters = [], $options = [])
 * @method int countByCreatedAt($created_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method string getSQLByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method int countByUpdatedAt($updated_at, $limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPager($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQuery($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilder($limiters = [], $options = [])
 * @method string getSQL($limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOne($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] find($limiters = [], $options = [])
 * @method int count($limiters = [], $options = [])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator getPagerBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Query\Builder getQueryBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Builder getQueryBuilderBy($limiters = [], $options = [])
 * @method string getSQLBy($limiters = [], $options = [])
 * @method \App\Models\News\Blocks findOneBy($limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] findBy($limiters = [], $options = [])
 * @method int countBy($limiters = [], $options = [])
 * @method \App\Models\News\Blocks retrieveByPK($id, $limiters = [], $options = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\Models\News\Blocks[] retrieveByPKs($ids, $limiters = [], $options = [])
 */
	 abstract class BlocksRepository extends \Wanjia\Common\Database\Repository {}

}