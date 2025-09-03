<?php

namespace plugin\vatadmin\app\model\admin;

use plugin\vatadmin\service\tools\Enum;
use support\Redis;
use think\Model;

/**
 * vat_admin_dict 
 * @property integer $id ID(主键)
 * @property string $name 名称
 * @property string $code 键
 * @property string $value 值
 * @property integer $status 状态
 * @property mixed $createtime 创建时间
 * @property mixed $updatetime 更新时间
 */
class AdminDict extends Model
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
    protected $table = 'vat_admin_dict';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    public static function getOkAll(){
        return AdminDict::where('status', Enum::STATUS_OK)->field('code,name,value')->select();
    }

    public static function refreshCache(){
        $list = self::getOkAll();
        $fieldsToKeep = array_column($list->toArray(), 'code');
        $dictKey = env('VAT_ADMIN_DICT_KEY');
        $dicts = Redis::hGetAll($dictKey);
        foreach ($dicts as $field => $value){
            // 如果字段不在要保留的字段列表中
            if (!in_array($field, $fieldsToKeep)) {
                // 删除这个字段
                Redis::hDel($dictKey, $field);
            }
        }
        $json = [];
        foreach ($list as $k => $v){
            Redis::hSet($dictKey, $v['code'], $v['value']);
            $json[$v['code']] = ['name' => $v['name'],'options' => json_decode($v['value'], true)];
        }
        //更新前端缓存文件
        Redis::set($dictKey.'FrontEnd', json_encode($json, JSON_UNESCAPED_UNICODE));
//        vat_base_build($json, 'vat_dict.json');
    }


    public static function getDict(){
        $dict = Redis::get(env('VAT_ADMIN_DICT_KEY') . 'FrontEnd');
        return $dict ? json_decode($dict, true) : [];
    }
}
