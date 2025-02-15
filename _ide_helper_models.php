<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace app\admin\model{
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
	class Banner extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property string $name 名称
 * @property int $weigh 权重
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classify newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classify newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classify query()
 * @property int $type 类型:1=男生,2=女生,3=短剧分类
 * @property int $pid 父级
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Classify> $children
 * @property-read Classify|null $parent
 * @mixin \Eloquent
 */
	class Classify extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property string $name 名称
 * @property string $author 作者
 * @property string $image 封面
 * @property int $read_num 阅读量
 * @property int $class_id 分类id
 * @property int $text_num 文字数量
 * @property int $creation_status 状态:0=连载中,1=已完结
 * @property float $score 评分
 * @property string $content 简介
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Novel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Novel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Novel query()
 * @property int $hot 热度
 * @property int $search_num 搜索数量
 * @property int $vip VIP:0=否,1=是
 * @property string|null $end_time 最后更新时间
 * @property string|null $finish_time 完结时间
 * @property int $status 状态:0=下架,1=上架
 * @property-read mixed $creation_status_text
 * @property-read mixed $vip_text
 * @property-read \app\admin\model\Classify|null $class
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \app\admin\model\Classify> $tags
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \app\admin\model\NovelDetail> $detail
 * @mixin \Eloquent
 */
	class Novel extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property integer $id 主键(主键)
 * @property integer $novel_id 小说
 * @property integer $class_id 分类
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NovelClass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NovelClass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NovelClass query()
 * @mixin \Eloquent
 */
	class NovelClass extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property int $novel_id 小说
 * @property string $name 章节名称
 * @property string|null $content 内容
 * @property string $price 价格
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NovelDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NovelDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NovelDetail query()
 * @property-read \app\admin\model\Novel|null $novel
 * @property-read \app\admin\model\UsersReadLog|null $readLog
 * @property int $index 章节索引
 * @mixin \Eloquent
 */
	class NovelDetail extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $novel_id 小说
 * @property int $novel_detail_id 章节
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NovelOrders newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NovelOrders newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NovelOrders query()
 * @property string $amount 金额
 * @property int $type 购买类型:1=单章,2=整本
 * @mixin \Eloquent
 * @property-read \app\admin\model\Novel|null $novel
 * @property-read \app\admin\model\NovelDetail|null $novelDetail
 * @property-read \app\admin\model\User|null $user
 */
	class NovelOrders extends \Eloquent {}
}

namespace app\admin\model{
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
 * @property-read \app\admin\model\Classify|null $class
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \app\admin\model\PlayletDetail> $detail
 * @property-read mixed $vip_text
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \app\admin\model\Classify> $tags
 * @property int $creation_status 状态:0=连载中,1=已完结
 * @property-read mixed $creation_status_text
 * @property int $play_num 播放次数
 * @mixin \Eloquent
 */
	class Playlet extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property int $playlet_id 短剧
 * @property int $class_id 分类
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayletClass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayletClass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayletClass query()
 * @mixin \Eloquent
 */
	class PlayletClass extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property int $playlet_id 短剧
 * @property string $name 名称
 * @property string $content 介绍
 * @property string $price 价格
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayletDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayletDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayletDetail query()
 * @property string $image 封面
 * @property string $video 视频
 * @property int $index 剧集索引
 * @property-read \app\admin\model\Playlet|null $playlet
 * @property int $like_num 点赞人数
 * @property int $play_num 播放次数
 * @mixin \Eloquent
 */
	class PlayletDetail extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $playlet_id 短剧
 * @property int $playlet_detail_id 剧集
 * @property string $amount 金额
 * @property int $type 购买类型:1=单集,2=整集
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayletOrders newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayletOrders newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayletOrders query()
 * @property-read \app\admin\model\Playlet|null $playlet
 * @property-read \app\admin\model\PlayletDetail|null $playletDetail
 * @property-read \app\admin\model\User|null $user
 * @mixin \Eloquent
 */
	class PlayletOrders extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $pay_type 支付方式:1=微信,2=支付宝
 * @property string $ordersn 订单编号
 * @property string $pay_amount 支付金额
 * @property int $status 状态:0=未支付,1=已支付
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RechargeOrders newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RechargeOrders newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RechargeOrders query()
 * @property-read \app\admin\model\User|null $user
 * @property string|null $pay_time 支付时间
 * @property-read mixed $pay_type_text
 * @mixin \Eloquent
 */
	class RechargeOrders extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property string|null $event 事件
 * @property string|null $mobile 手机号
 * @property string|null $code 验证码
 * @property int $times 验证次数
 * @property string|null $ip IP
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sms newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sms newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sms query()
 * @mixin \Eloquent
 */
	class Sms extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property string $username 用户名
 * @property string $nickname 昵称
 * @property string $password 密码
 * @property string $sex 性别
 * @property string|null $avatar 头像
 * @property string|null $email 邮箱
 * @property string|null $mobile 手机
 * @property int $level 等级
 * @property string|null $birthday 生日
 * @property string $money 余额(元)
 * @property int $score 积分
 * @property string|null $last_time 登录时间
 * @property string|null $last_ip 登录ip
 * @property string|null $join_time 注册时间
 * @property string|null $join_ip 注册ip
 * @property string|null $token token
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property int $role 角色
 * @property int $status 禁用
 * @property string $invitecode 邀请码
 * @property string $platform_open_id 开放平台OPENID
 * @property string $mini_open_id 小程序OPENID
 * @property string $wechat_unionid 微信身份唯一标识
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @property int $parent_id 上级
 * @property \Illuminate\Support\Carbon|null $vip_expire 会员过期时间
 * @property-read User|null $parent
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $novel_id 小说
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersBookrack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersBookrack newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersBookrack query()
 * @mixin \Eloquent
 */
	class UsersBookrack extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property int|null $user_id
 * @property int|null $parent_id
 * @property int|null $layer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersLayer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersLayer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersLayer query()
 * @mixin \Eloquent
 */
	class UsersLayer extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $playlet_id 短剧
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersPlayletFollow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersPlayletFollow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersPlayletFollow query()
 * @mixin \Eloquent
 */
	class UsersPlayletFollow extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $playlet_id 短剧
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersPlayletLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersPlayletLike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersPlayletLike query()
 * @property-read \app\admin\model\Playlet|null $playlet
 * @property-read \app\admin\model\User|null $user
 * @property int $playlet_detail_id 剧集
 * @property-read \app\admin\model\PlayletDetail|null $playletDetail
 * @mixin \Eloquent
 */
	class UsersPlayletLike extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $playlet_id 短剧
 * @property int $playlet_detail_id 剧集
 * @property string $rate 进度
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersPlayletLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersPlayletLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersPlayletLog query()
 * @property-read \app\admin\model\Playlet|null $playlet
 * @property-read \app\admin\model\PlayletDetail|null $playletDetail
 * @mixin \Eloquent
 */
	class UsersPlayletLog extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $novel_id 小说
 * @property int $novel_detail_id 章节
 * @property string $rate 进度
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersReadLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersReadLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersReadLog query()
 * @property-read \app\admin\model\Novel|null $novel
 * @property-read \app\admin\model\NovelDetail|null $novelDetail
 * @property-read \app\admin\model\User|null $user
 * @mixin \Eloquent
 */
	class UsersReadLog extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 会员ID
 * @property string $score 变更积分
 * @property string $before 变更前积分
 * @property string $after 变更后积分
 * @property string|null $memo 备注
 * @property string $type 类型
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersScoreLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersScoreLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersScoreLog query()
 * @property-read User|null $user
 * @mixin \Eloquent
 */
	class UsersScoreLog extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property string $name 月会员
 * @property string $original_price 原价
 * @property string $price 现价
 * @property string $mark 备注
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vip newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vip query()
 * @mixin \Eloquent
 */
	class Vip extends \Eloquent {}
}

namespace app\admin\model{
/**
 * 
 *
 * @property int $id 主键
 * @property int $vip_id 会员
 * @property int $user_id 用户
 * @property int $pay_type 支付方式:1=微信,2=支付宝
 * @property string $ordersn 订单编号
 * @property string $pay_amount 支付金额
 * @property int $status 状态:0=未支付,1=已支付
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VipOrders newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VipOrders newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VipOrders query()
 * @property string|null $pay_time 支付时间
 * @property-read \app\admin\model\User|null $user
 * @mixin \Eloquent
 */
	class VipOrders extends \Eloquent {}
}

