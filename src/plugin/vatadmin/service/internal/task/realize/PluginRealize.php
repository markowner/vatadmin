<?php

namespace plugin\vatadmin\service\internal\task\realize;

use plugin\vatadmin\service\internal\socket\SocketClient;
use support\Log;
use think\facade\Db;

class PluginRealize extends BaseRealize{

    public static function exec($data)
    {
        try{
            //跳转对应事件方法，static 方法名为 event + 驼峰命名
            $eventMethod = 'event' . str_replace('-', '', ucwords($data['event'], '-'));
            if(!method_exists(self::class, $eventMethod)){
                throw new \Exception('事件类型错误');
            }
            self::$eventMethod($data);
            echo self::$taskTitle . "执行成功" . PHP_EOL;
            SocketClient::send($data['admin_id'], self::$taskTitle . "执行成功", self::$taskTitle . "执行成功");
        }catch(\Exception $e){
            echo $e->getTraceAsString();
            Log::info( self::$taskTitle . '执行失败',['data' => $data,'msg' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 页面导入数据
     * @param mixed $data
     * @throws \Exception
     * @return void
     */
    public static function eventInstall($data){
        $slug = $data['slug'];
        $downloadUrl = $data['link'];

        $tmpZip = runtime_path("plugin_{$slug}.zip");

        // 2. 下载插件包到本地
        self::downloadFile($downloadUrl, $tmpZip);

        // 3. 解压
        $zip = new \ZipArchive();
        if ($zip->open($tmpZip) !== true) {
            throw new \Exception('插件包损坏');
        }

        $pluginDir = base_path('plugin');
        $zip->extractTo($pluginDir);
        $zip->close();
        unlink($tmpZip);

        // 3. 执行插件安装脚本（必须有 Install.php）
        $installFile = $pluginDir . DIRECTORY_SEPARATOR . $slug . DIRECTORY_SEPARATOR . 'Install.php';
        if (is_file($installFile)) {
            require_once $installFile;
            $className = "\\plugin\\{$slug}\\Install";
            $class = new $className();
            
      
            $class::install();
            if($class::ifRunSql()){
                // 5. 执行安装SQL
                self::runInstallSql($slug);
            }
        }
    }

      /**
     * 下载远程文件
     */
    protected static function downloadFile($url, $savePath)
    {
        $client = new \GuzzleHttp\Client();
        $client->get($url, ['sink' => $savePath]);
    }

    /**
     * 执行安装SQL
     */
    protected static function runInstallSql($slug)
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
    protected static function runUninstallSql($slug)
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