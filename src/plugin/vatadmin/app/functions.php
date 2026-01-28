<?php
/**
 * Here is your custom functions.
 */


function splitPathOptimized($path) {
    // 找到最后一个斜杠的位置
    $lastSlashPos = strrpos($path, '/');

    // 如果找到了斜杠
    if ($lastSlashPos !== false) {
        // 分割路径为两部分
        $firstPart = substr($path, 0, $lastSlashPos);
        $secondPart = substr($path, $lastSlashPos + 1);
        return [$firstPart, $secondPart];
    }

    // 如果没有斜杠，返回原始字符串和空值
    return ['', $path];
}

function stub_path()
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR .'stub';
}


function recursiveMkdir($path, $mode = 0777, $recursive = true) {
    // 如果目录已经存在，则直接返回true
    if (is_dir($path)) {
        return true;
    }

    // 获取父目录
    $parentDir = dirname($path);

    // 如果父目录不是根目录（即'/'或'C:\'等形式），则递归创建父目录
    if ($parentDir !== '.' && $parentDir !== '/' && !is_dir($parentDir)) {
        // 递归调用自身以创建父目录
        if (!recursiveMkdir($parentDir, $mode, $recursive)) {
            return false;
        }
    }

    // 创建当前目录
    return mkdir($path, $mode, $recursive);
}

function vat_base_build($json, $filename){
    $path = BASE_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . getenv('VAT_ADMIN_PROJECT_NAME'). DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'vat'. DIRECTORY_SEPARATOR . $filename;
    file_put_contents($path, json_encode($json,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
}


function cdnUrl($url) {
    if(!$url){
        return $url;
    }
    // 解析URL
    $parsedUrl = parse_url($url);

    // 检查是否具有http/https协议
    if (isset($parsedUrl['scheme']) && ($parsedUrl['scheme'] === 'http' || $parsedUrl['scheme'] === 'https')) {
        return $url;
    }

    // 如果没有协议，则拼接基础域名
    $cdnUrl = \plugin\vatadmin\app\model\admin\AdminConfig::getCodeConfig('cdn_url');
    return $cdnUrl . '/' . ltrim($url, '/');
}

function tree($data, $parent_id = 0, $field = 'id|parent_id')
{
    $tree = array();
    $fields = explode('|', $field);
    if(count($fields) != 2){
        $fields[1] = 'parent_id';
    }
    foreach ($data as $item) {
        if ($item[$fields[1]] == $parent_id) {
            $children = tree($data, $item[$fields[0]], $field);
            $item['children'] = $children ? : null;
            $tree[] = $item;
        }
    }
    return $tree;
}

function treeSimple($data, $parent_id = 0, $field = 'id|parent_id')
{
    $tree = array();
    $fields = explode('|', $field);
    if(count($fields) != 2){
        $fields[1] = 'parent_id';
    }
    foreach ($data as $item) {
        if ($item[$fields[1]] == $parent_id) {
            $children = treeSimple($data, $item[$fields[0]], $field);
            $tree[] = [
                'value'         => $item[$fields[0]],
                'key'           => $item[$fields[0]],
                'label'         => $item['name'],
                'parent_id'     => $item[$fields[1]],
                'children'      => $children ? : null,
            ];
        }
    }
    return $tree;
}

function VatUid(){
    return \Tinywan\Jwt\JwtToken::getCurrentId();
}

function VatUserExtendVal($key){
    return \Tinywan\Jwt\JwtToken::getExtendVal($key);
}

function VatUser(){
    return \Tinywan\Jwt\JwtToken::getUser();
}