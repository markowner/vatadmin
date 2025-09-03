<?php

namespace plugin\vatadmin\app\controller\system;

use plugin\vatadmin\app\model\admin\AdminRole;
use plugin\vatadmin\app\model\admin\AdminRoleMenu;
use plugin\vatadmin\app\controller\BaseController;
use support\Container;
use support\Request;

class RoleController extends BaseController{

    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\AdminRole::class);
    }

    public function tree(Request $request){
        $list = AdminRole::getSimpleFormatAll();
        return $this->ok('success', ['list' => $list]);
    }

    /**
     * 设置角色菜单权限
     * @param Request $request
     * @return \support\Response
     */
    public function roleMenuSubmit(Request $request){
        $roleId = $request->input('role_id');
        $menuIds = $request->input('selected');
        $rs = AdminRoleMenu::setPermission($menuIds, $roleId);
        if($rs){
            return $this->ok('设置成功');
        }
        return $this->error('设置失败');
    }
}

