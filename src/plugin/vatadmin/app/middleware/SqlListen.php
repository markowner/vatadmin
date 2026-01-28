<?php
namespace plugin\vatadmin\app\middleware;

use support\Log;
use think\facade\Db;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class SqlListen implements MiddlewareInterface
{

    // 使用静态变量标记是否已经注册过监听
    private static $isListened = false;

    public function process(Request $request, callable $next) : Response
    {
        $debug = config('plugin.vat.vatadmin.app.debug', false);
        if($debug && !self::$isListened){
            Db::listen(function($sql, $runtime, $master) {
                 Log::info($sql, ['runtime' => $runtime, 'master' => $master]);   
            });
            self::$isListened = true;
        }
        return $next($request);
    }
    
}
