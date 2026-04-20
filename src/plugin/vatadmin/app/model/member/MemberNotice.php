<?php

namespace plugin\vatadmin\app\model\member;

use think\Model;

/**
 * 会员消息通知表
 * @field id int unsigned 
 * @field member_id int 会员ID
 * @field type int 类型
 * @field title varchar(255) 标题
 * @field content text 内容
 * @field is_read tinyint(1) 已读
 * @field createtime datetime 创建时间
 * @field updatetime datetime 更新时间
 */
class MemberNotice extends Model
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
    protected $table = 'vat_member_notice';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

}
