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

    protected function getIpLocation($ip) {
        $ip2region = new \Ip2Region();
        try {
            $region = $ip2region->memorySearch($ip);
        } catch (\Exception $e) {
            return '未知';
        }
        list($country, $number, $province, $city, $network) = explode('|', $region['region']);
        if ($network === '内网IP') {
            return $network;
        }
        if ($country === '中国') {
            return $province.'-'.$city.':'.$network;
        }
        if ($country == '0') {
            return '未知';
        }

        return $country;
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