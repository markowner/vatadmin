<?php

namespace plugin\vatadmin\app\model\admin;

use think\Model;

/**
 * 登录日志表
 * @field id int ID
 * @field title varchar(64) 标题
 * @field memo varchar(640) 描述
 * @field ip varchar(32) IP地址
 * @field user_agent varchar(255) 用户代理
 * @field admin_id int 用户
 * @field createtime timestamp 创建时间
 * @field updatetime timestamp 更新时间
 */
class AdminLogLogin extends Model
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
    protected $table = 'vat_admin_log_login';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

}
