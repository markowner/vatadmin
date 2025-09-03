<?php

namespace plugin\vatadmin\app\model\admin;

use think\Model;

/**
 * 操作日志表
 * @field id int ID
 * @field title varchar(64) 名称
 * @field route varchar(255) 路由地址
 * @field method varchar(16) 请求方法
 * @field params varchar(1280) 请求参数
 * @field ip varchar(32) IP地址
 * @field ip_location varchar(200) 操作地址
 * @field browser varchar(32) 浏览器
 * @field system varchar(64) 系统
 * @field user_agent varchar(255) 用户代理
 * @field admin_id int 用户
 * @field createtime timestamp 创建时间
 * @field updatetime timestamp 更新时间
 */
class AdminLogOperation extends Model
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
    protected $table = 'vat_admin_log_operation';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

}
