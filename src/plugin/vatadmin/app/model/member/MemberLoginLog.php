<?php

namespace plugin\vatadmin\app\model\member;

use think\Model;

/**
 * 会员登录日志表
 * @field id int ID
 * @field member_id int 会员ID
 * @field username varchar(64) 登录用户名
 * @field ip varchar(32) IP地址
 * @field ip_location varchar(200) IP地址位置
 * @field browser varchar(32) 浏览器
 * @field system varchar(64) 系统
 * @field user_agent varchar(255) 用户代理
 * @field login_type varchar(20) 登录方式
 * @field login_status tinyint(1) 登录结果[1:成功;0:失败]
 * @field login_result varchar(255) 登录结果
 * @field status tinyint(1) 状态
 * @field createtime datetime 创建时间
 * @field updatetime datetime 更新时间
 */
class MemberLoginLog extends Model
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
    protected $table = 'vat_member_login_log';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

}
