<?php

namespace plugin\vatadmin\app\controller\system;

use plugin\vatadmin\app\controller\BaseController;
use plugin\vatadmin\app\model\admin\AdminDepartment;
use support\Container;
use support\Request;

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

//    public function before($type, &$data){
//        if($type === 'edit'){
//            if($data['parent_id']){
//                $parentIds = explode(',', $data['parent_id']);
//                $data['parent_id'] = end($parentIds);
//            }
//        }
//    }

}
