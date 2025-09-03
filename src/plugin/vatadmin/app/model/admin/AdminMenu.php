<?php

namespace plugin\vatadmin\app\model\admin;

use plugin\vatadmin\service\tools\Enum;
use think\facade\Db;
use think\Model;

/**
 * admin_menu 菜单表
 * @property integer $id ID(主键)
 * @property string $name 菜单名称
 * @property string $path 路径
 * @property string $component 组件
 * @property string $icon 图标
 * @property integer $parent_id 上级
 * @property string $active 上级高亮地址
 * @property integer $hidden 是否显示
 * @property integer $hidden_breadcrumb 隐藏面包屑
 * @property integer $affix 固定菜单
 * @property string $type 类型
 * @property integer $fullpage 整页路由
 * @property integer $is_permission 权限验证
 * @property string $permission_route 权限路由
 * @property string $redirect 跳转
 * @property integer $cached 页面缓存
 * @property integer $sortrank 排序
 * @property integer $platform_id 平台
 * @property integer $status 状态
 * @property mixed $createtime 创建时间
 * @property mixed $updatetime 更新时间
 */
class AdminMenu extends Model
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
    protected $table = 'vat_admin_menu';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    const AFFIX_OK = 10; //固定菜单标签(不可删除)
    const AFFIX_NO = 0;  //不固定

    const HIDDEN_OK = 10; //隐藏
    const HIDDEN_NO = 0;  //不隐藏

    const CACHED_OK = 10; //缓存
    const CACHED_NO = 0;  //不缓存

    const IS_PERMISSION_NO = 0; //不检测权限
    const IS_PERMISSION_OK = 1; //检测权限

    const TYPE_MENU = 10;
    const TYPE_OPTION = 20;
    const TYPE_PERMISSION = 30;
    const TYPE_HREF = 40;

    /**
     * 获取全部有效数据
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    static function getAll(){
        return (new self())->where('status', Enum::STATUS_OK)->order('parent_id', 'ASC')->order('sortrank','DESC')->order('id', 'ASC')->select()->toArray();
    }

    static function getMenus(){
        return (new self())->where('status', Enum::STATUS_OK)->where('type', 'menu')->order('parent_id', 'ASC')->order('sortrank','DESC')->order('id', 'ASC')->select()->toArray();
    }

    static function buildTreeSimple($menu, $parent_id = 0)
    {
        $tree = array();
        foreach ($menu as $item) {
            if ($item['parent_id'] == $parent_id) {
                $children = self::buildTreeSimple($menu, $item['id']);
                $tree[] = [
                    'value'         => $item['id'],
                    'key'           => $item['id'],
                    'label'         => $item['name'],
                    'parent_id'     => $item['parent_id'],
                    'children'      => $children ? : null,
                ];
            }
        }
        return $tree;
    }

    static function tree($menu, $parent_id = 0)
    {
        $tree = array();
        foreach ($menu as $item) {
            if ($item['parent_id'] == $parent_id) {
                $children = self::tree($menu, $item['id']);
                $children && $item['children'] = $children;
                $tree[] = $item;
            }
        }
        return $tree;
    }

    /**
     * 递归生成树状结构
     * @param $menu
     * @param $parent_id
     * @return array
     */
    static function buildTree($menu, $parent_id = 0, $topPid = 0) {
        $tree = array();
        foreach ($menu as $item) {
            if ($item['parent_id'] == $parent_id) {
                if($parent_id == 0 && $topPid == 0){
                    $children = self::buildTree($menu, $item['id'], $item['id']);
                }else{
                    $children = self::buildTree($menu, $item['id'], $topPid);
                }

                if ($children) {
                    $item['children'] = $children;
                }
                //获取父breadcrumb
                $pmenu = [];
                $topPid > 0 && $pmenu = AdminMenu::find($topPid);
                $tree[] = [
                    'id'            => $item['id'],
                    'path'          => $item['path'],
                    'name'          => $item['path'],
                    'title'         => $item['name'],
                    'icon'          => $item['icon'],
                    'platform_id'   => $item['platform_id'],
                    'parent_id'     => $item['parent_id'],
                    'sortrank'      => $item['sortrank'],
                    'meta'  => [
                        'id'        => $item['id'],
                        'title'     => $item['name'],
                        'icon'      => $item['icon'],
                        'affix'     => $item['affix'] ? true : false,
                        'type'      => $item['type'],
                        'hidden'    => $item['hidden'] ? true : false,
                        'fullpage'  => $item['fullpage'] ? true : false,
                        'hiddenBreadcrumb' => $item['hidden_breadcrumb'] ? true : false,
                        'cached'    => $item['cached'] ? true : false,
                        'is_permission'    => $item['is_permission'] ? true : false,
                        'active'    => $item['active'],
                        'breadcrumb'    => $pmenu ? [
                            'path' => $pmenu['path'],
                            'title' => $pmenu['name'],
                        ] : [],
                    ],
//                    'layout'        => $item['layout']
                    'component'     => $item['component'],
                    'redirect'      => $item['redirect'],
                    'children'      => $children ? : [],
                    'status'        => $item['status'],
                    // 'is_permission' => $item['is_permission'] ? true : false,
                    'permission'    => $item['permission_route'] ? json_decode($item['permission_route']) : [],
                ];
            }
        }
        return $tree;
    }

    /**
     * 获取区域权限标识
     * @param $rows
     * @return array
     */
    public static function getButtonView($rows){
        $views = [];
        foreach($rows as $k => $v){
            if($v['type'] !== 'button' || !$v['path']){
                continue;
            }
            $views[] = $v['path'];
        }
        return $views;
    }


    /**
     * 获取角色菜单
     * @param $roles
     * @return array
     * @throws \think\db\exception\BindParamException
     */
    static function getMenusByRoles($roles){
        if(!$roles){return [];}
        $adminRoleMenu = new AdminRoleMenu();
        $adminMenu = new AdminMenu();
        $sql = "SELECT * FROM {$adminMenu->getTable()} 
                    WHERE `status` = ".Enum::STATUS_OK." 
                        AND (id IN (SELECT `menu_id` FROM {$adminRoleMenu->getTable()} WHERE role_id IN ($roles) AND `status` = ".Enum::STATUS_OK.") 
                        OR is_permission = ".self::IS_PERMISSION_NO.") ORDER BY parent_id, sortrank DESC,id";
        $rows = Db::query($sql);
        return $rows;
    }

    /**
     * 获取角色菜单
     * @param $roles
     * @return array
     * @throws \think\db\exception\BindParamException
     */
    static function getOnlyMenusByRoles($roles){
        if(!$roles){return [];}
        $adminRoleMenu = new AdminRoleMenu();
        $adminMenu = new AdminMenu();
        $sql = "SELECT * FROM {$adminMenu->getTable()} 
                    WHERE `status` = ".Enum::STATUS_OK." 
                        AND type = 'menu'
                        AND (id IN (SELECT `menu_id` FROM {$adminRoleMenu->getTable()} WHERE role_id IN ($roles) AND `status` = ".Enum::STATUS_OK.") 
                        OR is_permission = ".self::IS_PERMISSION_NO.") ORDER BY parent_id, sortrank DESC,id";
        $rows = Db::query($sql);
        return $rows;
    }

    /**
     * 根据角色集合获取授权菜单
     * 返回树状结构
     */
    static function getByRoles($roles){
        $roleArr = explode(',', $roles);
        if(in_array(1, $roleArr)){
            //超级管理员
            $menus = self::buildTree(self::getMenus());
            $views = self::getButtonView(self::getAll());
        }else{
            //其他角色
            $rows = self::getMenusByRoles($roles);
            $views = self::getButtonView($rows);
            $menus = self::buildTree(self::getOnlyMenusByRoles($roles));
        }
        //获取
        return ['views' => $views, 'menus' => $menus];
    }


    /**
     * 获取菜单列表
     */
    public static function getFormatAll(){
        $rows = self::getAll();
        return self::buildTree($rows);
    }

    /**
     * 获取菜单列表
     */
    public static function getSimpleFormatAll(){
        $rows = self::getAll();
        return self::buildTreeSimple($rows);
    }


    /**
     * 获取角色菜单Id集合
     */
    public static function getRoleMenuIds($roleId){
        $rows = self::getMenusByRoles($roleId);
        $menuIds = array_column($rows, 'id');
        return $menuIds;
    }

    /**
     * 添加菜单
     */
    public static function addData($data){
        $menuInfo = [
            'platform_id'       => $data['platform_id'],
            'name'              => $data['meta']['title'],
            'icon'              => $data['meta']['icon'],
            'affix'             => $data['meta']['affix'] ? 10 : 0,
            'hidden'            => $data['meta']['hidden'] ? 10 : 0,
            'fullpage'          => $data['meta']['fullpage'] ? 10 : 0,
            'hiddenBreadcrumb'  => $data['meta']['hiddenBreadcrumb'] ? 10 : 0,
            'cached'            => $data['meta']['cached'] ? 10 : 0,
            'active'            => $data['meta']['active'],
            'type'              => $data['meta']['type'],
            'path'              => $data['path'],
            'sortrank'          => $data['sortrank'],
            'redirect'          => $data['redirect'],
            'parent_id'         => $data['parent_id'] ? (is_array($data['parent_id']) ? end($data['parent_id']) : $data['parent_id']) : 0,
            'component'         => $data['component'],
            'is_permission'     => $data['meta']['is_permission'] ? 1 : 0,
            'permission_route'  => $data['permission'] ? json_encode($data['permission'], JSON_UNESCAPED_UNICODE): '',
        ];
        if($data['id']){
            //更新
            self::find($data['id'])->save($menuInfo);
        }else{
            //添加
            $menuInfo['status'] = Enum::STATUS_OK;
            self::create($menuInfo);
        }
        return true;
    }
}
