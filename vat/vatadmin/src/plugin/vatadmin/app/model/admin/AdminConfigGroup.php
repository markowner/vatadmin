<?php

namespace plugin\vatadmin\app\model\admin;

use think\Model;

/**
 * 配置组
 * @field id int ID
 * @field name varchar(64) 名称
 * @field code varchar(64) 标识
 * @field status int 状态
 * @field createtime timestamp 创建时间
 * @field updatetime timestamp 更新时间
 */
class AdminConfigGroup extends Model
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
    protected $table = 'vat_admin_config_group';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

}
