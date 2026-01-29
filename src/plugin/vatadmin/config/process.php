<?php
return [
     //异步任务
    'Task'  => [
        'handler'  => plugin\vatadmin\process\Task::class,
        'listen' => config('plugin.vat.vatadmin.app.task.listen'),
        'count' => config('plugin.vat.vatadmin.app.task.count'),
    ],
];
