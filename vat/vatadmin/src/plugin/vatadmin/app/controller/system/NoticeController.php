<?php

namespace plugin\vatadmin\app\controller\system;

use plugin\vatadmin\app\controller\BaseController;
use plugin\vatadmin\service\internal\socket\SocketClient;
use support\Container;
use support\Request;
use Tinywan\Jwt\JwtToken;

class NoticeController extends BaseController{

    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\AdminNotice::class);
    }


    public function after($type, $model){
        if($type === 'edit'){
            if($model->type == 1){
                //发送公告通知
                SocketClient::sendAffiche($model->title, $model->content);
            }
        }
    }

    public function injectOwner(&$where){
        if(request()->input('type')){
            $where['admin_id'] = 0;
        }else{
            $where['admin_id'] = JwtToken::getCurrentId();
        }
    }

    /**
     * 重新发送
     * @param Request $request
     */
    public function again(Request $request){
        $id = $request->input('id');
        if(!$id){
            return $this->error('参数错误');
        }
        $model = $this->model->find($id);
        if($model->type == 1){
            //发送公告通知
            SocketClient::sendAffiche($model->title, $model->content);
        }
        return $this->ok('操作成功');
    }

    /**
     * 设置已读
     * @param Request $request
     * @return \support\Response|void
     */
    public function readSet(Request $request){
        $id = $request->input('id');
        if(!$id){
            return $this->error('参数错误');
        }
        $rs = $this->model::setRead($id);
        if($rs){
            return $this->ok('操作成功');
        }
       return $this->error('操作失败');
    }
}

