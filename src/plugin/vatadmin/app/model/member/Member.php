<?php

namespace plugin\vatadmin\app\model\member;

use think\Model;

/**
 * 会员表
 * @field id int ID
 * @field username varchar(64) 用户名
 * @field password varchar(128) 密码
 * @field nickname varchar(64) 昵称
 * @field avatar varchar(255) 头像
 * @field email varchar(128) 邮箱
 * @field mobile varchar(20) 手机号
 * @field level_id int 会员等级
 * @field points decimal(10,2) 积分余额
 * @field last_login_time datetime 最后登录时间
 * @field status tinyint(1) 状态
 * @field createtime datetime 创建时间
 * @field updatetime datetime 更新时间
 */
class Member extends Model
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
    protected $table = 'vat_member';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

}
