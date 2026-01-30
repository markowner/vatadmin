<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\basic;

use plugin\vatadmin\app\controller\BaseController;
use support\Container;
use support\Log;
use support\Request;
use Vatcron\Client;

/**
 * @property \plugin\vatadmin\app\model\admin\Crontab $model
 */
class CrontabController extends BaseController{

    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\Crontab::class);
    }

    /**
     * 定时任务添加编辑
     * @param Request $request
     * @return \support\Response
     */
    public function edit(Request $request){
        $data = $request->post();
        $param = [
            'method' => $data['id'] ? 'updateTask' : 'createTask',
            'args'   => $data
        ];

        $result  = Client::instance()->request($param);
        Log::info('定时任务添加编辑更新结果：', ['res' => $result]);
        if($result->code === 200){
            return $this->ok('操作成功');
        }
        return $this->error('操作失败');
    }


    /**
     * 任务重启
     * @param Request $request
     * @return \support\Response
     */
    public function reload(Request $request){
        $ids = $request->post("ids");
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $param = [
            'method' => 'reloadTask',
            'args'   => [
                'id' => $ids
            ]
        ];
        $result  = Client::instance()->request($param);
        if($result->code === 200){
            return $this->ok('操作成功');
        }
        return $this->error('操作失败');
    }

    /**
     * 状态
     * @param Request $request
     * @return mixed|\support\Response
     */
    public function lock(Request $request){
        $ids = $request->input('ids');
        $status = $request->input('status');
        if(!$ids){
            return $this->error('参数错误');
        }

        $param = [
            'method' => $status == '0' ? 'startTask' : 'closeTask',
            'args'   => [
                'id' => $ids,
            ]
        ];

        $result  = Client::instance()->request($param);
        Log::info('定时任务更新状态结果：', ['res' => $result]);
        if($result->code === 200){
            return $this->ok('操作成功');
        }
        return $this->error('操作失败');
    }
}

