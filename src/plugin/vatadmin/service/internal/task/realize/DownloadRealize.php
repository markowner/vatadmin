<?php

namespace plugin\vatadmin\service\internal\task\realize;

use Rap2hpoutre\FastExcel\FastExcel;
use plugin\vatadmin\service\internal\socket\SocketClient;
use support\Log;


class DownloadRealize{
    public static function run($data)
    {
        $name = '';
        try{
            $fileUrl = '';
            //跳转对应事件方法，static 方法名为 event + 驼峰命名
            $eventMethod = 'event' . str_replace('-', '', ucwords($data['event'], '-'));
            if(!method_exists(self::class, $eventMethod)){
                throw new \Exception('事件类型错误');
            }
            $fileUrl = self::$eventMethod($data);
            SocketClient::send($data['admin_id'], "导出成功","【".$name."】数据导出成功，". '<a target="_blank" href="'.$fileUrl.'">点击下载</a>' );
        }catch(\Exception $e){
            Log::info('文件下载失败',['data' => $data,'msg' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            SocketClient::send($data['admin_id'], "导出失败","【".$name."】数据导出失败，请稍后在试，如还不行请联系管理员");
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * pages下载 事件执行
     * @param mixed $data
     */
    public static function eventPages($data){
        $curd = $data['curd'];
        $rows = $curd->fetch($data['params']);
        $calledClass = $data['calledClass'];
        $controller = $data['this'];
        $fieldDict = $controller->buildDict();
        method_exists($calledClass, 'injectMap') && $controller->injectMap();
        $heading = [];
        foreach ($controller->pageInfo['tpl_json']['fields'] as $fields){
            $heading[$fields['field']] = $fields['comment'];
        }

        foreach ($rows as $k => &$row){
            foreach ($fieldDict as $field => $dict){
                if($row[$field] !== '' && $row[$field] !== null){
                    $row[$field.'_desc'] = $dict[$row[$field]];
                    if(!isset($heading[$field.'_desc'])){
                        $heading[$field.'_desc'] = $heading[$field];
                        unset($heading[$field]);
                    }
                }
            }
            if(method_exists($calledClass, 'injectAttr')){
                $exportColumn = $controller->injectAttr($row);
                $heading = array_merge($heading, $exportColumn);
            }
        }

        $name = ($controller->pageInfo['build_menu_name'] ? : $controller->pageInfo['name']);
        $filePath = DIRECTORY_SEPARATOR .'storage' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR . $name . date('YmdHis'). rand(10000, 99999) .'.csv';
        $headings = $controller->headings ? : $heading;
        (new FastExcel())->data($rows)->export(public_path() . $filePath, function($row) use ($headings){
            $map = [];
            foreach ($headings as $field => $name){
                $map[$name] = $row[$field];
            }
            return $map;
        });

        return cdnUrl($filePath);
    }
}