<?php

namespace plugin\vatadmin\process;

use plugin\vatadmin\app\model\task\TaskAsync;
use plugin\vatadmin\service\internal\socket\SocketClient;
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
        $taskInfo = TaskAsync::find($params['task_id']);
        try{
            ob_start();
            BaseRealize::run($params);
            $output = ob_get_clean();
            TaskAsync::completeTaskAsync($params['task_id'], $output);
        }catch(\Exception $e){
            $result['msg'] = '操作失败';
            $result['data'] = [
                'msg' => $e->getMessage(), 'line' => $e->getLine(),'file' => $e->getFile()
            ];
            TaskAsync::completeTaskAsync($params['task_id'], $e->getTraceAsString());
            if($taskInfo->admin_id){
                $prefix = '【'.$params['task_id'].'】' . $taskInfo->title . "异步任务执行失败";
                SocketClient::send($taskInfo->admin_id, $prefix, $prefix .':'. $e->getMessage());
            }
        }
        Log::info('AsyncTask任务('.$params['task_id'].')执行结果', $result);
        $connection->send(json_encode($result, JSON_UNESCAPED_UNICODE));
    }
}