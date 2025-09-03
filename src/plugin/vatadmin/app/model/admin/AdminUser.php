<?php

namespace plugin\vatadmin\app\model\admin;

use plugin\vatadmin\service\tools\Aes;
use plugin\vatadmin\service\tools\Enum;
use plugin\vatadmin\service\tools\Util;
use think\Model;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

/**
 * admin_user 用户表
 * @property integer $id (主键)
 * @property integer $department_id 部门
 * @property string $name 姓名
 * @property string $username 账号
 * @property string $mobile 手机号
 * @property string $password 密码
 * @property string $avatar 头像
 * @property string $roles 角色
 * @property integer $online_status 在线状态
 * @property string $last_online_time 最后在线时间
 * @property integer $status 状态
 * @property mixed $createtime 创建时间
 * @property mixed $updatetime 更新时间
 */
class AdminUser extends Model
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
    protected $table = 'vat_admin_user';

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
     * 变更密码
     * @param mixed $data
     * @throws \Tinywan\ExceptionHandler\Exception\BadRequestHttpException
     * @return bool
     */
    public static function changePassword($data){
        //检测密码，密码必须同时 包含大写字母、小写字母和数字，且不小于8位
        if(!trim($data['password_old'])){
            throw new BadRequestHttpException('请输入原密码');
        }
        if(!trim($data['password_new']) || !Util::checkPassword(trim($data['password_new']))){
            throw new BadRequestHttpException('请输入正确格式的新密码');
        }
        if(!trim($data['password_sure']) || !Util::checkPassword(trim($data['password_sure']))){
            throw new BadRequestHttpException('请输入正确格式的确认密码');
        }
        //判断输入是否一致
        if(trim($data['password_new']) != trim($data['password_sure'])){
            throw new BadRequestHttpException('新密码两次输入不一致');
        }
        //检测原密码
        $userRs = self::find($data['id']);
        if(!password_verify($data['password_old'], $userRs->password)){
            throw new BadRequestHttpException('请输入正确的原密码');
        }

        //更新新密码
        $updateRs = $userRs->save(['password' => password_hash(trim($data['password_new']), PASSWORD_DEFAULT)]);
        return $updateRs ? true : false;
    }

    /**
     * 检测用户数据
     * @param mixed $data
     * @throws \Tinywan\ExceptionHandler\Exception\BadRequestHttpException
     * @return void
     */
    public static function check($data){
        //检测姓名
        if(!trim($data['name'])){
            throw new BadRequestHttpException('请输入正确的姓名');
        }
        if(!Util::isValidUsername(trim($data['username']))){
            throw new BadRequestHttpException('请输入正确的账号,不能含有特殊字符，位数限制在5-20位');
        }
        //检测账号
        $users = self::getByUsername(trim($data['username']));
        if($users && $users->id != $data['id']){
            throw new BadRequestHttpException('账号已存在');
        }
        //检测手机号
        if(!Util::isMobile(trim($data['mobile']))){
            throw new BadRequestHttpException('请输入正确的手机号');
        }
        //检测密码，密码必须同时 包含大写字母、小写字母和数字，且不小于8位
        if(trim($data['password']) && !Util::checkPassword(trim($data['password']))){
            throw new BadRequestHttpException('请输入正确的密码');
        }
    }

    /**
     * 创建用户数据
     */
    public static function createUser($data){
        self::check($data);
        return AdminUser::create([
            'name'          => trim($data['name']),
            'username'      => trim($data['username']),
            'mobile'        => trim($data['mobile']),
            'password'      => password_hash(trim($data['password']), PASSWORD_DEFAULT),
            'roles'         => implode(',', $data['roles']),
            'department_id' => is_array($data['department_id']) ? end($data['department_id']) : $data['department_id'],
            'status'        => Enum::STATUS_OK,
        ]);
    }


    public static function getByDepartmentId($departmentId){
        return self::where('department_id', $departmentId)->column('id');
    }
    public static function getByDepartmentIds($departmentIds){
        return self::whereIn('department_id', $departmentIds)->column('id');
    }

    /**
     * 获取用户信息
     * @param mixed $adminUserRs
     * @return array
     */
    public static function userInfo($adminUserRs){
        $aes = new Aes(['iv' => md5($adminUserRs->id, 16)]);
        $menuView = AdminMenu::getByRoles($adminUserRs->roles);
        $data = [
            'userInfo' => [
                'id'        => $aes->encrypt($adminUserRs->id),
                'name'      => $adminUserRs['name'],
                'username'  => $adminUserRs->username,
                'mobile'    => $adminUserRs->mobile,
                'email'     => $adminUserRs->email,
                'avatar'    => $adminUserRs->avatar,
                'roles'     => $adminUserRs->roles,
                'noread'    => AdminNotice::getCountNoRead($adminUserRs->id),
            ],
            'menus' => $menuView['menus'],
            'views' => $menuView['views'],
            'config' => AdminConfig::getConfigs(),
            'dict' => AdminDict::getDict(),
        ];
        return $data;
    }
}
