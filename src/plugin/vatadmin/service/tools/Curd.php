<?php
/**
 * Curd操作 - TP ORM 重构版
 * join()   = SQL连表 LEFT JOIN
 * with()   = ThinkPHP模型关联预加载（原生with）
 * __call()    透传原生Query方法 where whereIn scope order etc.
 */
namespace plugin\vatadmin\service\tools;

use think\db\Query;
use think\Model;

class Curd
{
    /** @var Model */
    protected $model = null;

    // SQL JOIN连表配置
    protected $joins = [];
    // TP模型关联预加载配置
    protected $withRelations = [];

    protected $fields = '*';
    protected $primaryKey = 'id';
    protected $pageInfo = [];
    protected $sort = '';
    protected $alias = 't0';

    /**
     * @var array 原生查询构造器回调队列（魔术方法收集）
     */
    protected $queryCallbacks = [];

    static $whereIfFiled = [
        'L', 'LIKE', 'LL', 'LIKE_LEFT', 'LR', 'LIKE_RIGHT',
        'IN', 'BETWEEN', 'RANGE', 'EQ', 'GT', 'GE', 'LT', 'LE', 'NE',
        'OR', 'MATCH_AGAINST', 'MATCH_AGAINST_MODE'
    ];

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
        $this->setPrimaryKey($this->model->getPk() ?: 'id');
    }

    static function factory($obj)
    {
        return new self($obj);
    }

    public function initPage($pageInfo)
    {
        $this->setPageInfo($pageInfo);
        if (!empty($pageInfo['tpl_json']['joins'])) {
            $this->join($pageInfo['tpl_json']['joins']);
        }
        if (!empty($pageInfo['tpl_json']['with'])) {
            $this->with($pageInfo['tpl_json']['with']);
        }
        if (!empty($pageInfo['tpl_json']['select_fields'])) {
            $this->fields($pageInfo['tpl_json']['select_fields']);
        }
        return $this;
    }

    public function setPageInfo($pageInfo)
    {
        $this->pageInfo = $pageInfo;
        return $this;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    public function setSort($order)
    {
        $this->sort = $order;
    }

    public function buildFieldTable()
    {
        $table = [];
        foreach ($this->pageInfo['tpl_json']['fields'] as $fields) {
            $table[$fields['field']] = $fields['table_alias'];
        }
        return $table;
    }

    /**
     * SQL连表【替代原来的with方法】
     * @param array $joins [['join' => 'LEFT JOIN', 'table' => 'admin_role', 'alias' => 't1' ,'on' => 't0.roles = t1.id']];
     * @return $this
     */
    public function join($joins)
    {
        $this->joins = $joins;
        return $this;
    }

    /**
     * ThinkPHP原生模型关联预加载
     * 用法和框架完全一致：->with(['user','orders'=>fn($q)=>$q->where(...)])
     */
    public function with($with)
    {
        $this->withRelations = $with;
        return $this;
    }

    /**
     * 内部挂载JOIN语句
     */
    protected function attachJoin(Query $query)
    {
        if (empty($this->joins)) {
            return;
        }
        $query->alias($this->alias);
        foreach ($this->joins as $modelWith) {
            if (is_string($modelWith)) {
                $query->joinRaw($modelWith);
            } else {
                $type = strtolower(str_replace(' ', '', $modelWith['join']));
                //判断如果是inner join 则转换为join
                $type = $type == 'innerjoin' ? 'join' : $type;
                $table = $modelWith['table'] . ' ' . $modelWith['alias'];
                $query->$type($table, $modelWith['on']);
            }
        }
    }

    /**
     * 设置查询字段
     */
    public function fields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * 设置主键
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
        return $this;
    }

    // ======================== 核心查询方法 ========================

    public function select($assist)
    {
        $query = $this->buildBaseQuery($assist);

        // 挂载模型关联预加载
        if (!empty($this->withRelations)) {
            $query->with($this->withRelations);
        }

        $total = $query->count();

        // 排序
        if (!empty($assist['order'])) {
            $query->orderRaw($assist['order']);
        } elseif ($this->sort) {
            $query->orderRaw($this->sort);
        } elseif (!empty($this->joins)) {
            $query->order($this->alias . '.' . $this->primaryKey, 'desc');
        } else {
            $query->order($this->primaryKey, 'desc');
        }

        // limit / 分页
        if (!empty($assist['export'])) {
            if (!empty($assist['limit'])) {
                $query->limit($assist['limit']);
            }
        } else {
            if (!empty($assist['page'])) {
                $size = isset($assist['size']) ? $assist['size'] : 10;
                $query->page($assist['page'], $size);
            } elseif (!empty($assist['limit'])) {
                $query->limit($assist['limit']);
            }
        }
        
        $rows = $query->select()->toArray();
        
        return ['total' => (int)$total, 'list' => $rows];
    }

    public function fetch($assist)
    {
        $query = $this->buildBaseQuery($assist);

        if (!empty($this->withRelations)) {
            $query->with($this->withRelations);
        }

        if (!empty($assist['order'])) {
            $query->orderRaw($assist['order']);
        }
        if (!empty($assist['limit'])) {
            $query->limit($assist['limit']);
        }

        return $query->select()->toArray();
    }

    public function count($assist)
    {
        $query = $this->buildBaseQuery($assist);
        // 清除关联预加载，count不需要with
        $query->removeOption('with');
        return $query->count();
    }

    /**
     * 构建基础查询对象（field + join + where + group）
     */
    protected function buildBaseQuery($assist): Query
    {
        $query = $this->model->newQuery();
        $this->attachJoin($query);

        $select = $assist['field'] ?? $assist['select'] ?? $this->fields;
        $query->field($select);

        if (!empty($assist['where'])) {
            if (is_array($assist['where'])) {
                self::where($query, $assist['where']);
            } elseif (is_string($assist['where'])) {
                $query->whereRaw($assist['where']);
            }
        }

        if (!empty($assist['group'])) {
            $query->group($assist['group']);
        }

        foreach ($this->queryCallbacks as $callback) {
            $callback($query);
        }

        return $query;
    }

    // ======================== Where 构造（参数绑定，防注入） ========================
    public static function where(Query $query, array $whereMap)
    {
        foreach ($whereMap as $k => $v) {
            if (strtoupper($k) === 'SQL') {
                $query->whereRaw($v);
                continue;
            }
            /*
            * 查询字段是否有别名,场景,同一个字段,多种判断条件
            * 例如:
            * $where['user_id'] = ['ge' => 15]
            * $where['user_id|A'] = ['le' => 35]
            * $where['user_id|B'] = ['le' => 35]
            **/
            if (strpos($k, '|') !== false) {
                $fieldK = explode('|', $k);
                $field = $fieldK[0];
            } else {
                $field = $k;
            }

            if (is_string($v) || is_numeric($v)) {
                $query->where($field, $v);
                continue;
            }

            if (is_array($v)) {
                foreach ($v as $k1 => $v1) {
                    $fieldIf = strtoupper($k1);
                    switch ($fieldIf) {
                        case 'L':
                        case 'LIKE':
                            $query->where($field, 'like', "%{$v1}%");
                            break;
                        case 'LL':
                        case 'LIKE_LEFT':
                            $query->where($field, 'like', "%{$v1}");
                            break;
                        case 'LR':
                        case 'LIKE_RIGHT':
                            $query->where($field, 'like', "{$v1}%");
                            break;
                        case 'IN':
                            $arr = is_array($v1) ? $v1 : explode(',', $v1);
                            $query->whereIn($field, $arr);
                            break;
                        case 'NOTIN':
                            $arr = is_array($v1) ? $v1 : explode(',', $v1);
                            $query->whereNotIn($field, $arr);
                            break;
                        case 'BETWEEN':
                        case 'RANGE':
                            if (is_array($v1) && count($v1) == 2) {
                                if ($v1[0] !== '' && $v1[1] !== '') {
                                    $query->whereBetween($field, [$v1[0], $v1[1]]);
                                } elseif ($v1[0] !== '') {
                                    $query->where($field, '>=', $v1[0]);
                                } elseif ($v1[1] !== '') {
                                    $query->where($field, '<=', $v1[1]);
                                }
                            }
                            break;
                        case 'EQ':
                            $query->where($field, $v1);
                            break;
                        case 'EQF':
                            $query->whereRaw("{$field} = {$v1}");
                            break;
                        case 'GT':
                            $query->where($field, '>', $v1);
                            break;
                        case 'GE':
                            $query->where($field, '>=', $v1);
                            break;
                        case 'LT':
                            $query->where($field, '<', $v1);
                            break;
                        case 'LE':
                            $query->where($field, '<=', $v1);
                            break;
                        case 'NE':
                        case 'NEQ':
                            $query->where($field, '<>', $v1);
                            break;
                        case 'NEF':
                            $query->whereRaw("{$field} <> {$v1}");
                            break;
                        case 'OR':
                            $query->whereRaw($v1);
                            break;
                        case 'ETS':
                        case 'EXISTS':
                            $query->whereExists(function ($q) use ($v1) {
                                $q->whereRaw($v1);
                            });
                            break;
                        case 'NETS':
                        case 'NOT_EXISTS':
                            $query->whereNotExists(function ($q) use ($v1) {
                                $q->whereRaw($v1);
                            });
                            break;
                        case 'NULL':
                            $query->whereNull($field);
                            break;
                        case 'NOT_NULL':
                            $query->whereNotNull($field);
                            break;
                        case 'MATCH_AGAINST':
                            $query->whereRaw("MATCH({$field}) AGAINST(?)", [$v1]);
                            break;
                        case 'MATCH_AGAINST_MODE':
                            $query->whereRaw("MATCH({$field}) AGAINST(? IN BOOLEAN MODE)", [$v1]);
                            break;
                    }
                }
            }
        }
    }

    // ======================== 上层工具方法 ========================
    public static function requestAttr($k)
    {
        return request()->input($k);
    }

    public static function filterWhere($where)
    {
        $return = [];
        foreach ($where as $k => $v) {
            if (is_int($k)) {
                if (self::requestAttr($v) !== null && self::requestAttr($v) !== '') {
                    $return[$v] = self::requestAttr($v);
                }
            } elseif (is_string($k)) {
                if (is_int($v)) {
                    $return[$k] = $v;
                } elseif (is_string($v)) {
                    if (in_array(strtoupper($v), self::$whereIfFiled)) {
                        if (self::requestAttr($k) !== null && self::requestAttr($k) !== '') {
                            $return[$k] = [$v => self::requestAttr($k)];
                        }
                    } else {
                        if ($v !== '') {
                            $return[$k] = $v;
                        }
                    }
                } elseif (is_array($v)) {
                    $existsKey = self::array_key_upper_exists('OR', $v);
                    if ($existsKey !== false) {
                        $orStr = '';
                        if (self::requestAttr($k) !== null && self::requestAttr($k) !== '') {
                            $orW = self::requestAttr($k);
                            foreach ($v[$existsKey] as $orKey) {
                                $orStr .= "`{$orKey}` = '{$orW}' OR ";
                            }
                        } else {
                            foreach ($v[$existsKey] as $orKey) {
                                $orStr .= "`{$k}` = '{$orKey}' OR ";
                            }
                        }
                        $orStr = '(' . rtrim($orStr, 'OR ') . ')';
                        $return[$k] = [$existsKey => $orStr];
                    } elseif ($v) {
                        $return[$k] = $v;
                    }
                }
            }
        }
        return $return;
    }

    public function getPage($where, $assist = [])
    {
        $assistNew = [
            'where'  => self::filterWhere($where),
            'page'   => self::requestAttr('page') ?: 1,
            'size'   => self::requestAttr('size') ?: 10,
        ];
        if ($assist) {
            $assistNew = array_merge($assistNew, $assist);
        }
        return $this->select($assistNew);
    }

    public function filterCondition($filters, $conditions)
    {
        $where = [];
        foreach ($filters as $k => $v) {
            if (array_key_exists($k, $conditions)) {
                if ($conditions[$k] !== '=' && $v !== null) {
                    $v = [$conditions[$k] => $v];
                }
                if (count($this->joins) > 0) {
                    if (strpos($k, '.') !== false) {
                        $where[$k] = $v;
                    } else {
                        $where[$this->alias . '.' . $k] = $v;
                    }
                } else {
                    $where[$k] = $v;
                }
            }
        }
        return $where;
    }

    public function buildWhere($where)
    {
        return self::filterWhere($where);
    }

    public function filterConditionWhere($filters, $conditions)
    {
        return $this->buildParams($this->buildWhere($this->filterCondition($filters, $conditions)));
    }

    public function buildParams($where)
    {
        $orderArr = self::requestAttr('order');
        $order    = '';

        if (!$orderArr && $this->sort) {
            $order = $this->sort;
        } else {
            if ($orderArr) {
                if (is_array($orderArr)) {
                    $fieldTable = $this->buildFieldTable();
                    foreach ($orderArr as $v) {
                        if (empty($fieldTable[$v['field']])) continue;
                        $order .= $fieldTable[$v['field']] . '.' . $v['field'] . ' ' . str_replace('end', '', $v['order']) . ',';
                    }
                } else {
                    $order .= $orderArr;
                }
            } else {
                $order .= count($this->joins) > 0 ? $this->alias . '.' . $this->primaryKey . ' DESC' : $this->primaryKey . ' DESC';
            }
        }

        return [
            'select' => $this->fields,
            'where'  => $where,
            'group'  => self::requestAttr('group') ?? '',
            'export' => self::requestAttr('export') ?? 0,
            'order'  => rtrim($order, ','),
            'page'   => self::requestAttr('page') ?? 1,
            'size'   => self::requestAttr('size') ?? 10,
        ];
    }

    public static function array_key_upper_exists($key, $array)
    {
        foreach ($array as $k => $v) {
            if (strtoupper($k) == strtoupper($key)) {
                return $k;
            }
        }
        return false;
    }

    public function lock($id, $status)
    {
        if(is_array($id)){
            return $this->model->whereIn($this->primaryKey, $id)->save(['status' => $status]);
        }
        $row = $this->model->find($id);
        if(!$row) return false;
        return $row->save(['status' => $status]);
    }

        // 新增重置方法
    public function reset()
    {
        $this->joins = [];
        $this->withRelations = [];
        $this->fields = '*';
        $this->sort = '';
        $this->queryCallbacks = [];
        $this->alias = 't0';
        return $this;
    }

    /**
     * 魔术方法：转发所有不存在的方法到查询构造器
     * 示例 ->where() ->whereIn() ->scope() ->order() 等TP原生查询方法
     */
    public function __call(string $method, array $args): self
    {
        $this->queryCallbacks[] = function (Query $query) use ($method, $args) {
            $query->$method(...$args);
        };
        return $this;
    }
}