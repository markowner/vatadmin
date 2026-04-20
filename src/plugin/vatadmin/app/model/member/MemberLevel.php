<?php

namespace plugin\vatadmin\app\model\member;

use think\Model;

/**
 * 会员等级表
 * @field id int ID
 * @field name varchar(64) 等级名称
 * @field level int 等级值
 * @field min_points int 最小积分
 * @field max_points int 最大积分
 * @field privileges text 特权描述
 * @field sort int 排序
 * @field status tinyint(1) 状态
 * @field createtime datetime 创建时间
 * @field updatetime datetime 更新时间
 */
class MemberLevel extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'mysql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vat_member_level';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

}
