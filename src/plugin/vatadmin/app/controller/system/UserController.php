<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\system;

use Override;
use plugin\vatadmin\app\model\admin\AdminDepartment;
use plugin\vatadmin\app\model\admin\AdminRole;
use plugin\vatadmin\app\controller\BaseController;
use plugin\vatadmin\service\tools\CrudContext;
use support\Container;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;
use Tinywan\ExceptionHandler\Exception\ServerErrorHttpException;
use Vat\Validate;
use support\Request;

/**
 * @property \plugin\vatadmin\app\model\admin\AdminUser $model
 */
class UserController extends BaseController
{
    public function __construct()
    {
        $this->model = Container::get(\plugin\vatadmin\app\model\admin\AdminUser::class);
    }

    public function injectAttr(&$row)
    {
        //获取角色
        $row['roles'] = explode(',' , $row['roles']);
        $row['roles_name'] = AdminRole::getNamesByIds($row['roles']);
        if($row['department_id']){
            $departmentIds = explode(',', $row['department_id']);
            $lastDepartmentId = end($departmentIds);
            $row['department_name'] = AdminDepartment::find($lastDepartmentId)->name;
        }else{
            $row['department_name'] = '';
        }
    }

    protected function beforeAdd(CrudContext $ctx): void
    {
        if($ctx->input['password']){
            $ctx->input['password'] = password_hash($ctx->input['password'], PASSWORD_DEFAULT);
        }
        $ctx->input['roles'] = implode(',', $ctx->input['roles']);
    }

    protected function beforeEdit(CrudContext $ctx): void
    {
        $ctx->input['roles'] = implode(',', $ctx->input['roles']);
    }

    protected function beforeDelete(CrudContext $ctx): void
    {
        if(in_array(1, $ctx->input['ids'])){
            $ctx->stop('默认用户不能删除');
            // throw new ServerErrorHttpException('默认用户不能删除');
        }
    }

    //重置密码
    public function resetPassword(Request $request){
        Validate::setErrorHandler(BadRequestHttpException::class);
        $data = Validate::check($request->all(),[
            'ids' => 'required',
            'password' => [
                'password' => [
                    'message' => '请输入正确的新密码',
                    'min' => 6,
                    'max' => 20,
                    'letter' => 4,              // 0: 不必须, 1: 小写, 2: 大写, 3: 大小写, 4: 同时包含
                    'numeric' => true,          // 必须包含数字
                    'symbol' => true            // 必须包含特殊字符
                ]
            ],
            'password_confirm' => [
                'same' => [
                    'message' => '请输入确认新密码',
                    'field' => 'password'
                ]
            ]
        ]);
        
        $ids = explode(',', $data['ids']);
        if(in_array(1, $ids)){
            throw new ServerErrorHttpException('默认用户不能重置密码，请到个人中心修改密码');
        }
        $models = $this->model->whereIn('id', $ids)->select();
        if($models->count() != count($ids)){
            return $this->error('用户不存在');
        }
        foreach ($models as $model) {
            $model->save(['id' => $model->id, 'password' => password_hash($data['password'], PASSWORD_DEFAULT)]);
        }
        return $this->ok('操作成功');
    }

}
