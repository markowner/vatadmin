<?php

declare(strict_types=1);

namespace plugin\vatadmin\service\external\vat;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Middleware;
use support\Log;

class Client{
    
    private $baseUrl = 'http://127.0.0.1:8787';
    private $token = null;
    
    /**
     * 构造函数
     * @param string $baseUrl
     */
    public function __construct($baseUrl = null)
    {
        if ($baseUrl) {
            $this->setBaseUrl($baseUrl);
        }
    }

    /**
     * 检查token是否有效
     */
    public function checkToken(){
        if (!$this->token) {
            return false;
        }
        return true;
    }

    /**
     * 请求
     */
    public function request($url, $param = [], $method = 'post', $type = 'form_params'){
        Log::info('CURL请求地址', ['url' => $this->baseUrl . $url, 'method' => $method, 'param' => $param]);
        $stack = \GuzzleHttp\HandlerStack::create();
        // 添加日志中间件
        $stack->push(Middleware::tap(function ($request) {
            $headers = "";
            foreach ($request->getHeaders() as $name => $values) {
                $headers .= "--header '" . $name . ": " . implode(", ", $values) . "' \n";
            }

            $body = (string) $request->getBody();
            $curl = "curl --location '" . $request->getUri() . "' \n" .
                $headers .
                "--data '" . $body . "'";

            Log::info('CURL请求', ['command' => $curl]);
        }));
 
        try {
            $client = new GuzzleHttpClient(['base_uri' => $this->baseUrl,'handler' => $stack]);
            if(strtolower($method) == 'post'){
                $response = $client->post($url, ['headers' => $this->getHeaders(), $type => $param]);
            }else{
                $response = $client->get($url, ['headers' => $this->getHeaders(), 'query' => $param]);
            }
            $result = $response->getBody()->getContents();
            Log::info('CURL请求结果', ['response' => $result]);
            $data = json_decode($result, true);
            return new Response($data);
        } catch (\Exception $e) {
            Log::info('CURL请求异常', ['error' => $e->getMessage(), 'code' => $e->getCode()]);
            return new Response(['code' => $e->getCode(), 'msg' => '请求失败', 'data'=> $e->getMessage()]);
        }
    }


    /**
     * 获取headers信息
     */
    public function getHeaders(){
        return ['Content-Type: application/json', 'authorization' => $this->token];
    }

    /**
     * 登录获取token
     * @param string $username
     * @param string $password
     */
    public function login($username, $password)
    {
        $result = $this->request('/app/vatadmin/basic/user/login', ['client' => 'member', 'username' => $username, 'password' => $password]);
        if(!$result){
            return false;
        }
        
        if ($result->isOk && isset($result->getData()['access_token'])) {
            $this->token = $result->getData()['access_token'];
        }else{
            return false;
        }
        
        return $result->getData();
    }
    
    /**
     * 获取插件列表
     * @param array $params
     * @return Response
     */
    public function plugins($params = [])
    {
        return $this->request('/api/vat/plugin/list', $params);
    }
    
    /**
     * 安装插件
     * @param string $slug
     * @return Response
     */
    public function pluginInstall($slug)
    {
        return $this->request('/api/vat/plugin/install', ['slug' => $slug]);
    }
    
    
    /**
     * 更新插件配置
     * @param string $slug
     * @param array $config
     * @return Response
     */     
    public function updatePluginConfig($slug, $config)
    {
        return $this->request('/api/vat/plugins/updateConfig', [
            'slug' => $slug,
            'config' => $config
        ]);
    }
    
    /**
     * 设置基础URL
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }
    
    /**
     * 获取基础URL
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
    
    /**
     * 获取当前token
     * @return string|null
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * 手动设置token
     * @param string $token
     */
    public function setToken($token){
        $this->token = $token;
        return $this;
    }
}