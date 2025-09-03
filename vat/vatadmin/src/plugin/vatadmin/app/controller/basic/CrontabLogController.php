<?php

namespace plugin\vatadmin\app\controller\basic;

use plugin\vatadmin\app\controller\BaseController;
use support\Container;

class CrontabLogController extends BaseController{

    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\CrontabLog::class);
    }

    public function injectAttr(&$row)
    {
        $row['create_time'] = date('Y-m-d H:i:s', $row['create_time']);
        $row['update_time'] = date('Y-m-d H:i:s', $row['update_time']);
    }
}

