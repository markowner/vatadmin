<?php

namespace plugin\vatadmin\app\model\admin;

use think\Model;

/**
 * 定时器任务执行日志表
 * @field id bigint unsigned 
 * @field crontab_id bigint unsigned 任务id
 * @field target varchar(255) 任务调用目标字符串
 * @field parameter varchar(500) 任务调用参数
 * @field exception text 任务执行或者异常信息输出
 * @field return_code tinyint(1) 执行返回状态[0成功; 1失败]
 * @field running_time varchar(10) 执行所用时间
 * @field create_time int 创建时间
 * @field update_time int 更新时间
 */
class CrontabLog extends Model
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
    protected $table = 'vat_admin_crontab_log';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

}
