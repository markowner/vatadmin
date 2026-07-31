<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\system;

use plugin\vatadmin\app\controller\BaseController;
use plugin\vatadmin\app\model\admin\AdminDepartment;
use plugin\vatadmin\service\tools\CrudContext;
use support\Container;
use support\Request;

/**
 * @property \plugin\vatadmin\app\model\admin\AdminDepartment $model
 */
class DepartmentController extends BaseController
{
    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\AdminDepartment::class);
    }

    public function list(Request $request){
       return $this->listTree($request);
    }
    public function buildTree(&$rows){
        $rows = tree($rows);
    }

    /**
     * 树形数据
     */
    public function tree(Request $request){
        $list = AdminDepartment::getTreeSelectAll();
        return $this->ok('获取成功',['list' =>$list]);
    }

    /**
     * 树形数据
     */
    public function tree1(Request $request){
        $list = AdminDepartment::getSimpleFormatAll();
        return $this->ok('获取成功',['list' =>$list]);
    }


    protected function beforeDelete(CrudContext $ctx) :void{
        //获取当前id及所有子集及子孙级id
        $ids = treeChildIds($ctx->input['ids'], $this->model);
        $ctx->model = $ctx->model->whereIn('id', $ids);
    }

}
