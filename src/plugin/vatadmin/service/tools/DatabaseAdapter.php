<?php
/**
 * 数据库适配器 - 屏蔽不同数据库的差异
 */
namespace plugin\vatadmin\service\tools;

use think\facade\Db;

class DatabaseAdapter
{
    protected static ?self $instance = null;
    protected string $type; // mysql | pgsql

    public function __construct(string $type)
    {
        $this->type = in_array($type, ['pgsql', 'postgres']) ? 'pgsql' : 'mysql';
    }

    /**
     * 获取当前数据库适配器实例（单例）
     */
    public static function getInstance(): self
    {
        if (static::$instance === null) {
            $type = config('database.default', 'mysql');
            // 兼容 think-orm 配置
            if (!$type) {
                $type = config('think-orm.default', 'mysql');
            }
            // 映射 pgsql/postgres 为 pgsql
            if (in_array($type, ['pgsql', 'postgres'])) {
                $type = 'pgsql';
            }
            static::$instance = new static($type);
        }
        return static::$instance;
    }

    /**
     * 重置实例（安装切换数据库后调用）
     */
    public static function resetInstance(): void
    {
        static::$instance = null;
    }

    /**
     * 获取当前数据库类型
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * 是否 MySQL
     */
    public function isMysql(): bool
    {
        return $this->type === 'mysql';
    }

    /**
     * 是否 PostgreSQL
     */
    public function isPgsql(): bool
    {
        return $this->type === 'pgsql';
    }

    // ============================================================
    // PDO 连接相关
    // ============================================================

    /**
     * 构建 PDO DSN
     */
    public function buildDsn(string $host, int $port, string $charset = '', string $database = null): string
    {
        if ($this->isPgsql()) {
            $dsn = "pgsql:host=$host;port=$port";
            if ($database) {
                $dsn .= ";dbname=$database";
            }
            return $dsn;
        }
        // MySQL
        $dsn = "mysql:host=$host;port=$port";
        if ($charset) {
            $dsn .= ";charset=$charset";
        }
        if ($database) {
            $dsn .= ";dbname=$database";
        }
        return $dsn;
    }

    /**
     * 获取 PDO 额外参数
     */
    public function getPdoParams(): array
    {
        if ($this->isPgsql()) {
            return [
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_TIMEOUT => 5,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ];
        }
        return [
            \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_TIMEOUT => 5,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ];
    }

    // ============================================================
    // 数据库操作相关
    // ============================================================

    /**
     * 检查数据库是否存在
     */
    public function checkDatabaseExists(\PDO $db, string $database): bool
    {
        if ($this->isPgsql()) {
            $stmt = $db->prepare("SELECT 1 FROM pg_database WHERE datname = ?");
            $stmt->execute([$database]);
            return !empty($stmt->fetchAll());
        }
        // MySQL
        $stmt = $db->query("SHOW DATABASES LIKE '{$database}'");
        return !empty($stmt->fetchAll());
    }

    /**
     * 创建数据库
     */
    public function createDatabase(\PDO $db, string $database, string $charset = 'utf8mb4', string $collate = ''): void
    {
        if ($this->isPgsql()) {
            // PostgreSQL 不支持 IF NOT EXISTS 语法（9.1之前），用查询判断
            $db->exec("CREATE DATABASE \"{$database}\" ENCODING 'UTF8'");
        } else {
            $collateSql = $collate ? " COLLATE {$collate}" : '';
            $db->exec("CREATE DATABASE \"{$database}\" CHARSET {$charset}{$collateSql}");
        }
    }

    /**
     * 检查表是否存在
     */
    public function checkTableExists(\PDO $db, string $table): bool
    {
        if ($this->isPgsql()) {
            $stmt = $db->prepare("SELECT 1 FROM pg_tables WHERE schemaname = 'public' AND tablename = ?");
            $stmt->execute([$table]);
            return !empty($stmt->fetchAll());
        }
        // MySQL
        $stmt = $db->query("SHOW TABLES LIKE '{$table}'");
        return !empty($stmt->fetchAll());
    }

    // ============================================================
    // 表注释查询
    // ============================================================

    /**
     * 获取表注释
     */
    public function getTableComment(string $table): string
    {
        $db = Db::connect();
        if ($this->isPgsql()) {
            $result = $db->query(
                "SELECT obj_description(c.oid) as comment FROM pg_class c JOIN pg_namespace n ON c.relnamespace = n.oid WHERE c.relname = ? AND n.nspname = 'public'",
                [$table]
            );
            return $result[0]['comment'] ?? '';
        }
        // MySQL
        $result = $db->query(
            "SELECT TABLE_COMMENT as comment FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?",
            [$table]
        );
        return $result[0]['comment'] ?? '';
    }

    // ============================================================
    // 全文检索
    // ============================================================

    /**
     * 获取全文检索 SQL 片段
     * @param string $field 字段名
     * @param string $keyword 关键词（绑定参数占位）
     * @param bool $booleanMode 是否布尔模式
     * @return array [sql, bindings]
     */
    public function buildFullTextSearch(string $field, string $keyword, bool $booleanMode = false): array
    {
        if ($this->isPgsql()) {
            $sql = "to_tsvector('simple', COALESCE({$field}::text, '')) @@ plainto_tsquery('simple', ?)";
            return [$sql, [$keyword]];
        }
        // MySQL
        if ($booleanMode) {
            $sql = "MATCH({$field}) AGAINST(? IN BOOLEAN MODE)";
        } else {
            $sql = "MATCH({$field}) AGAINST(?)";
        }
        return [$sql, [$keyword]];
    }

    // ============================================================
    // SQL 文件路径
    // ============================================================

    /**
     * 获取初始化 SQL 文件路径
     */
    public function getInitSqlFilePath(): string
    {
        if ($this->isPgsql()) {
            return base_path() . '/plugin/vatadmin/db/vatadmin-1.0-pgsql.sql';
        }
        return base_path() . '/plugin/vatadmin/db/vatadmin-1.0.sql';
    }

    /**
     * 获取默认端口
     */
    public function getDefaultPort(): int
    {
        return $this->isPgsql() ? 5432 : 3306;
    }

    /**
     * 获取默认字符集
     */
    public function getDefaultCharset(): string
    {
        return $this->isPgsql() ? 'utf8' : 'utf8mb4';
    }

    /**
     * 获取默认 Collation
     */
    public function getDefaultCollate(): string
    {
        return $this->isPgsql() ? '' : 'utf8mb4_general_ci';
    }
}
