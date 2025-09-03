<?php

namespace plugin\vatadmin\service\internal\task\realize;

use plugin\vatadmin\app\model\UploadLog;
use Rap2hpoutre\FastExcel\FastExcel;
use plugin\vatadmin\service\internal\socket\SocketClient;
use support\Log;
use think\facade\Db;

class ImportRealize{
    public static function run($data)
    {
        try{
            $UploadLogRs = UploadLog::find($data['id']);
            $params = json_decode($UploadLogRs->params, true);
            $content = json_decode($UploadLogRs->content, true);
            $allSheets = (new FastExcel())->import(public_path() . DIRECTORY_SEPARATOR . $content['file_name'])->toArray();
            $allSheets = array_values($allSheets);
            if(!$allSheets){
                throw new \Exception('请上传一个有数据的文件哦');
            }
            //跳转对应事件方法，static 方法名为 event + 驼峰命名
            $eventMethod = 'event' . str_replace('-', '', ucwords($params['event'], '-'));
            if(!method_exists(self::class, $eventMethod)){
                throw new \Exception('上传文件事件类型错误');
            }
            //调用对应事件方法
            self::$eventMethod($data, $params, $allSheets);

            SocketClient::send($UploadLogRs->admin_id, $params['title']."导入成功", $params['title']."导入成功");
        }catch(\Throwable $e){
            Log::info('导入文件失败',['id' => $data['id'],'msg' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            SocketClient::send($UploadLogRs->admin_id, $params['title']."导入失败", $params['title']."导入失败" );
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 页面导入数据
     * @param mixed $data
     * @param mixed $params
     * @param mixed $allSheets
     * @throws \Exception
     * @return void
     */
    public static function eventPages($data, $params, $allSheets){
        $insert = [];
        foreach($allSheets as $row){
            $item = [];
            foreach($data['params']['heading'] as $name => $field){
                isset($row[$name]) && $item[$field] = $row[$name];
            }
            $item && $insert[] = $item;
        }
        // 分块处理避免 SQL 过长
        $chunks = array_chunk($insert, 1000);
        Db::startTrans();
        try{
            foreach ($chunks as $chunk) {
                Db::name($params['table'])->extra('IGNORE')->insertAll($chunk);
            }
            Db::commit();
        }catch (\Throwable $exception){
            Db::rollback();
            throw new \Exception($exception->getMessage());
        }
    }
}