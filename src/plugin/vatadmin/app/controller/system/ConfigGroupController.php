<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\system;

use plugin\vatadmin\app\controller\BaseController;
use support\Container;

/**
 * @property \plugin\vatadmin\app\model\admin\AdminConfigGroup $model
 */
class ConfigGroupController extends BaseController{

    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\AdminConfigGroup::class);
    }
}

