<?php

namespace plugin\vatadmin\app\controller\system;

use plugin\vatadmin\app\controller\BaseController;
use support\Container;

class LogOperationController extends BaseController{

    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\AdminLogOperation::class);
    }
}

