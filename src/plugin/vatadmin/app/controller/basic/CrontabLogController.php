<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\basic;

use plugin\vatadmin\app\controller\BaseController;
use support\Container;

/**
 * @property \plugin\vatadmin\app\model\admin\CrontabLog $model
 */
class CrontabLogController extends BaseController{

    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\CrontabLog::class);
    }
}

