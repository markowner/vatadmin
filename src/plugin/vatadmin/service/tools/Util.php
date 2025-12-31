<?php

namespace plugin\vatadmin\service\tools;

class Util{

     /**
     * 验证手机号是否正确
     * @param $mobile
     * @return bool
     */
    public static function isMobile($mobile)
    {
        if(empty($mobile)){
            return false;
        }
        $search = '/^1[3|4|5|6|7|8|9][0-9]\d{8}$/';
        if ( preg_match( $search, $mobile ) ) {
            return true ;
        }
        return false;
    }

    /**
     * 检测座机
     * @param $mobile
     * @return bool
     */
    public static function isPhone( $phone ) {
        if(empty($phone)){
            return false;
        }
        $search = '/^(\d{3,4}|\d{3,4}-|\s)?\d{6,8}$/';
        if ( preg_match( $search, $phone ) ) {
            return true ;
        }
        return false;
    }

    //验证身份证是否有效
    static function validateIDCard($IDCard) {
        if(empty($IDCard)){
            return false;
        }
        if (strlen($IDCard) == 18) {
            return self::check18IDCard($IDCard);
        }

        if ((strlen($IDCard) == 15)) {
            $IDCard = self::convertIDCard15to18($IDCard);
            return self::check18IDCard($IDCard);
        }

        return false;
    }

//计算身份证的最后一位验证码,根据国家标准GB 11643-1999
    static function calcIDCardCode($IDCardBody) {
        if (strlen($IDCardBody) != 17) {
            return false;
        }

        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $code = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;

        for ($i = 0, $iMax = strlen($IDCardBody); $i < $iMax; $i++) {
            $checksum += substr($IDCardBody, $i, 1) * $factor[$i];
        }

        return $code[$checksum % 11];
    }

// 将15位身份证升级到18位
    static function convertIDCard15to18($IDCard) {
        if (strlen($IDCard) != 15) {
            return false;
        } else {
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (in_array(substr($IDCard, 12, 3), array('996', '997', '998', '999'))) {
                $IDCard = substr($IDCard, 0, 6) . '18' . substr($IDCard, 6, 9);
            } else {
                $IDCard = substr($IDCard, 0, 6) . '19' . substr($IDCard, 6, 9);
            }
        }
        $IDCard = $IDCard . self::calcIDCardCode($IDCard);
        return $IDCard;
    }

    // 18位身份证校验码有效性检查
    static function check18IDCard($IDCard) {
        if (strlen($IDCard) != 18) {
            return false;
        }

        $IDCardBody = substr($IDCard, 0, 17); //身份证主体
        $IDCardCode = strtoupper(substr($IDCard, 17, 1)); //身份证最后一位的验证码

        return self::calcIDCardCode($IDCardBody) == $IDCardCode;
    }

     /**
     * 格式化两位小数
     * @param $money
     * @return string
     */
    public static function format($money,$length=2)
    {
        $nextLength = $length + 1;
        return sprintf("%.{$length}f", substr(sprintf("%.{$nextLength}f", $money), 0, -$length));
    }

    /**
     * 检测密码
     * 密码格式：同时且只能包含大写字母、小写字母、数字，并且检测字符长度
     */
    public static function checkPassword($password){
        if(empty($password)){
            return false;
        }
        $reg = "/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])[A-Za-z0-9]{8,20}/";
        if ( preg_match( $reg, $password ) ) {
            return true ;
        }
        return false;
    }

    /**
     * 检测账号
     */
    public static function isValidUsername($username) {
        if(empty($username)){
            return false;
        }
        // 定义合法字符集，这里包括字母（大小写）、数字和下划线
        $pattern = '/^[a-zA-Z0-9_]{5,20}$/';

        // 使用preg_match函数检查$username是否匹配该模式
        if (preg_match($pattern, $username)) {
            return true; // 账号名有效，长度在5至20个字符之间且不包含特殊字符
        }
        return false; // 账号名无效，不符合长度要求或包含特殊字符
    }

    /**
     * 合并数据
     */
    public static function mergeJson($array1, $array2) {
        foreach ($array2 as $key => $value) {
            if (is_array($value)) {
                if (isset($array1[$key]) && is_array($array1[$key])) {
                    // 如果是关联数组，递归合并并允许覆盖
                    if (array_keys($value) !== range(0, count($value) - 1)) {
                        $array1[$key] = self::mergeJson($array1[$key], $value);
                    }
                    // 如果是索引数组，并且 key 是 'fields'，根据 field 和 table_alias 去重并部分覆盖
                    elseif ($key === 'fields') {
                        // 创建一个关联数组来跟踪已有的 field 和 table_alias 组合及其索引
                        $existingFields = [];
                        foreach ($array1[$key] as $index => $item) {
                            $identifier = isset($item['table_alias']) && $item['table_alias'] ? $item['field'] . ':' . $item['table_alias'] : $item['field'];
                            $existingFields[$identifier] = $index;
                        }

                        // 添加或更新 tpl_json 中的新字段到 tpl_json2
                        foreach ($value as $item) {
                            $identifier = isset($item['table_alias']) && $item['table_alias'] ? $item['field'] . ':' . $item['table_alias'] : $item['field'];
                            if (isset($existingFields[$identifier])) {
                                // 如果存在，则部分覆盖（仅覆盖存在的键）
                                $existingIndex = $existingFields[$identifier];
                                $array1[$key][$existingIndex] = array_merge(
                                    $array1[$key][$existingIndex], // 保留原有的所有属性
                                    $item // 覆盖和添加新的键值对
                                );
                            } else {
                                // 如果不存在，则添加
                                $array1[$key][] = $item;
                            }
                        }
                    }
                    // 对于其他索引数组，直接追加
                    else {
                        $array1[$key] = array_merge($array1[$key], $value);
                    }
                } else {
                    // 如果 key 不存在于第一个数组中，则添加。
                    $array1[$key] = $value;
                }
            } else {
                // 如果不是数组，只在第一个数组中没有该键时添加，或者覆盖现有键。
                $array1[$key] = $value;
            }
        }
        return $array1;

    }

}