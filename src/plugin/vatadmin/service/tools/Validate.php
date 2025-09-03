<?php

namespace plugin\vatadmin\service\tools;

use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class Validate {

    /**
     * [
     *      'range' => [
               'min' => '2',
               'max' => '5',
               'mode' => 'UTF8', //默认
               'message' => '请输入2到5个文本'
     *      ]
     * ],
     * [
     *      'required' => '请输入姓名'
     * ],
     * [
     *      'number' => [
     *          'message' => '请输入数字'
     *      ]
     * ],
     * [
     *      'idcard' => '请输入正确的身份证号',
     * ],
     * [
     *      'mobile' => '请输入正确的手机号',
     * ],
     *  [
     *      'phone' => '请输入正确的电话号',
     * ],
     * [
     *      'email' => '请输入正确的邮箱'
     * ],
     * [
     *      'url' => '请输入正确的地址'
     * ],
     * [
     *      'month' => '请输入正确的月份'
     * ],
     * [
     *      'date' => '请输入正确的日期'
     * ],
     * [
     *      'datetime' => '请输入正确的时间'
     * ],
     * [
     *      'unique' => '名称已存在',
     * ]
     * @param $data
     * @param $rule
     */
    public static function check2($data,$rule, $func = ''){
        foreach ($rule as $k => $v){
            if(is_int($k)){
                //过滤没有设置的,规则错误的
                if(!isset($data[$v])) continue;
                $rs = self::checkRequired($data[$v]);
                if(!$rs) {
                    throw new BadRequestHttpException('参数错误');
                }
            }else{
                //过滤没有设置的,规则错误的
                if(!isset($data[$k])) continue;
                if(is_string($v)){//自定义提示消息
                    //默认类型
                    $rs = self::checkRequired($data[$k]);
                    if(!$rs) {
                        throw new BadRequestHttpException($v);
                    }
                }elseif(is_array($v)){ //多种验证规则
                    if(!self::array_key_upper_exists('required',$v)){
                        $rs = self::checkRequired($data[$k]);
                        if(!$rs) {
                            throw new BadRequestHttpException('参数错误');
                        }
                    }
                    foreach ($v as $k1 => $v1){
                        $type = ucfirst(strtolower($k1));
                        $methodName = 'check' . $type;
                        if(is_string($v1)){
                            if($type === 'Unique'){
                                $rs = $func($type,$k,$data[$k]);
                            }else{
                                $rs = self::$methodName($data[$k]);
                            }
                            if(!$rs){
                                throw new BadRequestHttpException($v1);
                            }
                        }elseif(is_array($v1)){
                            $rs = self::$methodName($data[$k],$v1);
                            if(!$rs){
                                throw new BadRequestHttpException($v1['message']);
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }

    public static function check($data){
        $request = request();
        $requestData = [];
        $ruleData = [];
        foreach ($data as $field => $v){
            $requestData[$field] = $request->input($field);
            $v && $ruleData[$field] = $v;
        }
        return self::check2($requestData, $ruleData);
    }


    /**
     * 查询key是否存在【不区分大小写】
     * @param $key
     * @param $array
     * @return bool|int|string
     */
    static function array_key_upper_exists($key, $array){
        foreach ($array as $k => $v){
            if(strtoupper($k) == strtoupper($key)){
                return $k;
            }
        }
        return false;
    }

    /**
     * 检测范围
     * @param $value
     * @param $rule
     * @return bool
     */
    static function checkRange($value, $rule){
        $return = false;
        $charset = isset($rule['mode']) ? $rule['mode'] : 'UTF8';
        $valLen = mb_strlen($value, $charset);
        if($value !== false){
            if(isset($rule['min'])){
                if($valLen >= $rule['min']){
                    $return = true;
                }
            }
            if(isset($rule['max'])){
                if($valLen <= $rule['max']){
                    $return = true;
                }
            }
        }

        return $return;
    }


    /**
     * 检测必填
     * @param $value
     * @param $rule
     * @return bool
     */
    static function checkRequired($value, $rule=[]){
        return $value !== '';
    }

    /**
     * 检测是数字
     * @param $value
     * @param $rule
     * @return bool
     */
    static function checkNumber($value, $rule=[]){
        if(is_numeric($value)){
            return true;
        }
        return false;
    }

    /**
     * 检测身份证号
     * @param $value
     * @param $rule
     * @return bool
     */
    static function checkIdcard($value, $rule=[]){
        if(Util::validateIDCard($value)){
            return true;
        }
        return false;
    }

    /**
     * 检测手机号
     * @param $value
     * @param $rule
     * @return bool
     */
    static function checkMobile($value, $rule=[]){
        if(Util::isMobile($value)){
            return true;
        }
        return false;
    }

    /**
     * 检测电话号
     * @param $value
     * @param $rule
     * @return bool
     */
    static function checkPhone($value, $rule=[]){
        if(Util::isPhone($value)){
            return true;
        }
        return false;
    }

    /**
     * 检测邮箱
     * @param $value
     * @param $rule
     * @return bool
     */
    static function checkEmail($value, $rule=[]){
        if(filter_var($value, FILTER_VALIDATE_EMAIL)){
            return true;
        }
        return false;
    }

    /**
     * 检测地址
     * @param $value
     * @param $rule
     * @return bool
     */
    static function checkUrl($value, $rule=[]){
        if(filter_var($value, FILTER_VALIDATE_URL)){
            return true;
        }
        return false;
    }

    /**
     * 检测年-月日期
     * @param $value
     * @param $rule
     * @return bool
     */
    static function checkMonth($value, $rule=[]){
        $patten = "/^\d{4}[\-](0?[1-9]|1[012])$/";
        if (preg_match($patten, $value)) {
            return true;
        }
        return false;
    }

    /**
     * 检测日期
     * @param $value
     * @param $rule
     * @return bool
     */
    static function checkDate($value, $rule=[]){
        $patten = "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])$/";
        if (preg_match($patten, $value)) {
            return true;
        }
        return false;
    }

    /**
     * 检测时间
     * @param $value
     * @param $rule
     * @return bool
     */
    static function checkDatetime($value, $rule=[]){
        $patten = "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9]))+$/";
        if (preg_match($patten, $value)) {
            return true;
        }
        return false;
    }
}