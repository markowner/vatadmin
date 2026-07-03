<?php

namespace plugin\vatadmin\app\model\task;

use plugin\vatadmin\service\internal\socket\SocketClient;
use think\Model;

/**
 * 异步任务表
 * @field id int ID编号
 * @field title varchar(200) 名称
 * @field progress int 进度
 * @field content text 内容
 * @field admin_id int 用户ID
 * @field status tinyint(1) 状态
 * @field createtime datetime 创建时间
 * @field updatetime datetime 更新时间
 */
class TaskAsync extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'mysql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vat_task_async';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';


    const STATUS_CANCEL = -99; //已取消
    const STATUS_WAITING = 0; //待执行
    const STATUS_RUNNING = 50; //运行中
    const STATUS_COMPLETE = 100; //已完成

    public static function runningTaskAsync($taskId){
        self::find($taskId)->save([
            'status' => self::STATUS_RUNNING,
            'start_time' => time(),
        ]);
        SocketClient::sendTaskAsync($taskId, '任务执行中');
    }

    /**
     * 任务执行完成
     */
    public static function completeTaskAsync($taskId, $output = ''){
         self::find($taskId)->save([
            'status' => self::STATUS_COMPLETE,
            'end_time' => time(),
            'content' => $output,
        ]);
        SocketClient::sendTaskAsync($taskId, '任务执行完成');
    }

    /**
     * 取消任务
     */
    public static function cancel($taskId){
        self::find($taskId)->save([
            'status' => self::STATUS_CANCEL,
        ]);
    }

    /**
     * 复制为新任务
     * @param mixed $taskId
     * @return void
     */
    public static function retryNew($taskId){
        $task = self::find($taskId);
        self::create([
            'title' => $task->title,
            'namespace' => $task->namespace,
            'exec_class' => $task->exec_class,
            'params' => $task->params,
            'admin_id' => VatUid(),
        ]);
    }
}
