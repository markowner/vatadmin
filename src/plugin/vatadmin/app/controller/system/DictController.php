<?php

namespace plugin\vatadmin\app\controller\system;

use plugin\vatadmin\app\model\admin\AdminDict;
use plugin\vatadmin\app\controller\BaseController;
use support\Cache;
use support\Container;
use support\Redis;
use support\Request;

class DictController extends BaseController{

    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\AdminDict::class);
    }

    public function injectAttr(&$row){
        $row['value'] = $row['value'] ? json_decode($row['value'], true) : [];
    }

    public function before($type, &$data)
    {
        if($type === 'edit' && is_array($data['value'])){
            foreach ($data['value'] as $k => &$v) {
                if (is_numeric($v['value'])) {
                    $v['value'] = (int)$v['value'];
                }
            }
            $data['value'] = json_encode($data['value'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function after($type, $model){
//            Redis::hSet(getenv('VAT_ADMIN_DICT_KEY'), $model->code, $model->value);
        AdminDict::refreshCache();
    }
}

