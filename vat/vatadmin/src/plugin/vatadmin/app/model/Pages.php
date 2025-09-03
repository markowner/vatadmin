<?php

namespace plugin\vatadmin\app\model;

use think\Model;

/**
 * pages 页面表
 * @property integer $id ID(主键)
 * @property string $table 表名
 * @property string $name 名称
 * @property string $path 路由
 * @property string $views 视图
 * @property string $tpl_json 模版数据
 * @property integer $status 状态
 * @property mixed $createtime 创建时间
 * @property mixed $updatetime 更新时间
 */
class Pages extends Model
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
    protected $table = 'vat_pages';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    /**
     * 获取已生成的页面表
     */
    public static function getPagesTables()
    {
        return self::column('table');
    }
    
}
