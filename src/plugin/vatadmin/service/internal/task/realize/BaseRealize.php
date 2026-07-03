<?php

namespace plugin\vatadmin\service\internal\task\realize;

use plugin\vatadmin\app\model\task\TaskAsync;
use plugin\vatadmin\service\internal\socket\SocketClient;

class BaseRealize{

    protected static $taskId = 0;
    protected static $taskTitle = '';

    //场景：自己发送通知无需默认失败通知，抛出异常并使用此code时，不发默认失败通知
    const ErrorNoticeNotSendCode = 99; 

    public static function run($params){
        //生成任务
        $taskInfo = TaskAsync::find($params['task_id']);
        if(!$taskInfo){
            $errorMsg = '任务不存在-'.$params['task_id'];
            throw new \Exception($errorMsg);
        }
        
        self::$taskId = $taskInfo->id;
        self::$taskTitle = $taskInfo->title;
        
        //发送异步任务消息
        SocketClient::sendTaskAsync(self::$taskId, '准备开始执行任务');

         //跳转对应class，方法名为 驼峰命名 + Realize
        $classRealize = str_replace('-', '', ucwords($taskInfo->exec_class, '-')) . 'Realize';
        
        if(!class_exists($taskInfo->namespace . '\\' . $classRealize)){
            throw new \Exception('任务类不存在-'.$taskInfo->namespace . '\\' . $classRealize);
        }
        $classRealize = $taskInfo->namespace . '\\' . $classRealize;
        if(!method_exists($classRealize, 'run')){
            throw new \Exception($$classRealize.'::run方法未定义');
        }

        //任务执行中
        TaskAsync::runningTaskAsync(self::$taskId);

        //执行任务
        $classRealize::exec($taskInfo->params ? json_decode($taskInfo->params, true) : []);
    }
}