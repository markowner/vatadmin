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
### 访问后台
```
http://localhost:8787，首次访问会检测是否安装过（检测.env配置文件是否存在），没有配置文件会进入安装页面，引导安装从而生成配置文件及生成数据库，
安装成功后，点击跳转访问后台页面，默认账号：admin，密码：123456
```