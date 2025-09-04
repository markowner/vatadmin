<?php

return [
    'user.login' => [
        [\plugin\vatadmin\app\event\User::class, 'login']
    ],
    'user.operation' => [
        [\plugin\vatadmin\app\event\User::class, 'operation']
    ]
];
