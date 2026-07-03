<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\basic;

use plugin\vatadmin\app\controller\BaseController;
use support\Container;
use support\Request;

/**
 * @property \plugin\vatadmin\app\model\task\TaskAsync $model
 */
class TaskAsyncController extends BaseController{

    protected $tableCode = 0; //标识,用于区分多个页面相同的表名
 
    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\task\TaskAsync::class);
    }

    /**
     * 取消任务
     */
    public function cancel(Request $request){
        $id = $request->input('id');
        if(!$id || !is_numeric($id)){
            return $this->error('参数错误');
        }
        $task = $this->model->find($id);
        if(!$task){
            return $this->error('任务不存在');
        }
        if($task->status != $this->model::STATUS_WAITING){
            return $this->error('非待执行任务不能取消');
        }
        $this->model->cancel($id);
        return $this->ok('取消成功');
    }

    /**
     * 复制为新任务
     */
    public function retryNew(Request $request){
        $id = $request->input('id');
        if(!$id || !is_numeric($id)){
            return $this->error('参数错误');
        }
        $task = $this->model->find($id);
        if(!$task){
            return $this->error('任务不存在');
        }
        $this->model->retryNew($id);
        return $this->ok('复制任务成功');
    }
}

