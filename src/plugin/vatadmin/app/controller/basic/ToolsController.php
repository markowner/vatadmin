<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\basic;

use plugin\vatadmin\app\controller\BaseController;
use Shopwwi\WebmanFilesystem\Facade\Storage;
use support\Request;

class ToolsController extends BaseController {
    /**
     * @params 参数 ["event" => "event1", ...], 自定义参数，上传后续需队列处理，增加event参数，自行在uploadAfter Task处理后续
     * @storage 存储类型 本地：local，阿里云：oss，腾讯云：cos，七牛：qiniu     默认local
     * 上传文件
     */
    public function upload(Request $request){
       return parent::import($request);
    }
}