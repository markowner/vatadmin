<?php

namespace plugin\vatadmin\app\controller\basic;

use plugin\vatadmin\app\controller\BaseController;
use support\Container;

class UploadLogController extends BaseController{

    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\UploadLog::class);
    }
}

