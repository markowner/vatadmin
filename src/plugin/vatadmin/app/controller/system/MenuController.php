<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\system;

use Override;
use plugin\vatadmin\app\model\admin\AdminMenu;
use plugin\vatadmin\app\model\admin\AdminRoleMenu;
use plugin\vatadmin\app\controller\BaseController;
use plugin\vatadmin\service\tools\CrudContext;
use support\Container;
use support\Request;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

/**
 * @property \plugin\vatadmin\app\model\admin\AdminMenu $model
 */
class MenuController extends BaseController{

    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\AdminMenu::class);
    }

    public function list(Request $request){
        return $this->listTree($request);
    }
    public function buildTree(&$rows){
        $rows = AdminMenu::tree($rows);
    }

    public function roleMenus(Request $request){
        $role_id = $request->input('role_id');
        $list = AdminMenu::buildTreeSimple(AdminMenu::getAll());
        $selected = AdminRoleMenu::getMenuByRoleId($role_id);
        return $this->ok('ok', ['list' => $list, 'selected' => $selected]);
    }

    public function beforeDelete(CrudContext $ctx): void
    {
        //获取当前id及所有子集及子孙级id
        $ids = treeChildIds($ctx->input['ids'], $this->model);
        $ctx->model = $ctx->model->whereIn('id', $ids);
    }


    protected function afterDelete(CrudContext $ctx): void
    {
        $ids = $ctx->input['ids'];
        //删除角色菜单关联数据
        AdminRoleMenu::whereIn('menu_id', $ids)->delete();
    }
}

