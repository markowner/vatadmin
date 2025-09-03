<?php

namespace plugin\vatadmin\app\model\admin;

use think\Model;

/**
 * 定时器任务表
 * @field id int unsigned 
 * @field title varchar(100) 任务标题
 * @field type tinyint(1) 任务类型 (1 command, 2 class, 3 url, 4 eval)
 * @field rule varchar(100) 任务执行表达式
 * @field target varchar(150) 调用任务字符串
 * @field parameter varchar(500) 任务调用参数
 * @field running_times int 已运行次数
 * @field last_running_time int 上次运行时间
 * @field remark varchar(255) 备注
 * @field sort int 排序，越大越前
 * @field status tinyint 任务状态状态[0:禁用;1启用]
 * @field create_time int 创建时间
 * @field update_time int 更新时间
 * @field singleton tinyint(1) 是否单次执行 (0 是 1 不是)
 */
class Crontab extends Model
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
    protected $table = 'vat_admin_crontab';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

}
