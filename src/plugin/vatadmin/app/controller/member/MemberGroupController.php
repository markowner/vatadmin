<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\member;

use plugin\vatadmin\app\controller\BaseController;
use support\Container;

/**
 * @property \plugin\vatadmin\app\model\member\MemberGroup $model
 */
class MemberGroupController extends BaseController{

    protected $tableCode = 0; //标识,用于区分多个页面相同的表名
 
    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\member\MemberGroup::class);
    }
}

