# vatadmin
基于webman框架开发的管理后台插件

### 首先安装webman框架
```bash
composer create-project workerman/webman:~2.0
```
### 在webman框架中安装vatadmin插件
```bash
composer require vat/vatadmin
```

### 启动webman
```bash
php start.php start
```
### 后台页面


```
安装后台管理页面

# 克隆项目
git clone https://github.com/markowner/vatadmin-naive.git

# 进入项目目录
cd vatadmin-naive

# yarn安装依赖
yarn install

# 运行项目
yarn dev

http://localhost:8787，首次访问会检测是否安装过（检测.env配置文件是否存在），没有配置文件会进入安装页面，引导安装从而生成配置文件及生成数据库，
安装成功后，点击跳转访问后台页面，默认账号：admin，密码：123456
```
### 内置定时任务 [vatcron https://github.com/markowner/vatcron]

```
# 启动服务 (调试模式)
php webman vatcron start

# 启动服务 (后台守护模式)
php webman vatcron start -d

# 停止服务
php webman vatcron stop

# 重启服务
php webman vatcron restart

# 查看服务状态
php webman vatcron status
```