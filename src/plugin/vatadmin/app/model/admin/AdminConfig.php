<?php

namespace plugin\vatadmin\app\model\admin;

use plugin\vatadmin\service\tools\Enum;
use support\Cache;
use think\Model;

/**
 * 配置表
 * @field id int ID
 * @field group_id int 配置组
 * @field name varchar(64) 名称
 * @field code varchar(64) 键
 * @field value varchar(1280) 值
 * @field status int 状态
 * @field createtime timestamp 创建时间
 * @field updatetime timestamp 更新时间
 */
class AdminConfig extends Model
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
    protected $table = 'vat_admin_config';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    /**
     * 获取开放的配置
     * @return array
     */
    public static function getConfigs()
    {
        $result = self::where('status', Enum::STATUS_OK)->where('open_front', 1)->select();
        return array_column($result->toArray(), 'value', 'code');
    }

    /**
     * 刷新配置缓存
     */
    public static function refreshConfig(){
        $rows = self::where('status', Enum::STATUS_OK)->column('value', 'code');
        $key = env('VAT_ADMIN_CONFIG_KEY','VatAdminConfig');
        Cache::set($key, $rows);
        return $rows;
    }

    /**
     * 获取code值
     * @param $code
     * @return mixed|string
     */
    public static function getCodeConfig($code){
        $key = env('VAT_ADMIN_CONFIG_KEY','VatAdminConfig');
        $items = Cache::get($key);
        if(!$items){
            try {
                $items = self::refreshConfig();
            } catch (\Throwable $e) {
                return '';
            }
        }
        return isset($items[$code]) ? $items[$code] : '';
    }

    public function getValueAttr($value, $options){
        if($options['view'] === 'upload'){
            return cdnUrl($value);
        }
        return $value;
    }
}
