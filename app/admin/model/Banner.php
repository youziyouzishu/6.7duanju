<?php

namespace app\admin\model;


use plugin\admin\app\model\Base;


/**
 * 
 *
 * @property int $id 主键
 * @property string $image 图片
 * @property int $weigh 权重
 * @property int $type 板块:1=推荐,2=短剧,3=男生,4=女生
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner query()
 * @mixin \Eloquent
 */
class Banner extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_banner';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';



}
