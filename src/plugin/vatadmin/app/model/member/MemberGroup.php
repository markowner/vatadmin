<?php

namespace plugin\vatadmin\app\model\member;

use think\Model;

/**
 * 会员组表
 * @field id int ID
 * @field name varchar(64) 名称
 * @field memo varchar(200) 说明
 * @field status tinyint(1) 状态
 * @field createtime datetime 创建时间
 * @field updatetime datetime 更新时间
 */
class MemberGroup extends Model
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
    protected $table = 'vat_member_group';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

}
