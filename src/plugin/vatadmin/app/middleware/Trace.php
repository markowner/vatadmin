<?php
namespace plugin\vatadmin\app\middleware;

use support\Log;
use Webman\Http\Response;
use Webman\Http\Request;
use Webman\MiddlewareInterface;

class Trace implements MiddlewareInterface
{

    public function process(Request $request, callable $next) : Response
    {
         // 生成或获取 trace_id
        $traceId = $request->header(
                    config('plugin.vat.vatadmin.app.trace.header_key', 'trace-id'), 
                    md5(uniqid() . microtime(true) . rand(100000, 999999)));
        
        // 存储到请求对象中
        $request->traceId = $traceId;
        
        // 设置到全局上下文（便于日志记录）
        \support\Context::set('trace_id', $traceId);
        
        // 记录请求日志
        $debug = config('plugin.vat.vatadmin.app.trace.log', false);
        if($debug){
            Log::info('request', ['method' => $request->method(),'path' => $request->path(), 'params' => $request->all()]);
        }

        // 继续处理请求
        $response = $next($request);
        
        // 在响应头中返回 trace_id
        $response->withHeader('Trace-Id', $traceId);

        return $response;
    }
}