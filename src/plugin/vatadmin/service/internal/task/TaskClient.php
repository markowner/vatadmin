<?php

namespace plugin\vatadmin\service\internal\task;

use support\Log;
use Workerman\Connection\AsyncTcpConnection;

class TaskClient{

    public static function send($data, $task, $namespace = "plugin\\vatadmin\\service\\internal\\task\\realize"){
        $client = new AsyncTcpConnection("text://127.0.0.1:12345");
        $client->onConnect = function () use ($client, $task, $data, $namespace){
            $rs = $client->send(serialize(['task' => $task, 'namespace' => $namespace, 'data' => $data]));
            Log::info('Task发送任务('.$task.')结果', ['sendRs' => $rs, 'params' => $data]);
            $client->close();
        };
        $client->connect(); 
    }
}