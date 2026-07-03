<?php

namespace plugin\vatadmin\app\model\member;

use think\Model;

/**
 * 会员消息通知表
 * @field id int unsigned 
 * @field member_id int 会员ID
 * @field type int 类型
 * @field title varchar(255) 标题
 * @field content text 内容
 * @field is_read tinyint(1) 已读
 * @field createtime datetime 创建时间
 * @field updatetime datetime 更新时间
 */
class MemberNotice extends Model
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
    protected $table = 'vat_member_notice';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';


     /**
     * 获取未读数量
     */
    public static function getCountNoRead($memberId){
        return self::where('is_read', 0)->whereOr([['member_id','=', 0], ['member_id', '=', $memberId]])->count();
    }

    /**
     * 设置已读
     */
    public static function setRead($id){
        return self::find($id)->save(['is_read' => 1]);
    }
}
