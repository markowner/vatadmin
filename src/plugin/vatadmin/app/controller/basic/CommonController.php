<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\basic;

use plugin\vatadmin\app\controller\BaseController;
use plugin\vatadmin\app\model\admin\AdminConfig;
use support\Request;

class CommonController extends BaseController
{
    protected $noNeedLogin = ['configs'];


    public function configs(Request $request)
    {
        $configs = AdminConfig::getConfigs();
        return $this->ok('success',$configs);
    }
}
