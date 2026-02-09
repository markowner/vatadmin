<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\system;

use plugin\vatadmin\app\model\admin\AdminDepartment;
use plugin\vatadmin\app\model\admin\AdminRole;
use plugin\vatadmin\app\controller\BaseController;
use support\Container;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;
use Tinywan\ExceptionHandler\Exception\ServerErrorHttpException;

/**
 * @property \plugin\vatadmin\app\model\admin\AdminUser $model
 */
class UserController extends BaseController
{
    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\AdminUser::class);
    }

    public function injectAttr(&$row)
    {
        //获取角色
        $row['roles'] = explode(',' , $row['roles']);
        $row['roles_name'] = AdminRole::getNamesByIds($row['roles']);
        if($row['department_id']){
            $departmentIds = explode(',', $row['department_id']);
            $lastDepartmentId = end($departmentIds);
            $row['department_name'] = AdminDepartment::find($lastDepartmentId)->name;
        }else{
            $row['department_name'] = '';
        }
    }

    public function before($type, &$data, $model){
        if($type === 'edit'){
            if(!$data['id']){
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }else{
                if($data['password']){
                    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                }else{
                    unset($data['password']);
                }
            }
            $data['roles'] = implode(',', $data['roles']);
        }else if($type === 'delete'){
            if(in_array(1, $data)){
                throw new ServerErrorHttpException('默认用户不能删除');
            }
        }
    
    }

}
