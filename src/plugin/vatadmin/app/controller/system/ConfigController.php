<?php

namespace plugin\vatadmin\app\controller\system;

use plugin\vatadmin\app\model\admin\AdminConfigGroup;
use plugin\vatadmin\app\controller\BaseController;
use support\Container;
use support\Request;

class ConfigController extends BaseController{

    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\AdminConfig::class);
    }

    public function injectAttr(&$row)
    {
        $row['group_name'] = $row['group_id'] ? AdminConfigGroup::find($row['group_id'])->name : '';
        $row['view_option'] = $row['view_option_json'] ? json_decode($row['view_option_json'], true) : (object)[];
    }

    public function editMore(Request $request)
    {
        $data = $request->input('data');
        $data = json_decode($data, true);
        foreach ($data as $code => $value) {
            $this->model->where('code', $code)->save(['value' => $value]);
        }
        return $this->ok('操作成功');
    }
}

