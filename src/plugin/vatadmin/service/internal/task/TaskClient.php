<?php

namespace plugin\vatadmin\service\internal\task;

use plugin\vatadmin\app\model\task\TaskAsync;
use support\Log;
use Workerman\Connection\AsyncTcpConnection;

class TaskClient{

    public static function send($data, $task, $title = '', $namespace = "plugin\\vatadmin\\service\\internal\\task\\realize"){
        //创建异步任务数据
        $taskInfo = TaskAsync::create([
            'title'         => $title ?: $task,
            'namespace'     => $namespace,
            'exec_class'    => $task,
            'params'        => is_string($data) ? $data : json_encode($data),
            'admin_id'      => $data['admin_id'] ?? 0,
        ]);
        $taskId = $taskInfo->id;
        
        $client = new AsyncTcpConnection(config('plugin.vat.vatadmin.app.task.listen'));
        $client->onConnect = function () use ($client, $title, $taskId){
            $rs = $client->send(serialize(['task_id' => $taskId]));
            Log::info('Task发送任务('.$title.')结果', ['sendRs' => $rs]);
            $client->close();
        };
        $client->connect(); 
    }
}