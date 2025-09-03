<?php

namespace plugin\vatadmin\app\model\admin;

use think\Model;

/**
 * 消息通知表
 * @field id int unsigned 
 * @field admin_id int 用户
 * @field type int 类型
 * @field title varchar(255) 标题
 * @field content text 内容
 * @field is_read tinyint(1) 已读
 * @field createtime timestamp 创建时间
 * @field updatetime timestamp 更新时间
 */
class AdminNotice extends Model
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
    protected $table = 'vat_admin_notice';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    /**
     * 获取未读数量
     */
    static function getCountNoRead($admin_id){
        return self::where('is_read', 0)->whereOr([['admin_id','=', 0], ['admin_id', '=',$admin_id]])->count();
    }

    /**
     * 设置已读
     */
    static function setRead($id){
        return self::find($id)->save(['is_read' => 1]);
    }

}
