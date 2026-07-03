<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\basic;

use plugin\vatadmin\app\controller\BaseController;
use plugin\vatadmin\service\external\vat\Client;
use plugin\vatadmin\service\internal\task\TaskClient;
use support\Log;
use support\Request;
use think\facade\Db;

class PluginController extends BaseController
{
    protected $noNeedLogin = ['list'];

    /**
     * 获取插件列表
     */
    public function list(Request $request)
    {
        $name = $request->input('name', '');
        $tag_id = $request->input('tag_id', '');
        $installed = $request->input('installed', 0);
        $response = (new Client())->plugins([
            'page' => $request->input('page', 1),
            'size' => $request->input('size', 10),
            'name' => $name,
            'tag_id' => $tag_id,
            'price' => $request->input('price', null),
            'installed' => $installed,
            'slugs' => pluginInstalled(),
        ]);
        return $this->ok('success', $response->getData());
    }
    
    /**
     * 安装插件
     */
    public function install(Request $request)
    {
        try{
            $slug = $request->input('slug');
            $name = $request->input('name');
            if (!$slug || !$name) {
                return $this->error('参数错误');
            }
            $token = $request->header('plugin-authorization', '');
            //获取下载地址
            $response = (new Client())->setToken($token)->pluginInstall($slug);
            if (!$response->isOk) {
                return $this->error($response->getMsg(), 0, ['code' => $response->getCode()]);
            }
            //执行异步任务
            TaskClient::send([
                'admin_id' => $request->admin_id,
                'event' => 'install',
                'slug' => $slug,
                'link' => $response->getData()['link'],
            ], 'Plugin', '安装插件'.$name.'('.$slug.')');
            
        }catch(\Exception $e){
            return $this->error($e->getMessage());
        }
        
        return $this->ok('异步安装执行中，请等待');
    }

     /**
     * 卸载插件
     */
    public function uninstall(Request $request)
    {
        $slug = $request->input('slug');
        if (!$slug) {
            return $this->error('参数错误');
        }
        try{
            $pluginDir = base_path('plugin');
            $installFile = $pluginDir . DIRECTORY_SEPARATOR . $slug . DIRECTORY_SEPARATOR . 'Install.php';
            if (is_file($installFile)) {
                require_once $installFile;
                $className = "\\plugin\\{$slug}\\Install";
                $class = new $className();
                    
                // 捕获 echo 输出
                ob_start();
                $class::uninstall();
                $uninstallOutput = ob_get_clean();
                if($class::ifRunSql()){
                    // 5. 执行卸载SQL
                    $this->runUninstallSql($slug);
                }
                
                // 记录卸载输出到日志
                if ($uninstallOutput) {
                    Log::info("插件 {$slug} 卸载输出：" . $uninstallOutput);
                }
            }
            //卸载安装时的目录
            $pluginDir = base_path('plugin') . DIRECTORY_SEPARATOR . $slug;
            if(is_dir($pluginDir)){
                remove_dir($pluginDir);
            }
        }catch(\Throwable $e){
            return $this->error($e->getMessage());
        }
        return $this->ok('卸载成功');
    }

    /**
     * 下载远程文件
     */
    protected function downloadFile($url, $savePath)
    {
        $client = new \GuzzleHttp\Client();
        $client->get($url, ['sink' => $savePath]);
    }


    /**
     * 执行安装SQL
     */
    protected function runInstallSql($slug)
    {
        $sqlFile = base_path('plugin') . DIRECTORY_SEPARATOR . "$slug" . DIRECTORY_SEPARATOR . 'install.sql';
        if (is_file($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $pdo = Db::connect()->getPdo();
            $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); 
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->exec($sql);
        }
    }

    /**
     * 执行卸载SQL
     */
    protected function runUninstallSql($slug)
    {
        $sqlFile = base_path('plugin') . DIRECTORY_SEPARATOR . "$slug" . DIRECTORY_SEPARATOR . 'uninstall.sql';
        if (is_file($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $pdo = Db::connect()->getPdo();
            $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); 
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->exec($sql);
        }
    }
}