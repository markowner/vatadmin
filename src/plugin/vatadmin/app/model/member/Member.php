<?php

namespace plugin\vatadmin\app\model\member;

use plugin\vatadmin\service\tools\Aes;
use plugin\vatadmin\service\tools\Enum;
use think\Model;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

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

    /**
     * 检测账号密码登录
     * @param $username
     * @param $password
     * @return array|mixed|Model
     * @throws BadRequestHttpException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function checkLogin($username, $password){
        if(!$username || !$password){
            throw new BadRequestHttpException('参数错误');
        }
        $userRs = self::where('username', $username)->find();
        if(!$userRs){
            throw new BadRequestHttpException('账号不存在');
        }
        if($userRs->status != Enum::STATUS_OK){
            throw new BadRequestHttpException('账号已禁用');
        }
        if(!password_verify($password, $userRs->password)){
            throw new BadRequestHttpException('密码无效');
        }
        return $userRs;
    }

    /**
     * 用户信息
     */
    public static function userInfo($memberRs){
        $aes = new Aes(['iv' => md5('Member'.$memberRs->id, 16)]);
        $data = [
            'userInfo' => [
                'id'        => $aes->encrypt($memberRs->id),
                'name'      => $memberRs['name'],
                'username'  => $memberRs->username,
                'mobile'    => $memberRs->mobile,
                'email'     => $memberRs->email,
                'avatar'    => $memberRs->avatar,
                'noread'    => MemberNotice::getCountNoRead($memberRs->id),
            ],
            'menus' => '',
            'views' => '',
            'config' => [],
            'dict' => [],
        ];
        return $data;
    }

}
