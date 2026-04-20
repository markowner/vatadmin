<?php

namespace plugin\vatadmin\app\model\member;

use think\Model;

/**
 * 会员日志表
 * @field id int ID
 * @field member_id int 会员ID
 * @field username varchar(64) 用户名
 * @field title varchar(50) 标题
 * @field route varchar(255) 路由地址
 * @field method varchar(64) 请求方法
 * @field params text 请求参数
 * @field ip varchar(32) IP地址
 * @field ip_location varchar(200) IP地址位置
 * @field browser varchar(32) 浏览器
 * @field system varchar(64) 系统
 * @field user_agent varchar(255) 用户代理
 * @field status tinyint(1) 状态
 * @field createtime datetime 创建时间
 * @field updatetime datetime 更新时间
 */
class MemberActionLog extends Model
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
    protected $table = 'vat_member_action_log';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

}
