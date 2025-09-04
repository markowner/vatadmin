<?php
declare(strict_types=1);

namespace plugin\vatadmin\app\controller\system;

use plugin\vatadmin\app\controller\BaseController;
use support\Request;

class SystemController extends BaseController
{
    protected $noNeedLogin = ['index'];

    public function index(Request $request)
    {
        // 获取系统信息
        $sysInfo = [
            '操作系统' => php_uname('s') . ' ' . php_uname('r'),
            '服务器软件' => $_SERVER['SERVER_SOFTWARE'] ?? '未知',
            'PHP版本' => phpversion(),
            'Webman版本' => '',
            '运行用户' => get_current_user(),
            '服务器IP' => gethostbyname(gethostname()),
            '客户端IP' => $request->getRealIp(),
            '当前内存使用' => $this->formatBytes(memory_get_usage()),
            '内存峰值' => $this->formatBytes(memory_get_peak_usage()),
            '磁盘总空间' => $this->formatBytes(disk_total_space(base_path())),
            '磁盘可用空间' => $this->formatBytes(disk_free_space(base_path())),
            '上传限制' => ini_get('upload_max_filesize'),
            '时区设置' => date_default_timezone_get(),
            '运行时间' => $this->getUptime(),
        ];

        return $this->ok('success',$sysInfo);
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
    }

    private function getUptime(): string
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 'Windows 系统暂不支持';
        }
        $uptime = @file_get_contents('/proc/uptime');
        if (!$uptime) return '未知';
        $uptime = explode(' ', $uptime)[0];
        $days = floor($uptime / 86400);
        $hours = floor(($uptime % 86400) / 3600);
        return "{$days} 天 {$hours} 小时";
    }
}