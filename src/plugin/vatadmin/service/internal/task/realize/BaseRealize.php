<?php

namespace plugin\vatadmin\service\internal\task\realize;

class BaseRealize{

    public static function run($params){
         //跳转对应class，方法名为 驼峰命名 + Realize
        $classRealize = str_replace('-', '', ucwords($params['task'], '-')) . 'Realize';
        
        if(!class_exists($params['namespace'] . '\\' . $classRealize)){
            throw new \Exception('任务类不存在-'.$params['namespace'] . '\\' . $classRealize);
        }
        $classRealize = $params['namespace'] . '\\' . $classRealize;
        if(!method_exists($classRealize, 'run')){
            throw new \Exception($classRealize.'::run方法未定义');
        }

        $classRealize::run($params['data']);
    }

}