<?php
declare(strict_types=1);

namespace plugin\vatadmin\service\tools;

/**
 * CRUD 钩子上下文
 * 统一承载操作类型、输入数据、模型实例、结果及中断控制
 */
class CrudContext
{
    /** 操作类型：add / edit / delete / lock / detail */
    public string $action;

    /** 用户原始输入数据（before 钩子中可修改） */
    public array $input;

    /** 当前模型实例（add 操作前为 null） */
    public $model = null;

    /** 操作结果（save 返回值、delete 影响行数、模型实例等） */
    public $result = null;

    /** 是否中止操作 */
    public bool $abort = false;

    /** 中止时返回的消息 */
    public string $abortMsg = '';

    /** 额外扩展数据（供钩子间传值） */
    public array $extra = [];

    public function __construct(string $action, array $input, $model = null)
    {
        $this->action = $action;
        $this->input  = $input;
        $this->model  = $model;
    }

    /**
     * 阻止操作继续执行
     */
    public function stop(string $msg = '操作被阻止'): void
    {
        $this->abort    = true;
        $this->abortMsg = $msg;
    }
}