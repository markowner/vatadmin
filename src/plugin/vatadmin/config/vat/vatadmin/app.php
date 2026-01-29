<?php

return [
    'enable' => true,
    'sql' => [
        'log' => false,
        'display_type' => 10, // 10: 文件，20：输出, 100：自定义回调
        'callback' => function($sql, $time){
            // 自定义日志处理 display_type = 100 
        },
    ],
    'trace' => [
        'log' => true,
        'header_key' => 'trace-id',
    ],
    'aes' => [
        'key' => 'Vat-Admin',
        'cipher_algo' => 'AES-128-CBC',
        'iv' => '',
    ],
    'task' => [
        'listen' => 'text://0.0.0.0:12345',
        'count' => 2,
    ],
];
