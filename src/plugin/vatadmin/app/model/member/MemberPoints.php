<?php

namespace plugin\vatadmin\app\model\member;

use think\Model;

/**
 * 会员积分记录表
 * @field id int ID
 * @field member_id int 会员ID
 * @field points decimal(10,2) 积分变动值
 * @field balance decimal(10,2) 积分余额
 * @field reason varchar(255) 变动原因
 * @field type tinyint(1) 类型[1:增加;2:减少]
 * @field status tinyint(1) 状态
 * @field createtime datetime 创建时间
 * @field updatetime datetime 更新时间
 */
class MemberPoints extends Model
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
    protected $table = 'vat_member_points';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

}
