<?php

return [
    '' => [
        plugin\vatadmin\app\middleware\Trace::class,
        plugin\vatadmin\app\middleware\SqlListen::class,
        plugin\vatadmin\app\middleware\AuthCheck::class,
    ]
];
