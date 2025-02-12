<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;

/**
 * 
 *
 * @property integer $id 主键(主键)
 * @property string $name 名称
 * @property string $image 封面
 * @property integer $vip VIP:0=否,1=是
 * @property integer $like_num 喜欢人数
 * @property string $content 介绍
 * @property integer $hot 热度
 * @property integer $status 状态:0=下架,1=上架
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Playlet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Playlet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Playlet query()
 * @property int $class_id 分类
 * @mixin \Eloquent
 */
class Playlet extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_playlet';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $appends = ['vip_text'];

    function class()
    {
        return $this->belongsTo(Classify::class, 'class_id', 'id');
    }

    function tags()
    {
        return $this->belongsToMany(Classify::class, PlayletClass::class, 'playlet_id', 'class_id')->withTimestamps();
    }

    function getVipTextAttribute($value)
    {
        $value = $this->vip;
        $list = [
            0 => '否',
            1 => '是',
        ];
        return $list[$value] ?? '';
    }
    
    
    
}
