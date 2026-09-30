<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\basic;

use plugin\vatadmin\app\controller\BaseController;
use plugin\vatadmin\service\tools\DatabaseAdapter;
use support\Request;

class InstallController extends BaseController
{
    protected $noNeedLogin = ['check','install'];

    /**
     * 检测安装
     * @param \support\Request $request
     */
    public function check(Request $request){
        //检察是否已经安装 vat_installed.lock 文件
        $installed = file_exists(base_path() . DIRECTORY_SEPARATOR . '.env');
        return $this->ok('', ['installed' => $installed]);
    }

    /**
     * 安装
     * @param \support\Request $request
     */
    public function install(Request $request){
        $data = $request->post('data');

        $env = base_path() . DIRECTORY_SEPARATOR .'.env';
        clearstatcache();
        if (is_file($env)) {
            return $this->error('管理后台已经安装！如需重新安装，请删除根目录env配置文件并重启');
        }

        // 数据库类型，默认 mysql，支持 pgsql
        $dbType = $data['type'] ?? 'mysql';
        if (!in_array($dbType, ['mysql', 'pgsql'])) {
            return $this->error('不支持的数据库类型，仅支持 mysql 和 pgsql');
        }

        // 初始化适配器
        $adapter = new DatabaseAdapter($dbType);

        // 设置默认值
        $data['charset'] = $data['charset'] ?? $adapter->getDefaultCharset();
        $data['collate'] = $data['collate'] ?? $adapter->getDefaultCollate();
        $data['port'] = $data['port'] ?? $adapter->getDefaultPort();

        try {
            $db = $this->getPdo($adapter, $data['host'], $data['user'], $data['password'], (int)$data['port'], $data['charset']);
         
            // 检查数据库是否存在
            if (!$adapter->checkDatabaseExists($db, $data['database'])) {
                $adapter->createDatabase($db, $data['database'], $data['charset'], $data['collate']);
                // 重新连接已创建的数据库
                $db = $this->getPdo($adapter, $data['host'], $data['user'], $data['password'], (int)$data['port'], $data['charset'], $data['database']);
            } else {
                // 切换到目标数据库
                if ($adapter->isMysql()) {
                    $db->exec("USE \"{$data['database']}\"");
                } else {
                    // PostgreSQL 需要重新连接
                    $db = $this->getPdo($adapter, $data['host'], $data['user'], $data['password'], (int)$data['port'], $data['charset'], $data['database']);
                }
            }
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            if (stripos($message, 'Access denied for user') || stripos($message, 'password')) {
                return $this->error('数据库用户名或密码错误');
            }
            if (stripos($message, 'Connection refused')) {
                return $this->error('Connection refused. 请确认数据库IP端口是否正确，数据库已经启动');
            }
            if (stripos($message, 'timed out')) {
                return $this->error('数据库连接超时，请确认数据库IP端口是否正确，安全组及防火墙已经放行端口');
            }
            throw $e;
        }

        // 检查表是否已存在
        if ($adapter->checkTableExists($db, 'vat_admin_user')) {
            return $this->error('数据库已经安装，请勿重复安装');
        }

        // 获取对应数据库类型的 SQL 文件
        $sql_file = $adapter->getInitSqlFilePath();
        if (!is_file($sql_file)) {
            return $this->error('数据库SQL文件不存在: ' . basename($sql_file));
        }

        // 读取SQL文件内容
        $sql_query = file_get_contents($sql_file);
        if ($sql_query === false) {
            return $this->error('无法读取SQL文件');
        }

        // 执行SQL文件内容
        $db->exec($sql_query);

        $this->generateConfig($dbType);

        // 生成 .env 文件
        $dbTypeEnv = $dbType === 'pgsql' ? 'pgsql' : 'mysql';
        $defaultPort = $adapter->getDefaultPort();
        $env_config = <<<EOF
# 数据库配置
DB_TYPE = {$dbTypeEnv}
DB_HOST = {$data['host']}
DB_PORT = {$data['port']}
DB_NAME = {$data['database']}
DB_USER = {$data['user']}
DB_PASSWORD = {$data['password']}
DB_CHARSET = {$data['charset']}
DB_PREFIX = {$data['prefix']}

# 缓存方式
CACHE_MODE = file

# Redis配置
REDIS_HOST = 127.0.0.1
REDIS_PORT = 6379
REDIS_PASSWORD = ''
REDIS_DB = 0

VAT_ADMIN_PROJECT_NAME = 'vatadmin-naive'
VAT_ADMIN_DICT_KEY = 'vatadmin-dict'
VAT_ADMIN_CONFIG_KEY = 'vatadmin-config'

EOF;
        file_put_contents($env, $env_config);

        // 尝试reload
        if (function_exists('posix_kill')) {
            set_error_handler(function () {});
            posix_kill(posix_getppid(), SIGUSR1);
            restore_error_handler();
        }

        return $this->ok('安装成功');
    }

    /**
     * 生成配置文件
     * @param string $dbType 数据库类型 mysql|pgsql
     */
    protected function generateConfig(string $dbType = 'mysql')
    {
        $connectionName = $dbType === 'pgsql' ? 'pgsql' : 'mysql';
        $defaultPort = $dbType === 'pgsql' ? 5432 : 3306;
        $defaultCharset = $dbType === 'pgsql' ? 'utf8' : 'utf8mb4';

        // 1、think-orm配置文件
        $think_orm_config = <<<EOF
<?php

return [
    'default' => '{$connectionName}',
    'connections' => [
        '{$connectionName}' => [
            // 数据库类型
            'type' => env('DB_TYPE', '{$connectionName}'),
            // 服务器地址
            'hostname' => env('DB_HOST', '127.0.0.1'),
            // 数据库名
            'database' => env('DB_NAME', 'vat'),
            // 数据库用户名
            'username' => env('DB_USER', 'root'),
            // 数据库密码
            'password' => env('DB_PASSWORD', '123456'),
            // 数据库连接端口
            'hostport' => env('DB_PORT', {$defaultPort}),
            // 数据库连接参数
            'params' => [
                // 连接超时3秒
                \PDO::ATTR_TIMEOUT => 3,
            ],
            // 数据库编码默认采用utf8
            'charset' => env('DB_CHARSET', '{$defaultCharset}'),
            // 数据库表前缀
            'prefix' => env('DB_PREFIX', ''),
            // 断线重连
            'break_reconnect' => true,
            // 自定义分页类
            'bootstrap' =>  '',
            // 连接池配置
            'pool' => [
                'max_connections' => 5, // 最大连接数
                'min_connections' => 1, // 最小连接数
                'wait_timeout' => 3,    // 从连接池获取连接等待超时时间
                'idle_timeout' => 60,   // 连接最大空闲时间，超过该时间会被回收
                'heartbeat_interval' => 50, // 心跳检测间隔，需要小于60秒
            ],
        ],
    ],
];
EOF;
        file_put_contents(base_path() . '/config/think-orm.php', $think_orm_config);

        // 2、chache配置文件
        $cache_config = <<<EOF
<?php

return [
    'default' => env('CACHE_MODE', 'file'),
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => runtime_path('cache')
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default'
        ],
        'array' => [
            'driver' => 'array'
        ]
    ]
];
EOF;
        file_put_contents(base_path() . '/config/cache.php', $cache_config);        

        // 3、redis配置文件
        $redis_config = <<<EOF
<?php

return [
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', ''),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_DB', 0),
        'pool' => [
            'max_connections' => 5,
            'min_connections' => 1,
            'wait_timeout' => 3,
            'idle_timeout' => 60,
            'heartbeat_interval' => 50,
        ],
    ]
];
EOF;
        file_put_contents(base_path() . '/config/redis.php', $redis_config);

    }


     /**
     * 获取pdo连接
     * @param DatabaseAdapter $adapter
     * @param string $host
     * @param string $username
     * @param string $password
     * @param int $port
     * @param string $charset
     * @param string|null $database
     * @return \PDO
     */
    protected function getPdo(DatabaseAdapter $adapter, string $host, string $username, string $password, int $port, string $charset, string $database = null): \PDO
    {
        $dsn = $adapter->buildDsn($host, $port, $charset, $database);
        $params = $adapter->getPdoParams();
        return new \PDO($dsn, $username, $password, $params);
    }
}
