<?php

namespace plugin\vatadmin\service\external\vat;

class Response {

    protected int $code;
    protected string $msg;
    private $data;
    public bool $isOk = false;

    public function __construct($data){
        $this->code = $data['code'] ?? 0;
        $this->msg  = $data['msg'] ?? '';
        $this->data = $data['data'] ?? null;
        $this->isOk = $this->code == 200;
    }

    public function getCode(): int { return $this->code; }

    public function getMsg(): string { return $this->msg; }

    public function getData() { return $this->data; }
}

