<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\basic;

use plugin\vatadmin\app\controller\BaseController;
use plugin\vatadmin\app\model\admin\AdminDict;
use plugin\vatadmin\app\model\admin\AdminMenu;
use plugin\vatadmin\app\model\Pages;
use support\Container;
use support\Request;
use think\facade\Db;

/**
 * @property \plugin\vatadmin\app\model\Pages $model
 */
class PagesController extends BaseController
{
    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\Pages::class);
    }

    /**
     * 菜单tree
     * @param Request $request
     */
    public function menus(Request $request){
        $list = AdminMenu::getSimpleFormatAll();
        return $this->ok('success', ['list' => $list]);
    }

    /**
     * 获取字典列表
     * @param Request $request
     */
    public function dict(Request $request){
        $dictList = AdminDict::getOkAll();
        $list = [];
        foreach ($dictList as $item) {
            $list[] = [
                'label' => $item['name'],
                'value' => $item['code'],
            ];
        }
        return $this->ok('success', ['list' => $list]);
    }

    /**
     * 构建页面
     * @param Request $request
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function build(Request $request)
    {
        $id = $request->input('id');
        $options = $request->input('options');
        if(!$id){
            return $this->error('参数错误');
        }
        if(!$options){
            return $this->error('请选择构建选项');
        }
        //获取page信息
        $page = Pages::find($id);
        if(!$page){
            return $this->error('数据错误');
        }

        foreach ($options as $option){
            $option === 'controller' && $this->_buildController($page);
            $option === 'model' && $this->_buildModel($page);
            $option === 'json' && $this->_buildVatPage($page);
            $option === 'view' && $this->_buildView($page);
            $option === 'menu' && $this->_buildMenu($page);
        }

        return $this->ok('构建成功');
    }

    /**
     * 生成控制器
     * @param $page
     * @return void
     */
    private function _buildController($page)
    {
        $buildController = splitPathOptimized(rtrim($page['build_controller'],'/'));
        $buildModel = splitPathOptimized(rtrim($page['build_model'],'/'));
        //获取控制器模版
        $controllerTplPath = stub_path() . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'controller.stub';
        $controllerTplData = file_get_contents($controllerTplPath);
        $controllerTplData = str_replace(
            [
                '{#build_app_name#}',
                '{#build_controller_dir#}',
                '{#build_controller_name#}',
                '{#build_model_dir#}',
                '{#build_model_name#}',
            ],
            [
                $page['build_app_name'],
                $buildController[0] ? '\\' . $buildController[0] : '',
                $buildController[1],
                $buildModel[0] ? '\\' . $buildModel[0] : '',
                $buildModel[1],
            ],
            $controllerTplData
        );
        //获取生成路径
        $path = app_path() . DIRECTORY_SEPARATOR . $page['build_app_name'] . DIRECTORY_SEPARATOR . 'controller';
        $buildController[0] && $path .= DIRECTORY_SEPARATOR . $buildController[0];
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }
        $path .= DIRECTORY_SEPARATOR . $buildController[1].'Controller.php';
        file_put_contents($path, $controllerTplData);
    }

    /**
     * 生成模型
     * @param $page
     */
    private function _buildModel($page)
    {
        $buildModel = splitPathOptimized(rtrim($page['build_model'],'/'));
        //获取控制器模版
        $modelTplPath = stub_path() . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'model.stub';
        $modelTplData = file_get_contents($modelTplPath);

        //获取字段信息
        $db = Db::connect();
        $tableComment = $db->query("SELECT TABLE_COMMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?", [$page['table']]);
        if (!empty($tableComment)) {
            $comment = $tableComment[0]['TABLE_COMMENT'];
        } else {
            $comment = '';
        }
        $tableFields = $db->getFields($page['table']);
        $build_model_field = ' * ' . $comment."\n";
        foreach ($tableFields as $k => $field) {
            $build_model_field .= " * @field {$field['name']} {$field['type']} {$field['comment']}\n";
        }
        $modelTplData = str_replace(
            [
                '{#build_model#}',
                '{#build_model_name#}',
                '{#build_model_field#}',
                '{#table#}'
            ],
            [
                $buildModel[0] ? '\\' . $buildModel[0] : '',
                $buildModel[1],
                rtrim($build_model_field),
                $page['table'],
            ],
            $modelTplData
        );
        //获取生成路径
        $path = app_path() . DIRECTORY_SEPARATOR . 'model';
        $buildModel[0] && $path .= DIRECTORY_SEPARATOR . $buildModel[0];
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }
        $path .= DIRECTORY_SEPARATOR . $buildModel[1].'.php';
        file_put_contents($path, $modelTplData);
    }

    /**
     * 生成视图
     * @param $page
     */
    private function _buildView($page)
    {
        $basePath = BASE_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . ($page['build_project'] ?:'VatAdmin') . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'views';
        $path = $basePath . DIRECTORY_SEPARATOR . trim($page['build_view'], '/');
        if(!is_dir($path)){
            recursiveMkdir($path);
        }
        //获取控制器模版
        $indexTplPath = stub_path() . DIRECTORY_SEPARATOR . 'vue' . DIRECTORY_SEPARATOR . 'index.stub';
        $editTplPath = stub_path() . DIRECTORY_SEPARATOR . 'vue' . DIRECTORY_SEPARATOR . 'edit.stub';
        $indexTplPathData = file_get_contents($indexTplPath);
        $indexTplPathData = str_replace(
            [
                '{#page_name#}',
            ],
            [
                $page['table'],
            ],
            $indexTplPathData
        );
        $editTplPathData = file_get_contents($editTplPath);
        $editTplPathData = str_replace(
            [
                '{#page_name#}',
            ],
            [
                $page['table'],
            ],
            $editTplPathData
        );
        //获取生成路径
        file_put_contents($path . DIRECTORY_SEPARATOR . 'index.vue', $indexTplPathData);
        file_put_contents($path . DIRECTORY_SEPARATOR . 'edit.vue', $editTplPathData);
    }

    private function _buildVatPage($page){
        $path = BASE_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . ($page['build_project'] ?:getenv('VAT_ADMIN_PROJECT_NAME')) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'vat'. DIRECTORY_SEPARATOR .'pages'. DIRECTORY_SEPARATOR . $page['table'] . '.json';
        $tpl_json = json_decode($page['tpl_json']);
        file_put_contents($path, json_encode($tpl_json,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    /**
     * 生成菜单
     * @param $page
     */
    private function _buildMenu($page){
        if(!$page['build_menu']){
            return true;
        }

        //添加主菜单
        $menuId = Db::name('vat_admin_menu')
            ->insertGetId([
                'parent_id' => $page->build_menu,
                'name' => $page->build_menu_name,
                'path' => DIRECTORY_SEPARATOR . $page->build_view,
                'component' => $page->build_view . DIRECTORY_SEPARATOR .'index',
                'icon' => 'list',
                'hidden' => 0,
                'type' => 'menu',
                'is_permission' => 1,
                'permission_route' => ''
            ]);
        //添加子菜单记录
        $subMenu = [
            ['name' => '列表', 'path' => $page->table . '_list', 'permission_route' => DIRECTORY_SEPARATOR . $page->build_app_name . DIRECTORY_SEPARATOR . trim($page->build_controller, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'list'],
            ['name' => '添加', 'path' => $page->table . '_add', 'permission_route' => DIRECTORY_SEPARATOR . $page->build_app_name . DIRECTORY_SEPARATOR . trim($page->build_controller, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'edit'],
            ['name' => '编辑', 'path' => $page->table . '_edit', 'permission_route' => DIRECTORY_SEPARATOR . $page->build_app_name . DIRECTORY_SEPARATOR . trim($page->build_controller, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'edit'],
            ['name' => '锁定', 'path' => $page->table . '_lock', 'permission_route' => DIRECTORY_SEPARATOR . $page->build_app_name . DIRECTORY_SEPARATOR . trim($page->build_controller, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'lock'],
            ['name' => '解锁', 'path' => $page->table . '_unlock', 'permission_route' => DIRECTORY_SEPARATOR . $page->build_app_name . DIRECTORY_SEPARATOR . trim($page->build_controller, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'lock'],
            ['name' => '删除', 'path' => $page->table . '_delete', 'permission_route' => DIRECTORY_SEPARATOR . $page->build_app_name . DIRECTORY_SEPARATOR . trim($page->build_controller, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'delete'],
            ['name' => '导入', 'path' => $page->table . '_import', 'permission_route' => DIRECTORY_SEPARATOR . $page->build_app_name . DIRECTORY_SEPARATOR . trim($page->build_controller, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'import*'],
            ['name' => '下载', 'path' => $page->table . '_download', 'permission_route' => DIRECTORY_SEPARATOR . $page->build_app_name . DIRECTORY_SEPARATOR . trim($page->build_controller, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'download*'],
            ['name' => '批量操作', 'path' => $page->table . '_batch', 'permission_route' => DIRECTORY_SEPARATOR . $page->build_app_name . DIRECTORY_SEPARATOR . trim($page->build_controller, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'batch*'],
        ];

        foreach ($subMenu as $item) {
            Db::name('vat_admin_menu')->insert([
                'parent_id' => $menuId,
                'name'      => $item['name'],
                'path'      => $item['path'],
                'component' => '',
                'icon'      => '',
                'hidden' => 1,
                'type' => 'button',
                'is_permission' => 1,
                'permission_route' => $item['permission_route'],
            ]);
        }
    }
}
