# VatAdmin

基于 [webman](https://www.workerman.net/doc/webman/) 框架开发的管理后台插件，提供可视化的后台管理界面，支持快速搭建中后台应用。

## 特性

- 用户 / 角色 / 权限管理
- 部门管理（树形结构）
- 菜单管理 & 字典管理
- 系统配置 & 配置分组
- 操作日志 & 登录日志
- 消息通知（公告 / 通知）
- 定时任务管理（基于 [vatcron](https://github.com/markowner/vatcron)）
- CURD 页面可视化搭建
- 会员系统（会员 / 等级 / 积分 / 分组）
- 支持 MySQL / PostgreSQL 数据库

## 环境要求

- PHP >= 8.0
- Composer
- MySQL 5.7+ 或 PostgreSQL 10+
- Redis（可选，用于缓存；也可使用文件缓存）

## 快速开始

### 1. 安装 webman 框架

```bash
composer create-project workerman/webman:~2.0
```

### 2. 安装 vatadmin 插件

```bash
cd webman
composer require vat/vatadmin
```

### 3. 启动服务

```bash
php start.php start
```

### 4. 安装前端页面

```bash
# 克隆前端项目
git clone https://github.com/markowner/vatadmin-naive.git

# 进入目录
cd vatadmin-naive

# 安装依赖
yarn install

# 启动开发服务
yarn dev
```

### 5. 访问系统

打开浏览器访问 **http://localhost:8787**

首次访问将自动进入安装引导页面，根据提示配置数据库连接即可完成安装。安装成功后会自动生成 `.env` 配置文件并初始化数据库。

> 默认管理员账号：`admin` / 密码：`123456`

## 数据库支持

| 数据库 | 版本要求 | 状态 |
|--------|---------|------|
| MySQL | 5.7+ | ✅ 支持 |
| PostgreSQL | 10+ | ✅ 支持 |

安装时可通过前端选择数据库类型，系统将自动适配对应的驱动和初始化脚本。

## 定时任务

内置定时任务管理功能，基于 [vatcron](https://github.com/markowner/vatcron) 组件。

```bash
# 启动服务（调试模式）
php webman vatcron start

# 启动服务（后台守护模式）
php webman vatcron start -d

# 停止服务
php webman vatcron stop

# 重启服务
php webman vatcron restart

# 查看服务状态
php webman vatcron status
```

## 技术栈

| 模块 | 技术 |
|------|------|
| 后端框架 | [webman ~2.0](https://www.workerman.net/doc/webman/) |
| ORM | [think-orm](https://www.kancloud.cn/manual/think-orm) |
| 前端框架 | [Naive UI](https://www.naiveui.com/) + [Vite](https://vitejs.dev/) |
| 定时任务 | [vatcron](https://github.com/markowner/vatcron) |
| 包管理 | [Composer](https://getcomposer.org/) |

## 许可证

MIT
