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

}