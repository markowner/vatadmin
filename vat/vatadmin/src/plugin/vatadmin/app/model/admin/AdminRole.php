<?php

namespace plugin\vatadmin\app\model\admin;

use plugin\vatadmin\service\tools\Enum;
use think\Model;

/**
 * admin_role 用户角色表
 * @property integer $id (主键)
 * @property string $name 名称
 * @property integer $data_type 数据权限类型
 * @property integer $parent_id 上级
 * @property integer $sortrank 排序
 * @property integer $status 状态
 * @property mixed $createtime 创建时间
 * @property mixed $updatetime 更新时间
 */
class AdminRole extends Model
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
    protected $table = 'vat_admin_role';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';


    const DATA_TYPE_ALL = 0;

    const DATA_TYPE_DEPART = 10;
    const DATA_TYPE_DEPART_CHILD = 20;
    const DATA_TYPE_SELF = 30;


    /**
     * 根据id集合获取名称集合
     */
    public static function getNamesByIds($ids){
        return self::whereIn('id', $ids)->column('name');
    }

    /**
     * 根据id集合获取集合
     */
    public static function getDataTypeByIds($ids){
        return self::whereIn('id', $ids)->column('data_type');
    }

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
                    'key'           => (string)$item['id'],
                    'label'         => $item['name'],
                    'parent_id'     => $item['parent_id'],
                    'children'      => $children ? : null,
                ];
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
}
