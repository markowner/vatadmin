<?php

namespace plugin\vatadmin\app\controller\basic;

use plugin\vatadmin\app\controller\BaseController;
use support\Container;
use support\Log;
use support\Request;

class CrontabController extends BaseController{

    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\Crontab::class);
    }

    public function injectAttr(&$row)
    {
        $row['create_time'] = date('Y-m-d H:i:s', $row['create_time']);
        $row['update_time'] = date('Y-m-d H:i:s', $row['update_time']);
        $row['last_running_time'] = date('Y-m-d H:i:s', $row['last_running_time']);
    }

    /**
     * 定时任务添加编辑
     * @param Request $request
     * @return \support\Response
     */
    public function edit(Request $request){
        $data = $request->post();
        $param = [
            'method' => $data['id'] ? 'crontabUpdate' : 'crontabCreate',
            'args'   => $data
        ];

        $result  = \yzh52521\Task\Client::instance()->request($param);
        Log::info('定时任务添加编辑更新结果：', ['res' => $result]);
        if($result->code === 200){
            return $this->ok('操作成功');
        }
        return $this->error('操作失败');
    }


    /**
     * 删除
     * @param Request $request
     */
    public function delete(Request $request){
        $ids = $request->post("ids");
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $param = [
            'method' => 'crontabDelete',
            'args'   => [
                'id' => $ids
            ]
        ];
        $result  = \yzh52521\Task\Client::instance()->request($param);
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
            'method' => 'crontabReload',
            'args'   => [
                'id' => $ids
            ]
        ];
        $result  = \yzh52521\Task\Client::instance()->request($param);
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
            'method' => 'crontabUpdate',
            'args'   => [
                'id'        => $ids,
                'status'    => $status,
            ]
        ];

        $result  = \yzh52521\Task\Client::instance()->request($param);
        Log::info('定时任务添加编辑更新状态结果：', ['res' => $result]);
        if($result->code === 200){
            return $this->ok('操作成功');
        }
        return $this->error('操作失败');
    }
}

