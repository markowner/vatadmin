<?php

namespace plugin\vatadmin\process;

use plugin\vatadmin\service\internal\task\realize\BaseRealize;
use support\Log;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class Task extends Worker{

    /**
     * 接收消息
     */
    public function onMessage(TcpConnection $connection, $task_data){
        $params = unserialize($task_data);
        $result = ['msg' => '操作成功', 'params' => $params, 'data' => []];
        try{
            BaseRealize::run($params);
        }catch(\Exception $e){
            $result['msg'] = '操作失败';
            $result['data'] = [
                'msg' => $e->getMessage(), 'line' => $e->getLine(),'file' => $e->getFile()
            ];
        }
        Log::info('AsyncTask任务('.$params['task'].')执行结果', $result);
        $connection->send(json_encode($result, JSON_UNESCAPED_UNICODE));
    }

}