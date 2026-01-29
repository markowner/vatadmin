<?php

namespace plugin\vatadmin\service\tools;

class Aes{

    private $key = 'Vat-Admin';

    private $iv = '';

    public $method = 'AES-128-CBC';

    private $options = 0;

    public $bin2hex = false;

    public function __construct($config)
    {
        $this->key = config('plugin.vat.vatadmin.app.aes.key', 'Vat-Admin');
        $this->iv = config('plugin.vat.vatadmin.app.aes.iv', '');
        $this->method = config('plugin.vat.vatadmin.app.aes.cipher_algo', 'AES-128-CBC');

        foreach($config as $k => $v){
            $this->$k = $v;
        }
    }

    /**
     * 加密
     */
    public function encrypt($data){
        $encrypted = base64_encode(openssl_encrypt($data, $this->method, $this->key, $this->options, $this->iv));
        if($this->bin2hex){
            return bin2hex($encrypted);
        }
        return $encrypted;
    }


    /**
     * 解密
     */
    public function decrypt($data){
        if($this->bin2hex){
            $encrypted = hex2bin($data);
        }
        return openssl_decrypt(base64_decode($encrypted), $this->method, $this->key, $this->options, $this->iv);
    }

}