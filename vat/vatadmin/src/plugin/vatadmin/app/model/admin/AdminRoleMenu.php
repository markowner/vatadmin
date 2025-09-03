<?php

namespace plugin\vatadmin\app\model\admin;

use plugin\vatadmin\service\tools\Enum;
use think\facade\Db;
use think\Model;

/**
 * admin_role_menu 角色菜单表
 * @property integer $id (主键)
 * @property integer $menu_id 菜单ID
 * @property integer $role_id 角色ID
 * @property integer $status 状态
 * @property mixed $createtime 创建时间
 * @property mixed $updatetime 更新时间
 */
class AdminRoleMenu extends Model
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
    protected $table = 'vat_admin_role_menu';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';


    public static function getAllByRoleId($role_id){
        return self::where('role_id', $role_id)->select()->toArray();
    }

    /**
     * 检测用户权限
     * @param $id
     * @param $route
     * @return bool
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function checkPermission($id, $route){
        //获取用户信息
        $adminUserRs = AdminUser::find($id);
        $adminRoleMenu = new self();
        //获取用户菜单权限
        $adminMenu = new AdminMenu();
        $rows = Db::query("SELECT * FROM {$adminMenu->getTable()} 
                    WHERE `status` = ".Enum::STATUS_OK." 
                        AND (id IN (SELECT `menu_id` FROM {$adminRoleMenu->getTable()} WHERE role_id IN ($adminUserRs->roles) AND `status` = ".Enum::STATUS_OK.") 
                        OR is_permission = ".AdminMenu::IS_PERMISSION_NO.") ORDER BY parent_id, sortrank DESC,id");

        //获取权限集合
        $permissionRoutes = [];
         foreach($rows as $k => $v){
             $permission_route = str_replace(" ","",$v['permission_route']);
             $permission_route = str_replace("\n\r","\n",$permission_route);
             $permission_route = str_replace("\r","\n",$permission_route);
             $routes = explode("\n",$permission_route);
             $permissionRoutes = array_merge($permissionRoutes, $routes);
         }
//        foreach($rows as $k => $v){
//            $permission_route = $v['permission_route'] ? json_decode($v['permission_route'], true) : [];
//            $permission_route && $permissionRoutes = array_merge($permissionRoutes, array_column($permission_route, 'url'));
//        }
        if(in_array($route, $permissionRoutes)){
            return true;
        }

        foreach($permissionRoutes as $k => $v){
            if(strpos($v, '*') !== false){
                $tempRoute = str_replace('*','',$v);
                if(strpos($route, $tempRoute) === 0){
                    return true;
                }
            }
        }
        return false;
    }

    public static function getRolesByMenuId($menu_id){
        $roles = self::where('menu_id', $menu_id)->where('status', Enum::STATUS_OK)->column('role_id');
        return ['roles' => $roles,'rows' => AdminRole::getByIds($roles)];
    }

    /**
     * 根据菜单id获取数据
     */
    public static function getByMenuId($menu_id){
        return self::where('menu_id', $menu_id)->select()->toArray();
    }

    /**
     * 根据角色ID和菜单ID获取数据
     */
    public static function getByRoleIdMenuId($role_id, $menu_id){
        return self::where('role_id', $role_id)->where('menu_id', $menu_id)->find();
    }

    /**
     * 获取角色菜单
     */
    public static function getByRoleId($role_id){
        $adminRoleMenu = new self();
        $rows = AdminMenu::whereIn('id',function($query) use ($role_id, $adminRoleMenu){
            $query->table($adminRoleMenu->getTable())->where('status', Enum::STATUS_OK)->where('role_id', $role_id)->field('menu_id');
        })->field(['id','name','parent_id'])->order('parent_id ASC')->order('sortrank DESC')->select()->toArray();
        return $rows;
    }

    /**
     * 获取角色菜单
     */
    public static function getByRoles($roles){
        $adminRoleMenu = new self();
        $rows = AdminMenu::whereIn('id',function($query) use ($roles, $adminRoleMenu){
            $query->table($adminRoleMenu->getTable())->where('status', Enum::STATUS_OK)->whereIn('role_id', $roles)->field('menu_id');
        })->field(['id','name','parent_id'])->order('parent_id ASC')->order('sortrank DESC')->select()->toArray();
        return $rows;
    }

    /**
     * 获取角色菜单IDS
     * @param $role_id
     * @return array
     */
    public static function getMenuByRoleId($role_id){
        $adminRoleMenu = new self();
        $menuIds = AdminMenu::whereIn('id',function($query) use ($role_id, $adminRoleMenu){
            $query->table($adminRoleMenu->getTable())->where('status', Enum::STATUS_OK)->whereIn('role_id', $role_id)->field('menu_id');
        })->column('id');
        return $menuIds;
    }


    /**
     * 设置角色平台菜单权限
     */
    public static function setPermission($menu_ids, $role_id, $admin_id = 0){
        try{
            //获取以前的设置数据
            $oldMenuIds = self::getMenuByRoleId($role_id);
            //取差集
            $diffMenuIds = array_diff($oldMenuIds, $menu_ids);
            if($diffMenuIds){
                self::where('role_id', $role_id)->whereIn('menu_id', $diffMenuIds)->update(['status' => Enum::STATUS_NO]);
            }
            //反取差集
            $diffMenuIds2 = array_diff($menu_ids, $oldMenuIds);
            if($diffMenuIds2){
                foreach($diffMenuIds2 as $menuId){
                    //检测角色及菜单权限是否已存在
                    $checkExist = self::getByRoleIdMenuId($role_id, $menuId);
                    if($checkExist){
                        $checkExist->status == Enum::STATUS_NO && $checkExist->save([
                            'status' => Enum::STATUS_OK
                        ]);
                    }else{
                        //不存在进行添加
                        $roleMenuInfo = [
                            'menu_id' => $menuId,
                            'role_id' => $role_id,
                            'status'  => Enum::STATUS_OK
                        ];
                        self::create($roleMenuInfo);
                    }
                }
            }
            return true;
        }catch(\Exception $e){
            return false;
        }
    }
    
}
