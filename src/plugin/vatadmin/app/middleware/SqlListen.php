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
        $debug = config('plugin.vat.vatadmin.app.sql.log', false);
        $displayType = config('plugin.vat.vatadmin.app.sql.display_type', 10);
        if($debug && !self::$isListened){
            Db::listen(function($sql, $runtime) use($displayType){
                if($displayType == 10){
                    Log::info($sql, ['runtime' => $runtime]);   
                }elseif($displayType == 20){
                    echo $sql . PHP_EOL;
                }elseif($displayType == 100){
                    $callback = config('plugin.vat.vatadmin.app.sql.callback');
                    if(is_callable($callback)){
                        $callback($sql, $runtime);
                    }
                }
            });
            self::$isListened = true;
        }
        return $next($request);
    }
    
}
