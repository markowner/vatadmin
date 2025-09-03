<?php
namespace plugin\vatadmin\app\middleware;

use plugin\vatadmin\app\model\admin\AdminRoleMenu;
use think\facade\Db;
use Tinywan\ExceptionHandler\Exception\UnauthorizedHttpException;
use Tinywan\Jwt\JwtToken;
use Webman\Event\Event;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AuthCheck implements MiddlewareInterface
{


    public function process(Request $request, callable $next) : Response
    {

        // Db::listen(function($sql, $runtime, $master) {
        //      // 进行监听处理
        //      var_dump($sql);
        // });
         
        if($request->controller){
            $controller = new \ReflectionClass($request->controller);
            $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];
            //过滤掉非权限限制接口
            if (!in_array($request->action, $noNeedLogin)) {
                $adminId = JwtToken::getCurrentId();
                if($adminId != 1){
                    $checkRs = AdminRoleMenu::checkPermission($adminId, $request->path());
                    if(!$checkRs){
                        throw new UnauthorizedHttpException('您没有权限访问');
                    }
                }
                $request->admin_id = $adminId;
                //记录操作日志
                Event::emit('user.operation', ['admin_id' => $adminId, 'message' => '', 'route' => $request->path(), 'method' => $request->method(), 'params' => $request->all()]);
            }

         }

        return $next($request);
    }
    
}
