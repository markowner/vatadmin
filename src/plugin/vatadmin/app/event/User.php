<?php

namespace plugin\vatadmin\app\event;

use plugin\vatadmin\app\model\admin\AdminLogLogin;
use plugin\vatadmin\app\model\admin\AdminLogOperation;

class User{

    public function login($info){
        $ip = request()->getRealIp();
        $user_agent = request()->header('User-Agent');
        $data = [
            'title'         => $info['message'],
            'memo'          => '',
            'ip'            => $ip,
            'ip_location'   => $this->getIpLocation($ip),
            'system'        => $this->getSystem($user_agent),
            'browser'       => $this->getBrowser($user_agent),
            'user_agent'    => $user_agent,
            'username'      => $info['username'],
        ];
        AdminLogLogin::create($data);
    }


    public function operation($info){
        $ip = request()->getRealIp();
        $user_agent = request()->header('User-Agent');
        $data = [
            'title'         => $info['message'],
            'route'         => $info['route'],
            'method'        => $info['method'],
            'params'        => $info['params'] ? json_encode($info['params'], JSON_UNESCAPED_UNICODE) : '',
            'ip'            => $ip,
            'ip_location'   => $this->getIpLocation($ip),
            'system'        => $this->getSystem($user_agent),
            'browser'       => $this->getBrowser($user_agent),
            'user_agent'    => $user_agent,
            'admin_id'      => $info['admin_id'],
        ];
        AdminLogOperation::create($data);
    }

    protected function getIpLocation($ip)
    {
        // IP地址验证
        if (empty($ip) || !filter_var($ip, FILTER_VALIDATE_IP)) {
            return '未知';
        }

        $ip2region = new \Ip2Region();
        try {
            $searchResult = $ip2region->memorySearch($ip);          
            if (empty($searchResult['region'])) {
                return '未知';
            }
            
            return $this->parseLocationInfo($searchResult['region']);
            
        } catch (\Exception $e) {
            // 可以在这里记录日志
            return '未知';
        }
    }

    /**
     * 解析地理位置信息
     */
    protected function parseLocationInfo($regionInfo)
    {
        $components = explode('|', $regionInfo);
        $components = array_pad($components, 5, '');
        
        [
            $country, 
            $areaCode, 
            $province, 
            $city, 
            $isp
        ] = $components;
        
        // 处理特殊类型IP
        if ($this->isInternalIp($isp, $country)) {
            return '内网IP';
        }
        
        if ($this->isUnknownLocation($country)) {
            return '未知';
        }
        
        // 中国地区格式化
        if ($country === '中国') {
            return $this->formatChineseLocation($province, $city, $isp);
        }
        
        // 其他国家
        return $country;
    }

    /**
     * 判断是否为内网IP
     */
    protected function isInternalIp($isp, $country)
    {
        return $isp === '内网IP' || $country === '内网IP';
    }

    /**
     * 判断是否为未知位置
     */
    protected function isUnknownLocation($country)
    {
        return $country === '0' || empty($country);
    }

    /**
     * 格式化中国地区信息
     */
    protected function formatChineseLocation($province, $city, $isp)
    {
        $locationParts = [];
        
        if ($province && $province !== '0') {
            $locationParts[] = $province;
        }
        if ($city && $city !== '0' && $city !== $province) { // 避免重复
            $locationParts[] = $city;
        }
        
        $location = empty($locationParts) ? '中国' : implode('-', $locationParts);
        
        // 添加运营商信息
        if ($isp && $isp !== '0') {
            $location .= ':' . $isp;
        }
        
        return $location;
    }

    protected function getBrowser($user_agent): string
    {
        $br = 'Unknown';
        if (false !== stripos($user_agent, "MSIE")) {
            $br = 'MSIE';
        } elseif (false !== stripos($user_agent, "Firefox")) {
            $br = 'Firefox';
        } elseif (false !== stripos($user_agent, "Chrome")) {
            $br = 'Chrome';
        } elseif (false !== stripos($user_agent, "Safari")) {
            $br = 'Safari';
        } elseif (false !== stripos($user_agent, "Opera")) {
            $br = 'Opera';
        } else {
            $br = 'Other';
        }
        return $br;
    }

    protected function getSystem($user_agent): string
    {
        $system = 'Unknown';
        if (false !== stripos($user_agent, "win")) {
            $system = 'Windows';
        } elseif (false !== stripos($user_agent, "mac")) {
            $system = 'Mac';
        } elseif (false !== stripos($user_agent, "linux")) {
            $system = 'Linux';
        } else {
            $system = 'Other';
        }
        return $system;
    }
}