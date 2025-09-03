<?php

namespace plugin\vatadmin\app\controller\system;

use plugin\vatadmin\app\model\admin\AdminDepartment;
use plugin\vatadmin\app\model\admin\AdminRole;
use plugin\vatadmin\app\controller\BaseController;
use support\Container;

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

    public function before($type, &$data){
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
        }
    }

}
