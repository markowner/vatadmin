<?php

namespace plugin\vatadmin\app\controller\system;

use plugin\vatadmin\app\controller\BaseController;
use support\Container;
use Tinywan\Jwt\JwtToken;

class LogLoginController extends BaseController{

    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\AdminLogLogin::class);
    }

    public function injectOwner(&$where){
        $where['username'] = JwtToken::getExtendVal('username');
    }
}

