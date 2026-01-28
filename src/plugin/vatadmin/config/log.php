<?php

return [
    'default' => [
        'handlers' => [
            [
                'class' => Monolog\Handler\RotatingFileHandler::class,
                'constructor' => [
                    runtime_path() . '/logs/vatadmin.log',
                    7,
                    Monolog\Logger::DEBUG,
                ],
                'formatter' => [
                    'class' => Monolog\Formatter\LineFormatter::class,
                    'constructor' => [
                        "[%datetime%] %channel%.%level_name%: [trace_id:%extra.trace_id%] %message% %context%\n",
                        'Y-m-d H:i:s',
                        true,
                    ],
                ],
            ],
            'processors' => [
                plugin\vatadmin\app\processor\TraceIdProcessor::class,
            ],
        ],
    ],
];
