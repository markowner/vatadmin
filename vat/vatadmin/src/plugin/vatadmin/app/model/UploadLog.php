<?php

namespace plugin\vatadmin\app\model;

use think\Model;

/**
 * vat_upload_log 导入日志表
 * @property integer $id (主键)
 * @property integer $admin_id 用户
 * @property string $params 参数
 * @property string $file_url 文件地址
 * @property string $title 标题
 * @property string $content 内容
 * @property string $result 执行结果
 * @property mixed $createime 创建时间
 * @property mixed $updatetime 更新时间
 */
class UploadLog extends Model
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
    protected $table = 'vat_upload_log';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

}
