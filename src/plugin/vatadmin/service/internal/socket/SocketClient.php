<?php

namespace plugin\vatadmin\service\internal\socket;

use plugin\vatadmin\app\model\admin\AdminNotice;
use plugin\vatadmin\service\tools\Aes;
use Webman\Push\Api;

class SocketClient{

    /**
     * 给单个用户发送消息
     */
    static function send($uid, $title ,$content, $event = 'message'){
        try{
            $api = new Api(
            // webman下可以直接使用config获取配置，非webman环境需要手动写入相应配置
                config('plugin.webman.push.app.api'),
                config('plugin.webman.push.app.app_key'),
                config('plugin.webman.push.app.app_secret')
            );
            $Aes = new Aes(['iv' => md5($uid, 16)]);
            $uxid = $Aes->encrypt($uid);

            // 给订阅的所有客户端推送 message 事件的消息
            $api->trigger('Vat-User-'.$uxid, $event, [
                'content'  => $content
            ]);
            //记录通知
            AdminNotice::create([
                'admin_id'      => $uid,
                'type'          => 0,
                'title'         => $title,
                'content'       => $content
            ]);
            return true;
        }catch(\Exception $e){
            return false;
        }
    }

    /**
     * 发送组消息
     */
    public static function sendGroup($title, $content, $group = 1, $event = 'message'){
        try{
            $api = new Api(
            // webman下可以直接使用config获取配置，非webman环境需要手动写入相应配置
                config('plugin.webman.push.app.api'),
                config('plugin.webman.push.app.app_key'),
                config('plugin.webman.push.app.app_secret')
            );

            // 给订阅的所有客户端推送 message 事件的消息
            $api->trigger('Vat-Group-'.$group, $event, [
                'title'     => $title,
                'content'   => $content
            ]);

            //记录通知
            AdminNotice::create([
                'type'          => 0,
                'title'         => $title,
                'content'       => $content
            ]);

            return true;
        }catch(\Exception $e){
            return false;
        }
    }

    /**
     * 发送公告消息
     */
    static function sendAffiche($title, $content, $event = 'message'){
        try{
            $api = new Api(
            // webman下可以直接使用config获取配置，非webman环境需要手动写入相应配置
                config('plugin.webman.push.app.api'),
                config('plugin.webman.push.app.app_key'),
                config('plugin.webman.push.app.app_secret')
            );

            // 给订阅的所有客户端推送 message 事件的消息
            $api->trigger('Vat-Affiche', $event, [
                'title'     => $title,
                'content'   => $content
            ]);

            return true;
        }catch(\Exception $e){
            return false;
        }
    }

}