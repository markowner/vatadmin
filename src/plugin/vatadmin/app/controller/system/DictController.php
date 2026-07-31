<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\system;

use plugin\vatadmin\app\model\admin\AdminDict;
use plugin\vatadmin\app\controller\BaseController;
use support\Container;
use support\Request;
use plugin\vatadmin\service\tools\CrudContext;


/**
 * @property \plugin\vatadmin\app\model\admin\AdminDict $model
 */
class DictController extends BaseController{

    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\AdminDict::class);
    }

    public function injectAttr(&$row){
        $row['value'] = $row['value'] ? json_decode($row['value'], true) : [];
    }

    protected function beforeAdd(CrudContext $ctx) :void{
        $this->addEditHandle($ctx);
    }

    protected function beforeEdit(CrudContext $ctx) :void{
        $this->addEditHandle($ctx);
    }

    /**
     * 新增/编辑 公共处理
     */
    public function addEditHandle(CrudContext $ctx){
        if(is_array($ctx->input['value'])){
            foreach ($ctx->input['value'] as $k => &$v) {
                if (is_numeric($v['value'])) {
                    $v['value'] = (int)$v['value'];
                }
            }
            $ctx->input['value'] = json_encode($ctx->input['value'], JSON_UNESCAPED_UNICODE);
        }
    }

    protected function after(CrudContext $ctx) :void{
        AdminDict::refreshCache();
    }
}

