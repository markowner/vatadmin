<?php
return [
     //异步任务
    'Task'  => [
        'handler'  => plugin\vatadmin\process\Task::class,
        'listen' => 'text://0.0.0.0:12345',
        'count' => 2
    ],
];
