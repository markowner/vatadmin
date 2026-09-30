<?php

namespace plugin\vatadmin\app\model\admin;

use plugin\vatadmin\service\tools\Enum;
use support\Cache;
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
    protected $connection = null;
    
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
        $dictKey = env('VAT_ADMIN_DICT_KEY');

        // 构建字典映射（code => value）
        $dictMap = [];
        $frontEndJson = [];
        foreach ($list as $v){
            $dictMap[$v['code']] = $v['value'];
            $frontEndJson[$v['code']] = ['name' => $v['name'], 'options' => json_decode($v['value'], true)];
        }

        // 缓存字典数据
        Cache::set($dictKey, $dictMap);
        // 缓存前端字典数据
        Cache::set($dictKey . 'FrontEnd', $frontEndJson);
    }


    public static function getDict(){
        $dict = Cache::get(env('VAT_ADMIN_DICT_KEY') . 'FrontEnd');
        return $dict ?: [];
    }
}
