<?php

namespace plugin\vatadmin\app\controller\basic;

use plugin\vatadmin\app\controller\BaseController;
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

        try {
            $data['charset'] = $data['charset'] ?? 'utf8mb4';
            $data['collate'] = $data['collate'] ?? 'utf8mb4_unicode_ci';
            $db = $this->getPdo($data['host'], $data['user'], $data['password'], $data['port'], $data['charset']);
         
            $stmt = $db->query("show databases like '{$data['database']}'");
            if (empty($stmt->fetchAll())) {
                $stmt = $db->exec("create database {$data['database']} CHARSET utf8mb4 COLLATE {$data['collate']}");
                $stmt = $db->exec("use {$data['database']}");
            } else {
                $stmt = $db->exec("use {$data['database']}");
            }
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            if (stripos($message, 'Access denied for user')) {
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

        $stmt = $db->query("show tables like 'vat_admin_user'");
        $tables = $stmt->fetchAll();
        if (count($tables) > 0) {
            return $this->error('数据库已经安装，请勿重复安装');
        }

        $sql_file = base_path() . '/plugin/vatadmin/db/vatadmin-1.0.sql';
        if (!is_file($sql_file)) {
            return $this->error('数据库SQL文件不存在');
        }

        // 读取SQL文件内容
        $sql_query = file_get_contents($sql_file);
        if ($sql_query === false) {
            return $this->error('无法读取SQL文件');
        }

        // 执行SQL文件内容（注意：这里存在潜在安全风险，确保SQL文件来源可靠）
        $db->exec($sql_query);

        $this->generateConfig();

        $env_config = <<<EOF
# 数据库配置
DB_TYPE = mysql
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

VAT_ADMIN_PROJECT_NAME = 'VatAdmin'
VAT_ADMIN_DICT_KEY = 'VatAdminDict'
VAT_ADMIN_CONFIG_KEY = 'VatAdminConfig'

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
     */
    protected function generateConfig()
    {
        // 1、think-orm配置文件
        $think_orm_config = <<<EOF
<?php

return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type' => env('DB_TYPE', 'mysql'),
            // 服务器地址
            'hostname' => env('DB_HOST', '127.0.0.1'),
            // 数据库名
            'database' => env('DB_NAME', 'vat'),
            // 数据库用户名
            'username' => env('DB_USER', 'root'),
            // 数据库密码
            'password' => env('DB_PASSWORD', '123456'),
            // 数据库连接端口
            'hostport' => env('DB_PORT', 3306),
            // 数据库连接参数
            'params' => [
                // 连接超时3秒
                \PDO::ATTR_TIMEOUT => 3,
            ],
            // 数据库编码默认采用utf8
            'charset' => env('DB_CHARSET', 'utf8mb4'),
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
     * @param $host
     * @param $username
     * @param $password
     * @param $port
     * @param $database
     * @return \PDO
     */
    protected function getPdo($host, $username, $password, $port, $charset, $database = null): \PDO
    {
        $dsn = "mysql:host=$host;port=$port;charset=$charset";
        if ($database) {
            $dsn .= "dbname=$database";
        }
        $params = [
            \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_TIMEOUT => 5,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ];
        return new \PDO($dsn, $username, $password, $params);
    }
}
