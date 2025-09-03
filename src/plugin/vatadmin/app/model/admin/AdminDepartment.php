<?php

namespace plugin\vatadmin\app\model\admin;

use plugin\vatadmin\service\tools\Enum;
use think\Model;

/**
 * vat_admin_department 用户角色表
 * @property integer $id (主键)
 * @property string $name 名称
 * @property integer $parent_id 上级
 * @property integer $sortrank 排序
 * @property integer $status 状态
 * @property mixed $createtime 创建时间
 * @property mixed $updatetime 更新时间
 */
class AdminDepartment extends Model
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
    protected $table = 'vat_admin_department';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    static function getAll(){
        return (new self())->where('status', Enum::STATUS_OK)->order('parent_id', 'ASC')->order('sortrank','DESC')->order('id', 'ASC')->select()->toArray();
    }


    static function buildTreeSimple($menu, $parent_id = 0)
    {
        $tree = array();
        foreach ($menu as $item) {
            if ($item['parent_id'] == $parent_id) {
                $children = self::buildTreeSimple($menu, $item['id']);
                $tree[] = [
                    'key'           => $item['id'],
                    'label'         => $item['name'],
                    'parent_id'     => $item['parent_id'],
                    'children'      => $children ? : null,
                ];
            }
        }
        return $tree;
    }

    public static function buildTreeSelect($menu, $parent_id = 0, $parent_key = '')
    {
        $tree = array();
        foreach ($menu as $item) {
            if ($item['parent_id'] == $parent_id) {
                // 生成当前节点的层级 key（如果是根节点，直接用 id；否则拼接父级 key）
                $current_key = $parent_key === '' ? (string)$item['id'] : $parent_key . ',' . $item['id'];
                // 递归生成子节点
                $children = self::buildTreeSelect($menu, $item['id'], $current_key);
                // 构建节点数据
                $tree[] = [
                    'key' => $current_key, // 关键改动：key 变成层级路径
                    'label' => $item['name'],
                    'parent_id' => $item['parent_id'],
                    'children' => $children ?: null,
                ];
            }
        }
        return $tree;
    }

    static function buildTree($menu, $parent_id = 0)
    {
        $tree = array();
        foreach ($menu as $item) {
            if ($item['parent_id'] == $parent_id) {
                $children = self::buildTree($menu, $item['id']);
                $item['children'] = $children ? : null;
                $tree[] = $item;
            }
        }
        return $tree;
    }

    /**
     * 获取树形数据
     */
    public static function getSimpleFormatAll(){
        $rows = self::getAll();
        return self::buildTreeSimple($rows);
    }

    /**
     * 获取树形数据
     */
    public static function getTreeSelectAll(){
        $rows = self::getAll();
        return self::buildTreeSelect($rows);
    }

    public static function getChildIds($level){
        return self::whereLike('level', "{$level}%")->column('id');
    }
}
