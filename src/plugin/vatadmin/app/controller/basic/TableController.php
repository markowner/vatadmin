<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\basic;

use plugin\vatadmin\app\controller\BaseController;
use plugin\vatadmin\app\model\Pages;
use plugin\vatadmin\service\tools\Util;
use support\Request;
use think\facade\Db;

class TableController extends BaseController
{
    /**
     * 获取表信息
     * @param Request $request
     * @return \support\Response
     */
    public function list(Request $request){
        $db = Db::connect();
        $tables = $db->getTables();
        $list = [];
        $pageTables = Pages::getPagesTables();
        foreach ($tables as $table) {
            $tableComment = $db->query("SELECT TABLE_COMMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?", [$table]);
            if (!empty($tableComment)) {
                $comment = $tableComment[0]['TABLE_COMMENT'];
            } else {
                $comment = '';
            }
            $column = $db->getFields($table);
            $list[] = [
                'table' => $table,
                'comment' => $comment,
                'column' => $column,
                'is_page' => $pageTables && in_array($table, $pageTables) ? 1 : 0
            ];
        }
        return $this->ok('success', ['list' => $list]);
    }

    public function createPage(Request $request)
    {
        $table = $request->input('table');
        if (empty($table)) {
            return $this->error('参数错误');
        }

        $db = Db::connect();
        $tableComment = $db->query("SELECT TABLE_COMMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?", [$table]);
        $comment = $tableComment[0]['TABLE_COMMENT'] ?? '';

        $tableFields = $db->getFields($table);
        $fields = $this->generateFieldConfigs($tableFields);

        $tableSimple = strpos($table, 'vat_') === 0 ? preg_replace('/vat_/', '', $table, 1) : $table;
        $camelCase = str_replace('_', '', ucwords($tableSimple, '_'));

        $systemTplJson = [
            'api_list' => (object)[],
            'joins' => [],
            'select_fields' => '*',
            'fields' => $fields,
            'tools' => $this->generateToolConfigs($table, $comment),
            'setting' => (object)[]
        ];

        $data = [
            'table' => $table,
            'name' => $comment,
            'build_app_name' => 'admin',
            'build_controller' => $camelCase,
            'build_model' => $camelCase,
            'build_view' => $camelCase,
            'build_menu_name' => $comment,
            'tpl_system_json' => json_encode($systemTplJson, JSON_UNESCAPED_UNICODE)
        ];

        $pages = Pages::where('table', $table)->find();
        if ($pages) {
            $tplJson = $pages->tpl_json ? Util::mergeJson($systemTplJson, json_decode($pages->tpl_json, true)) : $systemTplJson;
            $result = $pages->update([
                'name' => $comment,
                'tpl_system_json' => $data['tpl_system_json'],
                'tpl_json' => json_encode($tplJson, JSON_UNESCAPED_UNICODE)
            ]);
        } else {
            $data['tpl_json'] = $data['tpl_system_json'];
            $result = Pages::create($data);
        }

        return $result !== false ? $this->ok('操作成功') : $this->error('操作失败');
    }

    private function generateFieldConfigs($tableFields)
    {
        $fields = [];
        $i = 0;
        foreach ($tableFields as $key => $field) {
            $config = $this->getFieldConfig($field, $i);
            $fields[] = $config;
            $i++;
        }
        return $fields;
    }

    private function getFieldConfig($field, $index)
    {
        $config = [
            'field' => $field['name'],
            'table_alias' => '',
            'alias' => '',
            'comment' => $field['comment'],
            'type' => $field['type'],
            'search' => true,
            'table_display' => '',
            'table_column' => true,
            'table_order' => $index * 10,
            'form' => true,
            'form_required' => true,
            'form_order' => $index * 10,
            'sorter' => false,
            'condition' => '=',
            'search_view' => 'input',
            'form_view' => 'input',
            'rules'     => 'required',
            'width' => 100,
            'default' => null,
            'config' => (object)[]
        ];

        $this->applyFieldRules($config, $field);

        return $config;
    }

    private function applyFieldRules(&$config, $field)
    {
        // 如果字段类型是整型，设置 form_view 为 input_number
        if ($this->isIntegerType($field['type'])) {
            $config['form_view'] = 'input_number';
        }

        if ($field['name'] === 'id') {
            $config['sorter'] = true;
            $config['form'] = false;
            $config['form_required'] = false;
            $config['rules'] = '';
            $config['width'] = 60;
        }

        if (preg_match('/^(name|title)|(name|title)$/', $field['name'])) {
            $config['condition'] = 'like';
        }

        if (preg_match('/^(type|status)|(type|status)$/', $field['name'])) {
            $config['search_view'] = 'select';
            $config['form_view'] = 'select';
        }

        if ($field['name'] === 'icon') {
            $config['table_display'] = 'icon';
        }

        if ($field['name'] === 'password') {
            $config['table_column'] = false;
        }

        if ($field['name'] === 'status') {
            $config['config']->dict = 'status_desc';
            $config['table_display'] = 'switch';
            $config['form'] = false;
        }

        if (preg_match('/^(json)|(json)$/', $field['name'])) {
            $config['search_view'] = '';
            $config['form_view'] = 'json_editor';
            $config['search'] = false;
            $config['table_column'] = false;
        }

        if (preg_match('/^(avatar|image|img|cover)|(avatar|image|img|cover)$/', $field['name'])) {
            $config['search_view'] = '';
            $config['form_view'] = 'upload';
            $config['form'] = true;
            $config['table_display'] = 'image';
            $config['config'] = [
                "props" => [
                    "list-type" => "image-card",
                    "max" => 1
                ]
            ];
        }

        if (preg_match('/^(imgs|images)|(imgs|images)$/', $field['name'])) {
            $config['search_view'] = '';
            $config['form_view'] = 'upload';
            $config['form'] = true;
            $config['table_display'] = 'images';
            $config['config'] = [
                "props" => [
                    "list-type" => "image-card",
                    "max" => 9
                ]
            ];
        }

        if ($field['name'] === 'avatar') {
            $config['table_display'] = 'avatar';
        }


        if (in_array($field['type'], ['datetime', 'timestamp']) && !in_array($field['name'], ['updatetime', 'update_time', 'updated_at'])) {
            $config['condition'] = 'between';
            $config['search_view'] = 'datepicker';
            $config['form_view'] = 'datepicker';
            $config['config']->type = 'datetimerange';
            $config['config']->form_type = 'datetime';
        }

        if (in_array($field['name'], ['create_time', 'createtime', 'created_at'])) {
            $config['form'] = false;
            $config['form_required'] = false;
            $config['rules'] = '';
        }

        if (in_array($field['name'], ['updatetime', 'update_time', 'updated_at'])) {
            $config['search'] = false;
            $config['form'] = false;
            $config['search_view'] = '';
            $config['form_required'] = false;
        }

        if ($field['type'] === 'date') {
            $config['search_view'] = 'datepicker';
            $config['form_view'] = 'datepicker';
            $config['config']->type = 'date';
            $config['config']->form_type = 'date';
        }

        if ($field['type'] === 'year') {
            $config['search_view'] = 'datepicker';
            $config['form_view'] = 'datepicker';
            $config['config']->type = 'year';
            $config['config']->form_type = 'year';
        }

        $ruleMap = [
            'mobile' => 'mobile',
            'idcard' => 'idcard',
            'email' => 'email',
            'url' => 'url',
            'month' => 'month',
            'date' => 'date',
            'datetime' => 'datetime',
        ];

        if (preg_match('/^(mobile|idcard|email|url|month|date|datetime)|(mobile|idcard|email|url|month|date|datetime)$/', $field['name'], $matches)) {
             // 检查是开头匹配还是结尾匹配
            if (!empty($matches[2])) {
                $matchedKeyword = $matches[2]; // 开头匹配的关键字
            } elseif (!empty($matches[4])) {
                $matchedKeyword = $matches[4]; // 结尾匹配的关键字
            }
            if(key_exists($matchedKeyword,$ruleMap)){
                $config['rules'] = $config['rules'] ? $config['rules'] . '|' . $ruleMap[$matchedKeyword] : $ruleMap[$matchedKeyword];
            }
        }
    }

    /**
     * 判断字段类型是否为整型
     */
    private function isIntegerType($type)
    {
        $integerTypes = [
            'int',
            'tinyint',
            'smallint',
            'mediumint',
            'bigint'
        ];

        // 检查字段类型是否在整型列表中
        foreach ($integerTypes as $integerType) {
            if (strpos($type, $integerType) === 0) {
                return true;
            }
        }

        return false;
    }

    private function generateToolConfigs($table, $comment)
    {
        return [
            'add' => ['show' => true, 'permission_key' => $table . '_add'],
            'edit' => ['show' => true, 'permission_key' => $table . '_edit'],
            'lock' => ['show' => false, 'permission_key' => $table . '_lock'],
            'unlock' => ['show' => false, 'permission_key' => $table . '_unlock'],
            'delete' => ['show' => false, 'permission_key' => $table . '_delete'],
            'import' => ['show' => false, 'permission_key' => $table . '_import', "params" => ["event" => "pages", 'table' => $table, 'title' => $comment]],
            'batch' => ['show' => false, 'permission_key' => $table . '_batch'],
            'refresh' => ['show' => true, 'permission_key' => $table . '_refresh'],
            'download' => ['show' => true, 'permission_key' => $table . '_download', "options" => [["label" => "导出", "key" => ""]]],
            'search' => ['show' => true, 'permission_key' => $table . '_search'],
        ];
    }


    public function fields(Request $request){
        $table = $request->input('table');
        if(!$table){
            return $this->error('参数错误');
        }
        $db = Db::connect();
        $tableFields = $db->getFields($table);
        $fields = [];
        $i = 0;
        foreach ($tableFields as $k => $field) {
            $config = [
                'field'         => $field['name'],
                'table_alias'   => '',
                'alias'         => '',
                'comment'       => $field['comment'],
                'type'          => $field['type'],
                'search'        => false,
                'table_display' => '',
                'table_column'  => true,
                'table_order'   => 900,
                'form'          => false,
                'form_required' => false,
                'form_order'    => 900,
                'sorter'        => false,
                'condition'     => '=',
                'search_view'   => 'input',
                'form_view'     => 'input',
                'rules'         => '',
                'width'         => 100,
                'default'       => null,
                'config'        => (object)[]
            ];
            $this->applyFieldRules($config, $field);
            $fields[] = $config;
            $i++;
        }
        return $this->ok('操作成功', ['list' => $fields]);
    }
}