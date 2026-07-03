<?php

return [
    'admin.login' => [
        [\plugin\vatadmin\app\event\User::class, 'login']
    ],
    'admin.operation' => [
        [\plugin\vatadmin\app\event\User::class, 'operation']
    ],
    'member.login' => [
        [\plugin\vatadmin\app\event\Member::class, 'login']
    ],
    'member.operation' => [
        [\plugin\vatadmin\app\event\Member::class, 'operation']
    ]
];
