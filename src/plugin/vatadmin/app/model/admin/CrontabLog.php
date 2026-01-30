<?php

namespace plugin\vatadmin\app\model\admin;

use think\Model;

/**
 * 定时任务执行日志表
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
    protected $table = 'vat_crontab_log';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

}
