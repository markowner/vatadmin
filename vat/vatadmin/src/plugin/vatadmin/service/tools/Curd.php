<?php
/**
 * Curd操作
 */
namespace plugin\vatadmin\service\tools;

use think\facade\Db;

class Curd {

    protected $model = null;
    protected $modelOrm = null;
    public $joins = [];
    public $fields = '*';
    protected $pageInfo = [];
    protected $sort = '';

    const STATE_OK = 100;
    const STATE_NO = 0;
    static $whereIfFiled = ['L','LIKE','LL','LIKE_LEFT','LR','LIKE_RIGHT','IN','BETWEEN','RANGE','EQ','GT','GE','LT','LE','NE','OR','MATCH_AGAINST','MATCH_AGAINST_MODE'];
    static $switchIfField = [
        'EQ' => '等于',
        'GT' => '大于',
        'GE' => '大于等于',
        'LT' => '小于',
        'LE' => '小于等于',
        'NE' => '不等于'
    ];

    private function __construct($obj)
    {
        $this->model = $obj;
        $this->modelOrm = $obj->getTable();
    }

    static function factory($obj){
        return new self($obj);
    }


    public function initPage($pageInfo)
    {
        $this->setPageInfo($pageInfo);
        $pageInfo['tpl_json']['joins'] && $this->with($this->pageInfo['tpl_json']['joins']);
        $pageInfo['tpl_json']['select_fields'] && $this->fields($this->pageInfo['tpl_json']['select_fields']);
        return $this;
    }
    public function setPageInfo($pageInfo)
    {
        $this->pageInfo = $pageInfo;
        return $this;
    }

    public function setSort($order){
        $this->sort = $order;
    }

    public function buildFieldTable(){
        $table = [];
        foreach ($this->pageInfo['tpl_json']['fields'] as $fields){
            $table[$fields['field']] = $fields['table_alias'];
        }
        return $table;
    }

    /**
     * @param $joins [['join' => 'LEFT JOIN', 'table' => 'admin_role', 'alias' => 't1' ,'on' => 't0.roles = t1.id']];
     */
    public function with($joins){
        $this->joins = $joins;
        $this->modelOrm = $this->model->getTable() .' t0';
        foreach ($joins as $k => $modelWith){
            $this->modelOrm .= " " . $modelWith['join'] . " " . $modelWith['table'] . " " . $modelWith['alias'] . " ON " . $modelWith['on'];
        }
        return $this;
    }


    /**
     * 分页查询
     * 格式: [
     *          'field' => 'id,`name`,user_id',
     *          'where' => 'type = 1',
     *          'group'  => 'user_id',
     *          'order'  => 'id DESC',
     *          'page'   => 1,
     *          'size'   => 20
     *      ]
     * @param $where
     * @return array
     */
    public function select($assist){
        $select = $assist['field'] ?? $this->fields;
        $where = '';
        if(isset($assist['where'])){
            if(is_array($assist['where'])){
                $where = $this->where($assist['where']);
            }elseif(is_string($assist['where'])){
                $where = $assist['where'];
            }
        }

        $model = $this->modelOrm;
        $baseSql = 'SELECT ' . $select . " FROM " . $model;
        $sql = '';
        $where && $sql .= " WHERE {$where}";

        //分组|获取总数
        if(isset($assist['group']) && $assist['group']){
            $sql .= " GROUP BY ".$assist['group'];
            //获取总数
            $totalRs = Db::query("SELECT COUNT(*) cnt FROM (" . $baseSql . $sql . ") t");
            $total = $totalRs[0]['cnt'];
        }else{
            //获取总数
            $totalRs =   Db::query("SELECT COUNT(*) cnt FROM {$model} {$sql}");
            $total = $totalRs[0]['cnt'];
        }
        //排序
        isset($assist['order']) && $assist['order'] && $sql .= " ORDER BY ".$assist['order'];

        if($assist['export']){
            isset($assist['limit']) && $assist['limit'] && $sql .= " LIMIT {$assist['limit']}";
        }else{
            //分页
            if($assist['page']){
                $size = isset($assist['size']) ? $assist['size'] : 10;
                $start = ($assist['page'] - 1) * $size;
                $sql .= " LIMIT {$start}, {$size}";
            }elseif($assist['limit']){
                $sql .= " LIMIT {$assist['limit']}";
            }
        }
//        var_dump($baseSql.$sql);
        $rows = Db::query($baseSql.$sql);
        return ['total' => (int)$total, 'list' => $rows];
    }

    /**
     * @param $assist
     */
    public function fetch($assist){
        $select = $assist['select'] ? : '*';
        $where = '';
        if(isset($assist['where'])){
            if(is_array($assist['where'])){
                $where = $this->where($assist['where']);
            }elseif(is_string($assist['where'])){
                $where = $assist['where'];
            }
        }

        $model = $this->modelOrm;
        $sql = 'SELECT ' . $select . " FROM " . $model;
        $where && $sql .= " WHERE {$where}";
        isset($assist['group']) && $assist['group'] && $sql .= " GROUP BY ".$assist['group'];
        isset($assist['order']) && $assist['order'] && $sql .= " ORDER BY ".$assist['order'];
        isset($assist['limit']) && $assist['limit'] && $sql .= " LIMIT {$assist['limit']}";
        return Db::query($sql);
    }

    /**
     * 获取总数
     * @return mixed
     * @throws \think\db\exception\BindParamException
     */
    public function count($assist){
        $select = $assist['field'] ?? $this->fields;
        $where = '';
        if(isset($assist['where'])){
            if(is_array($assist['where'])){
                $where = $this->where($assist['where']);
            }elseif(is_string($assist['where'])){
                $where = $assist['where'];
            }
        }

        $model = $this->modelOrm;
        $baseSql = 'SELECT ' . $select . " FROM " . $model;
        $sql = '';
        $where && $sql .= " WHERE {$where}";

        //分组|获取总数
        if(isset($assist['group']) && $assist['group']){
            $sql .= " GROUP BY ".$assist['group'];
            //获取总数
            $totalRs = Db::query("SELECT COUNT(*) cnt FROM (" . $baseSql . $sql . ") t");
            $total = $totalRs[0]['cnt'];
        }else{
            //获取总数
            $totalRs =   Db::query("SELECT COUNT(*) cnt FROM {$model} {$sql}");
            $total = $totalRs[0]['cnt'];
        }
        return $total;
    }

    /**
     * 分页数据￿
     * @param $where
     * @param $request
     * @param array $assist
     * @return array
     */
    public function getPage($where,$assist = []){
        $assistNew = [
            'where' => self::filterWhere($where),
            'page' => self::requestAttr('page') ?: 1,
            'size' => self::requestAttr('size') ?: 10,
        ];
        $assist && $assistNew = array_merge($assistNew, $assist);
        return $this->select($assistNew);
    }

    /**
     * 生成where条件
     * @param $whereMap
     * @return string
     */
    public static function where($whereMap){
        $return = '';
        foreach ($whereMap as $k => $v){
            /*
           * 查询字段是否有别名,场景,同一个字段,多种判断条件
           * 例如:
           * $where['user_id'] = ['ge' => 15]
           * $where['user_id|A'] = ['le' => 35]
           * $where['user_id|B'] = ['le' => 35]
           **/
            if(strtoupper($k) === 'SQL'){
                $return .= $v;
                continue;
            }

            if(strpos($k,'|') !== false){
                $fieldK = explode('|',$k);
                $k = $fieldK[0];
            }else if(strpos($k,'.') !== false){
                $fieldK = explode('.',$k);
                $k = "`{$fieldK[0]}`.`{$fieldK[1]}`";
            }else{
                $k = "`{$k}`";
            }

            if(is_string($v) || is_numeric($v)){
                $return .= " AND {$k} = '{$v}'";
            }elseif(is_array($v)){
                foreach ($v as $k1 => $v1){
                    $fieldIf = strtoupper($k1);
                    switch ($fieldIf){
                        case 'L' :
                        case 'LIKE' :
                            $return .= " AND {$k} LIKE '%{$v1}%'";
                            break;
                        case 'LL':
                        case 'LIKE_LEFT':
                            $return .= " AND {$k} LIKE '%{$v1}'";
                            break;
                        case 'LR':
                        case 'LIKE_RIGHT':
                            $return .= " AND {$k} LIKE '{$v1}%'";
                            break;
                        case 'IN':
                            if(is_string($v1) || is_numeric($v1)){
                                $inValStr = $v1;
                            }elseif(is_array($v1)){
                                $inValStr = implode(',',$v1);
                            }else{
                                $inValStr = '';
                            }
                            $inValStr && $return .= " AND {$k} IN ({$inValStr})";
                            break;
                        case 'NOTIN':
                            if(is_string($v1) || is_numeric($v1)){
                                $inValStr = $v1;
                            }elseif(is_array($v1)){
                                $inValStr = implode(',',$v1);
                            }else{
                                $inValStr = '';
                            }
                            $inValStr && $return .= " AND {$k} NOT IN ({$inValStr})";
                            break;
                        case 'BETWEEN':
                        case 'RANGE':
                            if(is_array($v1) && count($v1) == 2){
                                if($v1[0] && $v1[1]){
                                    $return .= " AND {$k} BETWEEN '{$v1[0]}' AND '{$v1[1]}'";
                                }elseif($v1[0]){
                                    $return .= " AND {$k} >= '{$v1[0]}'";
                                }elseif($v1[1]){
                                    $return .= " AND {$k} <= '{$v1[1]}'";
                                }
                            }
                            break;
                        case 'EQ':
                            $return .= " AND {$k} = '{$v1}'";
                            break;
                        case 'EQF':
                            $return .= " AND {$k} = {$v1}";
                            break;
                        case 'GT':
                            $return .= " AND {$k} > '{$v1}'";
                            break;
                        case 'GE':
                            $return .= " AND {$k} >= '{$v1}'";
                            break;
                        case 'LT':
                            $return .= " AND {$k} < '{$v1}'";
                            break;
                        case 'LE':
                            $return .= " AND {$k} <= '{$v1}'";
                            break;
                        case 'NE':
                        case 'NEQ':
                            $return .= " AND {$k} <> '{$v1}'";
                            break;
                        case 'NEF':
                            $return .= " AND {$k} <> {$v1}";
                            break;
                        case 'OR':
                            $return .= " AND {$v1}";
                            break;
                        case 'ETS':
                        case 'EXISTS':
                            $return .= " AND EXISTS ({$v1})";
                            break;
                        case 'NETS':
                        case 'NOT_EXISTS':
                            $return .= " AND NOT EXISTS ({$v1})";
                            break;
                        case 'NULL':
                            $return .= " AND {$k} IS NULL";
                            break;
                        case 'NOT_NULL':
                            $return .= " AND {$k} IS NOT NULL";
                            break;
                        case 'MATCH_AGAINST':
                            $return .= " AND MATCH({$k}) AGAINST('{$v1}')";
                            break;
                        case 'MATCH_AGAINST_MODE':
                            $return .= " AND MATCH({$k}) AGAINST('{$v1}' IN BOOLEAN MODE)";
                            break;
                    }
                }
            }
        }

        return ltrim($return,' AND');
    }

    public static function requestAttr($k){
        return request()->input($k);
    }

    /**
     * 生成where条件数组
     * @param $where
     * @param $request
     * @return array
     */
    public static function filterWhere($where){
        $return = [];
        foreach ($where as $k => $v){
            if(is_int($k)){
                if(self::requestAttr($v) !== null && self::requestAttr($v) != ''){
                    $return[$v] = self::requestAttr($v);
                }
            }elseif(is_string($k)){
                if(is_int($v)){
                    $return[$k] = $v;
                }elseif(is_string($v)){
                    if(in_array(strtoupper($v), self::$whereIfFiled)){
                        if(self::requestAttr($k) !== null && self::requestAttr($k) != ''){
                            $return[$k] = [$v => self::requestAttr($k)];
                        }else{
                            if(strpos($k,'.') !== false){
                                $keyAlias = explode('.',$k);
                                $return[$k] = [$v => self::requestAttr($keyAlias[1])];
                            }
                        }
                    }else{
                        $v !== '' && $return[$k] = $v;
                    }
                }elseif(is_array($v)){
                    $existsKey = self::array_key_upper_exists('OR',$v);
                    if($existsKey !== false) {
                        $orStr = '';
                        if(self::requestAttr($k) !== null && self::requestAttr($k) != '') {
                            foreach ($v[$existsKey] as $orKey) {
                                $orW = self::requestAttr($k);
                                $orStr .= "`{$orKey}` = '{$orW}' OR ";
                            }
                        }else{
                            foreach ($v[$existsKey] as $orKey) {
                                $orStr .= "`{$k}` = '{$orKey}' OR ";
                            }
                        }
                        $orStr = '(' . rtrim($orStr, 'OR ') . ')';
                        $return[$k] = [$existsKey => $orStr];
                    }else{
                        $v && $return[$k] = $v;
                    }
                }
            }
        }
        return $return;
    }

    public function filterCondition($filters, $conditions){
        $where = [];
        //补充连表 表名.字段
        foreach ($filters as $k => $v){
            if(array_key_exists($k, $conditions)){
                if($conditions[$k] !== '=' && $v !== null){
                    $v = [$conditions[$k] => $v];
                }
                if(count($this->joins) > 0){
                    if(strpos($k,'.') !== false){
                        $where[$k] = $v;
                    }else{
                        $where['t0.' . $k] = $v;
                    }
                }else{
                    $where[$k] = $v;
                }
            }
        }
        return $where;
    }

    public function filterConditionWhere($filters, $conditions){
        return $this->buildParams($this->buildWhere($this->filterCondition($filters, $conditions)));
    }


    /**
     * 生成where条件sql
     * @param $where
     * @param $request
     */
    public function buildWhere($where){
        return $this->where($this->filterWhere($where));
    }

    /**
     * 构建搜索参数
     * @param $where
     * @return array
     */
    public function buildParams($where){
        $orderArr = $this->requestAttr('order');
        $order = '';
        if(!$orderArr && $this->sort){
            $order = $this->sort;
        }else{
            if($orderArr){
                if(is_array($orderArr)){
                    $fieldTable = $this->buildFieldTable();
                    foreach ($orderArr as $v){
                        if(!$fieldTable[$v['field']]){
                            continue;
                        }
                        $order .= $fieldTable[$v['field']].'.'.$v['field'].' '.str_replace('end','',$v['order']).',';
                    }
                }else{
                    $order .= $orderArr;
                }
            }else{
                $order .= count($this->joins) > 0 ? 't0.id DESC' : 'id DESC';
            }
        }
        $page   = $this->requestAttr('page') ?? 1;
        $size   = $this->requestAttr('size') ?? 10;
        $group  = $this->requestAttr('group') ?? '';
        $export = $this->requestAttr('export') ?? 0;
        $field  = $this->fields;
        return [
           'select' => $field,
           'where'  => $where,
           'group'  => $group,
           'export' => $export,
           'order'  => rtrim($order, ','),
           'page'   => $page,
           'size'   => $size,
        ];
    }

    /**
     * 设置搜索字段
     * @param $fields
     */
    public function fields($fields){
        $this->fields = $fields;
        return $this;
    }

    /**
     * 查询key是否存在【不区分大小写】
     * @param $key
     * @param $array
     * @return bool|int|string
     */
    public static function array_key_upper_exists($key, $array){
        foreach ($array as $k => $v){
            if(strtoupper($k) == strtoupper($key)){
                return $k;
            }
        }
        return false;
    }

    /**
     * 禁用|锁定
     * @param $id
     * @param $status
     * @return mixed
     */
    public function lock($id, $status){
        return $this->model->find($id)->save(['status' => $status]);
    }
}