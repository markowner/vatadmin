<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller;

use plugin\vatadmin\app\model\admin\AdminDepartment;
use plugin\vatadmin\app\model\admin\AdminRole;
use plugin\vatadmin\app\model\admin\AdminUser;
use plugin\vatadmin\app\model\UploadLog;
use plugin\vatadmin\app\model\Pages;
use plugin\vatadmin\service\tools\Curd;
use Rap2hpoutre\FastExcel\FastExcel;
use plugin\vatadmin\service\internal\task\TaskClient;
use Shopwwi\WebmanFilesystem\Facade\Storage;
use support\Log;
use support\Redis;
use support\Request;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;
use Tinywan\Jwt\JwtToken;
use Illuminate\Support\Collection;
use plugin\vatadmin\service\tools\Enum;
use Vat\Validate;

class BaseController{

    protected $model = null; //page model
    protected $confine = 0; //数据边界 0：全部，1：自己，2：自动
    public $pageInfo = []; //page配置信息
    public $headings = []; //导出表头
    public $downloadLimit = 5000; //导出限制
    private $properties = [];
    protected $tableCode = 0; //标识,用于区分多个页面相同的表名
    protected $treeField = 'id|parent_id'; //tree字段，格式：id|parent_id

    /**
     * 成功返回格式
     */
    public function ok($msg = 'success', $data = []){
        return json([
            'code'  => 200,
            'msg'   => $msg,
            'data'  => $data
        ]);
    }

    /**
     * 错误返回格式
     */
    public function error($msg, $code = 0, $data = []){
        return json([
            'code'  => $code,
            'msg'   => $msg,
            'data'  => $data
        ]);
    }

    /**
     * 列表
     * @param Request $request
     */
    public function list(Request $request){
        $this->initPage();
        $filter = $request->input('filter') ? json_decode($request->input('filter'), true) : [];
        $curd = Curd::factory($this->model);
        $curd->initPage($this->pageInfo);
        $where = $curd->filterCondition($filter, $this->buildCondition());
        $calledClass = get_called_class();
        $this->injectConfineData($where);
        method_exists($calledClass, 'injectWhere') && $this->injectWhere($where);
        $params = $curd->buildParams($curd->buildWhere($where));
        method_exists($calledClass, 'injectSelectParams') && $this->injectSelectParams($params);
        $rows = $curd->select($params);
//        $rows = $curd->select($curd->filterConditionWhere($filter, $this->buildCondition()));
        $fieldDict = $this->buildDict();
        $calledClass = get_called_class();
        method_exists($calledClass, 'injectMap') && $this->injectMap();
        foreach ($rows['list'] as $k => &$row){
            foreach ($fieldDict as $field => $dict){
                $row[$field] !== '' && $row[$field] !== null && $row[$field.'_desc'] = $dict[$row[$field]];
            }
            method_exists($calledClass, 'injectAttr') && $this->injectAttr($row);
        }
        return $this->ok('success', $rows);
    }

    /**
     * 列表
     * @param Request $request
     */
    public function listTree(Request $request){
        $this->initPage();
        $filter = $request->input('filter') ? json_decode($request->input('filter'), true) : [];
        $curd = Curd::factory($this->model);
        $curd->initPage($this->pageInfo);
        $rows = $curd->fetch($curd->filterConditionWhere($filter, $this->buildCondition()));
        $fieldDict = $this->buildDict();
        $calledClass = get_called_class();
        foreach ($rows as $k => &$row){
            foreach ($fieldDict as $field => $dict){
                $row[$field] !== '' && $row[$field] !== null && $row[$field.'_desc'] = $dict[$row[$field]];
            }
        }
        if(method_exists($calledClass, 'buildTree')){
            $this->buildTree($rows);
        }else{
            $rows = tree($rows, 0, $this->treeField);
        }
        return $this->ok('success', ['list' => $rows, 'total' => count($rows)]);
    }

    /**
     * 列表
     * @param Request $request
     */
    public function listOwner(Request $request){
        $this->initPage();
        $filter = $request->input('filter') ? json_decode($request->input('filter'), true) : [];
        $curd = Curd::factory($this->model);
        $curd->initPage($this->pageInfo);
        $where = $curd->filterCondition($filter, $this->buildCondition());
        $calledClass = get_called_class();
        method_exists($calledClass, 'injectOwner') && $this->injectOwner($where);
        $params = $curd->buildParams($curd->buildWhere($where));
        method_exists($calledClass, 'injectSelectParams') && $this->injectSelectParams($params);
        $rows = $curd->select($params);
        $fieldDict = $this->buildDict();
        method_exists($calledClass, 'injectMap') && $this->injectMap();
        foreach ($rows['list'] as $k => &$row){
            foreach ($fieldDict as $field => $dict){
                $row[$field] !== '' && $row[$field] !== null && $row[$field.'_desc'] = isset($dict[$row[$field]]) ? $dict[$row[$field]] : '';
            }
            method_exists($calledClass, 'injectAttr') && $this->injectAttr($row);
        }
        return $this->ok('success', $rows);
    }

    public function injectOwner(&$where)
    {
        $where['admin_id'] = JwtToken::getCurrentId();
    }

    public function injectConfineData(&$where){
        if($this->confine === 1){
            $where['admin_id'] = JwtToken::getCurrentId();
        }else if($this->confine === 2){
            $user = JwtToken::getUser();
            $roles = explode(',', $user['roles']);
            if(!in_array(1, $roles)){
                //获取全部角色数据
                $dataTypes = AdminRole::getDataTypeByIds($roles);
                $userIds = [];
                $adminUser = JwtToken::getUser();
                foreach (array_unique($dataTypes) as $type){
                    switch ($type){
                        case AdminRole::DATA_TYPE_ALL:
                            return true;
                        case AdminRole::DATA_TYPE_DEPART:
                            //本部门
                            if($adminUser->deparment_id){
                                $users = AdminUser::getByDepartmentId($adminUser->deparment_id);
                                $userIds = array_merge($userIds, $users);
                            }
                            break;
                        case AdminRole::DATA_TYPE_DEPART_CHILD:
                            //本部门及以下
                            if($adminUser->deparment_id){
                                $departmentIds = AdminDepartment::getChildIds($adminUser->deparment_id);
                                $users = AdminUser::getByDepartmentIds($departmentIds);
                                $userIds = array_merge($userIds, $users);
                            }
                        case AdminRole::DATA_TYPE_SELF:
                            //自己
                            $userIds[] = $adminUser->id;
                            break;
                        default:
                            break;

                    }
                }
                $where['admin_id'] = ['in', $userIds];
            }
        }
    }

    /**
     * tree 级联结构数据
     * @param Request $request
     */
    public function tree(Request $request){
        $list = $this->model::where('status', Enum::STATUS_OK)->select();
        $list = treeSimple($list, 0, $this->treeField);
        return $this->ok('success', ['list' => $list]);
    }

    /**
     * 禁用解锁
     * @param Request $request
     */
    public function lock(Request $request){
        $ids = $request->input('ids');
        $status = $request->input('status');
        if(!$ids){
            return $this->error('参数错误');
        }
        $model = $this->model->whereIn('id', $ids);
        $calledClass = get_called_class();
        $data = ['ids' => $ids, 'status' => $status];
        method_exists($calledClass, 'before') && $this->before('lock', $data);
        $rs = $model->save(['status' => $status]);
        if($rs !== false){
            method_exists($calledClass, 'after') && $this->after('lock', $data);
            return $this->ok('操作成功');
        }
        return $this->error('操作失败');
    }

    /**
     * 添加编辑
     * @param Request $request
     */
    public function edit(Request $request){
        $data = $request->post();
        $this->initPage();
        if(!isset($this->pageInfo['tpl_json']['setting']['edit_validate']) || $this->pageInfo['tpl_json']['setting']['edit_validate']){
            $this->validate($this->buildValidate());
        }
        //操作数据前
        $calledClass = get_called_class();
        method_exists($calledClass, 'before') && $this->before('edit', $data);
        if($data['id']) {
            //编辑
            $model = $this->model->find($data['id']);
            $model && $rs = $model->save($data);
        } else {
            //添加
            $model = $rs = $this->model->create($data);
        }
        if($rs) {
            method_exists($calledClass, 'after') && $this->after('edit', $model);
            return $this->ok('保存成功');
        }

        return $this->error('保存失败');
    }

    /**
     * 删除
     * @param Request $request
     */
    public function delete(Request $request){
        $ids = $request->input('ids');
        if(!$ids){
            return $this->error('参数错误');
        }
        if(!is_array($ids)){
            $ids = [$ids];
        }
        $calledClass = get_called_class();
        $model = $this->model->whereIn('id', $ids);
        method_exists($calledClass, 'before') && $this->before('delete', $ids, $model);
        $rs = $model->delete();
        if($rs){
            method_exists($calledClass, 'after') && $this->after('delete', $ids, $model);
            return $this->ok('删除成功');
        }
        return $this->error('删除失败');
    }

    /**
     * 导入
     */
    public function import(Request $request){
        //导入事件，不传默认只是上传，不需要后续操作，传了就需要在队列执行函数中对应上
        $params = $request->input('params', '');
        $params = $params ? json_decode($params, true) : '';
        $file = $request->file('file');
        //存储类型 默认为本地存储：local，阿里云：oss，腾讯云：cos，七牛：qiniu
        $storage = $request->input('storage');
        if(!$storage){
            if(isset($params['storage']) && $params['storage']){
                $storage = $params['storage'];
            }else{
                //获取默认设置
                $storage_type = \plugin\vatadmin\app\model\admin\AdminConfig::getCodeConfig('storage_type');
                $storage = $storage_type ?? 'public';
            }
        }
        try {
            $result = Storage::adapter($storage)->path('storage/'.date('Ymd'))->upload($file);
        }catch (\Throwable $e){
            Log::info('上传失败', ['params' => $params, 'msg' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new BadRequestHttpException('上传失败');
        }

        //上传记录
        $UploadLogRs = UploadLog::create([
            'admin_id'      => $request->admin_id,
            'params'        => $params ? json_encode($params, JSON_UNESCAPED_UNICODE) : '',
            'event_key'     => isset($params['event']) && $params['event'] ? $params['event'] : '',
            'content'       => json_encode($result, JSON_UNESCAPED_UNICODE),
        ]);
        if(isset($params['event']) && $params['event']){
            $heading = [];
            if($params['event'] === 'pages'){
                $this->initPage();
                foreach ($this->pageInfo['tpl_json']['fields'] as $fields){
                    $heading[$fields['comment']] = $fields['field'];
                }
            }

            //加入异步任务执行
            TaskClient::send(['id' => $UploadLogRs->id, ['params' => ['heading' => $heading]]], 'import');
            return $this->ok('上传成功，已加入导入数据队列请等待执行', ['url' => $result->file_url,'path' => $result->file_name]);
        }
        return $this->ok('上传成功', ['url' => $result->file_url,'path' => $result->file_name]);
    }

    /**
     * 下载
     */
    public function download(Request $request){
        $this->initPage();
        $filter = $request->input('filter') ? json_decode($request->input('filter'), true) : [];
        $curd = Curd::factory($this->model);
        $curd->initPage($this->pageInfo);
        $where = $curd->filterCondition($filter, $this->buildCondition());
        $calledClass = get_called_class();
        $this->injectConfineData($where);
        method_exists($calledClass, 'injectWhere') && $this->injectWhere($where);
        $params = $curd->buildParams($curd->buildWhere($where));
        if($curd->count($params) < $this->downloadLimit){
            $rows = $curd->fetch($params);
            $fieldDict = $this->buildDict();
            $calledClass = get_called_class();
            method_exists($calledClass, 'injectMap') && $this->injectMap();

            $heading = [];
            foreach ($this->pageInfo['tpl_json']['fields'] as $fields){
                $heading[$fields['field']] = $fields['comment'];
            }

            foreach ($rows as $k => &$row){
                foreach ($fieldDict as $field => $dict){
                    if($row[$field] !== '' && $row[$field] !== null){
                        $row[$field.'_desc'] = $dict[$row[$field]];
                        if(!isset($heading[$field.'_desc'])){
                            $heading[$field.'_desc'] = $heading[$field];
                            unset($heading[$field]);
                        }
                    }
                }
                if(method_exists($calledClass, 'injectAttr')){
                    $exportColumn = $this->injectAttr($row);
                    $heading = array_merge($heading, $exportColumn);
                }
            }
            $name = ($this->pageInfo['build_menu_name'] ? : $this->pageInfo['name']) . date('YmdHis');
            return $this->_download($this->headings ? : $heading, $rows, 'csv', $name);
        }

        //异步任务下载
        TaskClient::send(['event' => 'pages','params' => $params, 'calledClass' => $calledClass, 'this' => $this, 'curd' => $curd, 'admin_id' => VatUid() ], 'download');
        return $this->ok('异步下载中，请等待...');
    }

    /**
     * 下载文件
     * @param $data
     * @param $type
     * @param $filename
     * @return \support\Response
     */
    private function _download($headings, $data, $type, $filename){
        ob_start();
        //文件名
        $filename = $filename.'.'.$type;
        (new FastExcel(Collection::make($data)))->download($filename, function($row) use ($headings){
            $map = [];
            foreach ($headings as $field => $name){
                $map[$name] = $row[$field];
            }
            return $map;
        });
        $fileContent = ob_get_contents();
        ob_end_clean();

        // 对文件名进行 URL 编码
        $encodedFilename = rawurlencode($filename);

        $contentType = [
            'csv' => 'text/csv; charset=UTF-8',
            'xlsx' => 'application/vnd.ms-excel',
        ];

        return response()
            ->withHeader('Content-Type', $contentType[$type])
            ->withHeader('Content-Disposition', "attachment; filename*=UTF-8''{$encodedFilename}")
            ->withHeader('Cache-Control', 'max-age=0')
            ->withBody($fileContent);
    }

    /**
     * 页面初始化
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function initPage(){
        $pages = Pages::where('table', $this->model->getTable())->where('table_code', $this->tableCode)->find();
        // 创建一个新数组存储处理后的数据，避免直接修改模型对象
        $pageInfo = $pages ? $pages->toArray() : [];
        $pageInfo['tpl_json'] = $pageInfo['tpl_json'] ? json_decode($pageInfo['tpl_json'], true) : [];
        $this->pageInfo = $pageInfo;
    }

    /**
     * 构建条件
     * @return array
     */
    public function buildCondition(){
        $fieldCondition = [];
        foreach ($this->pageInfo['tpl_json']['fields'] as $fields){
            if($fields['search']){
                $fieldCondition[$fields['field']] = $fields['condition'];
                $fields['table_alias'] && $fieldCondition[$fields['table_alias'].'.'.$fields['field']] = $fields['condition'];
            }
        }
        return $fieldCondition;
    }

    /**
     * 构建字典
     * @return array
     */
    public function buildDict(){
        //获取字典
        $dictMap = Redis::hGetAll(env('VAT_ADMIN_DICT_KEY'));
        $dict = [];
        foreach ($this->pageInfo['tpl_json']['fields'] as $fields){
            if(isset($fields['config']['dict']) && $fields['config']['dict']){
                //格式化
                $output = [];
                if($dictMap[$fields['config']['dict']]){
                    $values = json_decode($dictMap[$fields['config']['dict']], true);
                    foreach ($values as $item) {
                        $output[$item['value']] = $item['label'];
                    }
                }
                //获取字典
                $dict[$fields['field']] = $output;
            }
        }
        return $dict;
    }

    /**
     * 构建验证规则
     * @return array
     */
    public function buildValidate(){
        $validate = [];
        foreach ($this->pageInfo['tpl_json']['fields'] as $fields){
            if($fields['form'] && $fields['form_required']){
                $rules = isset($fields['rules']) && $fields['rules'] ? explode('|', $fields['rules']) : ['required'];
                if(in_array($fields['form_view'], ['input', 'input-pair', 'input_number','password', 'textarea'])){
                    $messagePrefix = '请输入';
                }else{
                    $messagePrefix = '请选择';
                }
                $buildRules = [];
                foreach ($rules as $rule){
                    if($rule == 'required'){
                        $buildRules[$rule] = $messagePrefix . $fields['comment'];
                    }else{
                        $buildRules[$rule] = $messagePrefix .'正确的'. $fields['comment'];
                    }
                }
                $validate[$fields['field']] = $buildRules;
            }
        }
        return $validate;
    }

    /**
     * 验证数据
     */
    public function validate($rules){
        Validate::setErrorHandler(BadRequestHttpException::class);
        Validate::check(request()->all(),$rules);
    }    


    public function __set($name, $value) {
        // 动态设置属性
        $this->properties[$name] = $value;
    }

    public function __get($name) {
        // 获取动态属性
        return isset($this->properties[$name]) ? $this->properties[$name] : null;
    }
}