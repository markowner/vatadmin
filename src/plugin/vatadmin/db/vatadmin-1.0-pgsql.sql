-- ----------------------------
-- PostgreSQL 版本初始化脚本
-- ----------------------------

-- ----------------------------
-- Table structure for vat_admin_config
-- ----------------------------
DROP TABLE IF EXISTS vat_admin_config;
CREATE TABLE vat_admin_config (
  id SERIAL PRIMARY KEY,
  group_id integer NOT NULL DEFAULT 0,
  name varchar(64) NOT NULL,
  code varchar(64) NOT NULL,
  value varchar(1280) NOT NULL DEFAULT '',
  view varchar(64) NOT NULL DEFAULT '',
  view_option_json varchar(1280) NOT NULL DEFAULT '',
  sortrank integer NOT NULL DEFAULT 0,
  open_front integer NOT NULL DEFAULT 0,
  status integer NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uni_code UNIQUE (code)
);
COMMENT ON TABLE vat_admin_config IS '配置表';
COMMENT ON COLUMN vat_admin_config.id IS 'ID';
COMMENT ON COLUMN vat_admin_config.group_id IS '配置组';
COMMENT ON COLUMN vat_admin_config.name IS '名称';
COMMENT ON COLUMN vat_admin_config.code IS '键';
COMMENT ON COLUMN vat_admin_config.value IS '值';
COMMENT ON COLUMN vat_admin_config.view IS '组件';
COMMENT ON COLUMN vat_admin_config.view_option_json IS '组件选项';
COMMENT ON COLUMN vat_admin_config.sortrank IS '排序';
COMMENT ON COLUMN vat_admin_config.open_front IS '公开前端';
COMMENT ON COLUMN vat_admin_config.status IS '状态';
COMMENT ON COLUMN vat_admin_config.createtime IS '创建时间';
COMMENT ON COLUMN vat_admin_config.updatetime IS '更新时间';

-- ----------------------------
-- Records of vat_admin_config
-- ----------------------------
BEGIN;
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (1, 1, '网站名称', 'app_name', 'VatAdmin文章中心', 'input', '', 0, 1, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (2, 1, '网站关键字', 'keyword', 'vat,Vat,Vue,VatAdmin', 'input', '', 0, 1, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (3, 1, '网站描述', 'description', 'Vat,VatAdmin,vue,vue3', 'textarea', '', 0, 1, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (4, 1, '版权信息', 'copyright', 'Copyright © 2025', 'input', '', 0, 1, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (5, 1, '备案号', 'record_num', '备案号 350106020111号', 'textarea', '', 0, 1, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (6, 1, '网站版本', 'app_version', 'v1.0.0', 'input', '', 0, 1, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (7, 2, '图片域名', 'cdn_url', 'http://127.0.0.1:8787', 'input', '', 0, 1, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (8, 2, '存储类型', 'storage_type', 'public', 'radio', '{"options":[{"label":"本地存储", "value":"public"},{"label":"阿里云OSS", "value": "oss"},{"label":"腾讯云COS", "value": "cos"},{"label":"七牛云", "value": "qiniu"}]}', 0, 0, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (9, 1, '百度统计', 'baidu_stat', '', 'textarea', '', 0, 1, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (10, 1, '公司地址', 'address', '', 'textarea', '', 0, 1, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (11, 1, '联系电话', 'phone', '', 'input', '', 0, 1, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (12, 1, '联系邮箱', 'email', '', 'input', '', 0, 1, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (13, 1, '营业时间', 'business_hours', '', 'input', '', 0, 1, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (14, 1, '底部标题', 'footer_title', '', 'input', '', 0, 1, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (15, 1, '底部描述', 'footer_description', '', 'textarea', '', 0, 1, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (16, 1, '官网LOGO', 'website_logo', '', 'upload', '{"props":{"list-type":"image-card","max":1}}', 0, 1, 0);
INSERT INTO vat_admin_config (id, group_id, name, code, value, view, view_option_json, sortrank, open_front, status) VALUES (17, 1, '后台Logo', 'logo', '', 'upload', '{"props":{"list-type":"image-card","max":1}}', 0, 1, 0);
COMMIT;

-- ----------------------------
-- Table structure for vat_admin_config_group
-- ----------------------------
DROP TABLE IF EXISTS vat_admin_config_group;
CREATE TABLE vat_admin_config_group (
  id SERIAL PRIMARY KEY,
  name varchar(64) NOT NULL,
  code varchar(64) NOT NULL,
  status integer NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uni_name UNIQUE (name)
);
COMMENT ON TABLE vat_admin_config_group IS '配置组';

BEGIN;
INSERT INTO vat_admin_config_group (id, name, code, status) VALUES (1, '站点配置', 'website', 0);
INSERT INTO vat_admin_config_group (id, name, code, status) VALUES (2, '上传配置', 'upload', 0);
COMMIT;

-- ----------------------------
-- Table structure for vat_admin_department
-- ----------------------------
DROP TABLE IF EXISTS vat_admin_department;
CREATE TABLE vat_admin_department (
  id SERIAL PRIMARY KEY,
  name varchar(64) NOT NULL DEFAULT '',
  parent_id integer NOT NULL DEFAULT 0,
  level varchar(255) NOT NULL DEFAULT '',
  sortrank integer NOT NULL DEFAULT 0,
  status integer NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uni_name UNIQUE (name)
);
COMMENT ON TABLE vat_admin_department IS '用户部门表';

-- ----------------------------
-- Table structure for vat_admin_dict
-- ----------------------------
DROP TABLE IF EXISTS vat_admin_dict;
CREATE TABLE vat_admin_dict (
  id SERIAL PRIMARY KEY,
  name varchar(64) NOT NULL,
  code varchar(64) NOT NULL,
  value text NOT NULL,
  status integer NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uni_code UNIQUE (code)
);
COMMENT ON TABLE vat_admin_dict IS '字典表';

BEGIN;
INSERT INTO vat_admin_dict (id, name, code, value, status) VALUES (1, '数据状态', 'status_desc', '[{"label":"正常","value":0},{"label":"禁用","value":1}]', 0);
INSERT INTO vat_admin_dict (id, name, code, value, status) VALUES (2, '菜单类型', 'menu_type_desc', '[{"label":"菜单","value":"menu"},{"label":"模块","value":"button"},{"label":"外链","value":"href"}]', 0);
INSERT INTO vat_admin_dict (id, name, code, value, status) VALUES (3, '是否', 'whether', '[{"label":"是","value":1},{"label":"否","value":0}]', 0);
INSERT INTO vat_admin_dict (id, name, code, value, status) VALUES (4, '渲染简易组件', 'display_view', '[{"label":"输入框","value":"input"},{"label":"文本域","value":"textarea"},{"label":"数字框","value":"input_number"},{"label":"单选框","value":"radio"},{"label":"多选框","value":"checkbox"},{"label":"下拉框","value":"select"},{"label":"键值框","value":"input_dynamic"},{"label":"图片上传","value":"upload"}]', 0);
INSERT INTO vat_admin_dict (id, name, code, value, status) VALUES (5, '消息通知类型', 'notice_type', '[{"label":"通知","value":0},{"label":"公告","value":1}]', 0);
INSERT INTO vat_admin_dict (id, name, code, value, status) VALUES (6, '消息已读', 'notice_read', '[{"label":"未读","value":0},{"label":"已读","value":1}]', 0);
INSERT INTO vat_admin_dict (id, name, code, value, status) VALUES (7, '定时任务类型', 'crontab_type', '[{"label":"Command","value":1},{"label":"Class","value":2},{"label":"Url","value":3},{"label":"Shell","value":4}]', 0);
INSERT INTO vat_admin_dict (id, name, code, value, status) VALUES (8, '数据状态1', 'status_desc1', '[{"label":"禁用","value":0},{"label":"正常","value":1}]', 0);
INSERT INTO vat_admin_dict (id, name, code, value, status) VALUES (9, '是否1', 'whether1', '[{"label":"是","value":0},{"label":"否","value":1}]', 0);
INSERT INTO vat_admin_dict (id, name, code, value, status) VALUES (10, '金额流水类型', 'type_desc', '[{"label":"增加","value":1},{"label":"减少","value":2}]', 0);
INSERT INTO vat_admin_dict (id, name, code, value, status) VALUES (11, '成功失败状态', 'success_status', '[{"label":"成功","value":1},{"label":"失败","value":0}]', 0);
COMMIT;

-- ----------------------------
-- Table structure for vat_admin_log_login
-- ----------------------------
DROP TABLE IF EXISTS vat_admin_log_login;
CREATE TABLE vat_admin_log_login (
  id SERIAL PRIMARY KEY,
  title varchar(64) NOT NULL DEFAULT '',
  memo varchar(640) NOT NULL DEFAULT '',
  ip varchar(32) NOT NULL DEFAULT '',
  ip_location varchar(200) NOT NULL DEFAULT '',
  browser varchar(32) NOT NULL DEFAULT '',
  system varchar(64) NOT NULL DEFAULT '',
  user_agent varchar(255) NOT NULL DEFAULT '',
  username varchar(64) NOT NULL,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP
);
COMMENT ON TABLE vat_admin_log_login IS '登录日志表';

-- ----------------------------
-- Table structure for vat_admin_log_operation
-- ----------------------------
DROP TABLE IF EXISTS vat_admin_log_operation;
CREATE TABLE vat_admin_log_operation (
  id SERIAL PRIMARY KEY,
  title varchar(64) NOT NULL DEFAULT '',
  route varchar(255) NOT NULL DEFAULT '',
  method varchar(16) NOT NULL DEFAULT '',
  params varchar(1280) NOT NULL DEFAULT '',
  ip varchar(32) NOT NULL DEFAULT '',
  ip_location varchar(200) NOT NULL DEFAULT '',
  browser varchar(32) NOT NULL DEFAULT '',
  system varchar(64) NOT NULL DEFAULT '',
  user_agent varchar(255) NOT NULL DEFAULT '',
  admin_id integer NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP
);
COMMENT ON TABLE vat_admin_log_operation IS '操作日志表';

-- ----------------------------
-- Table structure for vat_admin_menu
-- ----------------------------
DROP TABLE IF EXISTS vat_admin_menu;
CREATE TABLE vat_admin_menu (
  id SERIAL PRIMARY KEY,
  name varchar(64) NOT NULL DEFAULT '',
  path varchar(200) NOT NULL DEFAULT '',
  component varchar(200) NOT NULL DEFAULT '',
  icon varchar(32) NOT NULL DEFAULT '',
  parent_id integer NOT NULL DEFAULT 0,
  active varchar(200) NOT NULL DEFAULT '',
  hidden integer NOT NULL DEFAULT 0,
  hidden_breadcrumb integer NOT NULL DEFAULT 0,
  affix integer NOT NULL DEFAULT 0,
  type varchar(32) NOT NULL DEFAULT 'menu',
  fullpage integer NOT NULL DEFAULT 0,
  is_permission integer NOT NULL DEFAULT 1,
  permission_route varchar(640) NOT NULL DEFAULT '',
  redirect varchar(255) NOT NULL DEFAULT '',
  cached integer NOT NULL DEFAULT 0,
  sortrank integer NOT NULL DEFAULT 0,
  platform_id integer NOT NULL DEFAULT 0,
  status integer NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uni_path UNIQUE (path)
);
COMMENT ON TABLE vat_admin_menu IS '菜单表';
BEGIN;
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (1, '控制管理', '/control', '', 'home', 0, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (2, '首页', '/home', 'home/index', 'home', 1, '', 0, 0, 1, 'menu', 0, 1, '/app/vatadmin/basic/user/*\n/app/vatadmin/system/notice/listOwner\n/app/vatadmin/basic/tools/upload\n', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (3, '系统管理', '/system', '', 'set', 1, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (4, '用户管理', '/system/user', 'system/user/index', 'user', 3, '', 0, 0, 0, 'menu', 0, 1, '/app/vatadmin/system/role/tree\n/app/vatadmin/system/department/tree', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (15, '部门管理', '/system/department', 'system/department/index', 'department', 3, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (16, '列表', 'vat_admin_department_list', '', '', 15, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Department/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (17, '添加', 'vat_admin_department_add', '', '', 15, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Department/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (18, '编辑', 'vat_admin_department_edit', '', '', 15, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Department/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (19, '锁定', 'vat_admin_department_lock', '', '', 15, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Department/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (20, '解锁', 'vat_admin_department_unlock', '', '', 15, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Department/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (21, '删除', 'vat_admin_department_delete', '', '', 15, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Department/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (22, '导入', 'vat_admin_department_import', '', '', 15, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Department/import', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (23, '下载', 'vat_admin_department_download', '', '', 15, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Department/download', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (24, '批量操作', 'vat_admin_department_batch', '', '', 15, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Department/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (25, '角色管理', '/system/role', 'system/role/index', 'scale', 3, '', 0, 0, 0, 'menu', 0, 1, '/app/vatadmin/system/Menu/roleMenus\n/app/vatadmin/system/Role/roleMenuSubmit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (26, '列表', 'vat_admin_role_list', '', '', 25, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Role/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (27, '添加', 'vat_admin_role_add', '', '', 25, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Role/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (28, '编辑', 'vat_admin_role_edit', '', '', 25, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Role/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (29, '锁定', 'vat_admin_role_lock', '', '', 25, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Role/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (30, '解锁', 'vat_admin_role_unlock', '', '', 25, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Role/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (31, '删除', 'vat_admin_role_delete', '', '', 25, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Role/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (32, '导入', 'vat_admin_role_import', '', '', 25, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Role/import', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (33, '下载', 'vat_admin_role_download', '', '', 25, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Role/download', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (34, '批量操作', 'vat_admin_role_batch', '', '', 25, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Role/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (37, 'CURD管理', '/vat/curd', '', 'page', 0, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (38, '数据表', '/vat/table', 'vatpage/table', 'coin', 37, '', 0, 0, 0, 'menu', 0, 1, '/app/vatadmin/basic/Table*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (39, 'CURD页面', '/vat/page', 'vatpage/index', 'page1', 37, '', 0, 0, 0, 'menu', 0, 1, '/app/vatadmin/basic/Pages*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (40, '列表', 'vat_admin_user_list', '', '', 4, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/User/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (41, '添加', 'vat_admin_user_add', '', '', 4, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/User/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (42, '编辑', 'vat_admin_user_edit', '', '', 4, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/User/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (43, '锁定', 'vat_admin_user_lock', '', '', 4, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/User/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (44, '解锁', 'vat_admin_user_unlock', '', '', 4, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/User/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (45, '删除', 'vat_admin_user_delete', '', '', 4, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/User/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (46, '导入', 'vat_admin_user_import', '', '', 4, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/User/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (47, '下载', 'vat_admin_user_download', '', '', 4, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/User/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (48, '批量操作', 'vat_admin_user_batch', '', '', 4, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/User/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (49, '字典管理', '/system/dict', 'system/dict/index', 'dict', 3, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (50, '列表', 'vat_admin_dict_list', '', '', 49, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Dict/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (51, '添加', 'vat_admin_dict_add', '', '', 49, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Dict/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (52, '编辑', 'vat_admin_dict_edit', '', '', 49, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Dict/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (53, '锁定', 'vat_admin_dict_lock', '', '', 49, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Dict/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (54, '解锁', 'vat_admin_dict_unlock', '', '', 49, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Dict/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (55, '删除', 'vat_admin_dict_delete', '', '', 49, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Dict/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (56, '导入', 'vat_admin_dict_import', '', '', 49, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Dict/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (57, '下载', 'vat_admin_dict_download', '', '', 49, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Dict/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (58, '批量操作', 'vat_admin_dict_batch', '', '', 49, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Dict/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (59, '菜单管理', '/system/menu', 'system/menu/index', 'menu', 3, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (60, '列表', 'vat_admin_menu_list', '', '', 59, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Menu/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (61, '添加', 'vat_admin_menu_add', '', '', 59, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Menu/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (62, '编辑', 'vat_admin_menu_edit', '', '', 59, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Menu/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (63, '锁定', 'vat_admin_menu_lock', '', '', 59, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Menu/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (64, '解锁', 'vat_admin_menu_unlock', '', '', 59, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Menu/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (65, '删除', 'vat_admin_menu_delete', '', '', 59, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Menu/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (66, '导入', 'vat_admin_menu_import', '', '', 59, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Menu/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (67, '下载', 'vat_admin_menu_download', '', '', 59, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Menu/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (68, '批量操作', 'vat_admin_menu_batch', '', '', 59, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Menu/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (69, '配置管理', '/system/config', 'system/config/set', 'list', 3, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (70, '列表', 'vat_admin_config_list', '', '', 69, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Config/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (71, '添加', 'vat_admin_config_add', '', '', 69, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Config/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (72, '编辑', 'vat_admin_config_edit', '', '', 69, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Config/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (73, '锁定', 'vat_admin_config_lock', '', '', 69, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Config/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (74, '解锁', 'vat_admin_config_unlock', '', '', 69, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Config/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (75, '删除', 'vat_admin_config_delete', '', '', 69, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Config/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (76, '导入', 'vat_admin_config_import', '', '', 69, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Config/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (77, '下载', 'vat_admin_config_download', '', '', 69, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Config/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (78, '批量操作', 'vat_admin_config_batch', '', '', 69, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Config/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (79, '监控管理', '/monitor', '', 'monitor', 1, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (80, '登录日志', '/system/logLogin', 'system/logLogin/index', 'list', 79, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (81, '列表', 'vat_admin_log_login_list', '', '', 80, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogLogin/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (82, '添加', 'vat_admin_log_login_add', '', '', 80, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogLogin/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (83, '编辑', 'vat_admin_log_login_edit', '', '', 80, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogLogin/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (84, '锁定', 'vat_admin_log_login_lock', '', '', 80, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogLogin/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (85, '解锁', 'vat_admin_log_login_unlock', '', '', 80, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogLogin/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (86, '删除', 'vat_admin_log_login_delete', '', '', 80, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogLogin/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (87, '导入', 'vat_admin_log_login_import', '', '', 80, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogLogin/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (88, '下载', 'vat_admin_log_login_download', '', '', 80, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogLogin/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (89, '批量操作', 'vat_admin_log_login_batch', '', '', 80, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogLogin/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (90, '操作日志', '/logOperation', 'system/logOperation/index', 'list', 79, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (91, '列表', 'vat_admin_log_operation_list', '', '', 90, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogOperation/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (92, '添加', 'vat_admin_log_operation_add', '', '', 90, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogOperation/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (93, '编辑', 'vat_admin_log_operation_edit', '', '', 90, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogOperation/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (94, '锁定', 'vat_admin_log_operation_lock', '', '', 90, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogOperation/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (95, '解锁', 'vat_admin_log_operation_unlock', '', '', 90, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogOperation/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (96, '删除', 'vat_admin_log_operation_delete', '', '', 90, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogOperation/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (97, '导入', 'vat_admin_log_operation_import', '', '', 90, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogOperation/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (98, '下载', 'vat_admin_log_operation_download', '', '', 90, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogOperation/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (99, '批量操作', 'vat_admin_log_operation_batch', '', '', 90, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/LogOperation/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (100, '消息管理', '/notice', '', 'notice', 1, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (101, '消息通知', '/system/notice', 'system/notice/index', 'list', 100, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (102, '列表', 'vat_admin_notice_list', '', '', 101, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Notice/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (103, '添加', 'vat_admin_notice_add', '', '', 101, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Notice/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (104, '编辑', 'vat_admin_notice_edit', '', '', 101, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Notice/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (105, '锁定', 'vat_admin_notice_lock', '', '', 101, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Notice/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (106, '解锁', 'vat_admin_notice_unlock', '', '', 101, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Notice/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (107, '删除', 'vat_admin_notice_delete', '', '', 101, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Notice/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (108, '导入', 'vat_admin_notice_import', '', '', 101, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Notice/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (109, '下载', 'vat_admin_notice_download', '', '', 101, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Notice/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (110, '批量操作', 'vat_admin_notice_batch', '', '', 101, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/system/Notice/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (111, '工具管理', '/tools', '', 'set', 1, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (112, '定时任务', '/system/crontab', 'system/crontab/index', 'list', 111, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (113, '列表', 'vat_admin_crontab_list', '', '', 112, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/Crontab/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (114, '添加', 'vat_admin_crontab_add', '', '', 112, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/Crontab/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (115, '编辑', 'vat_admin_crontab_edit', '', '', 112, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/Crontab/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (116, '锁定', 'vat_admin_crontab_lock', '', '', 112, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/Crontab/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (117, '解锁', 'vat_admin_crontab_unlock', '', '', 112, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/Crontab/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (118, '删除', 'vat_admin_crontab_delete', '', '', 112, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/Crontab/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (119, '导入', 'vat_admin_crontab_import', '', '', 112, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/Crontab/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (120, '下载', 'vat_admin_crontab_download', '', '', 112, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/Crontab/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (121, '批量操作', 'vat_admin_crontab_batch', '', '', 112, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/Crontab/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (122, '资源日志', '/system/uploadLog', 'system/uploadLog/index', 'list', 79, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (123, '列表', 'vat_upload_log_list', '', '', 122, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/UploadLog/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (124, '添加', 'vat_upload_log_add', '', '', 122, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/UploadLog/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (125, '编辑', 'vat_upload_log_edit', '', '', 122, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/UploadLog/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (126, '锁定', 'vat_upload_log_lock', '', '', 122, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/UploadLog/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (127, '解锁', 'vat_upload_log_unlock', '', '', 122, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/UploadLog/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (128, '删除', 'vat_upload_log_delete', '', '', 122, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/UploadLog/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (129, '导入', 'vat_upload_log_import', '', '', 122, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/UploadLog/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (130, '下载', 'vat_upload_log_download', '', '', 122, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/UploadLog/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (131, '批量操作', 'vat_upload_log_batch', '', '', 122, '', 1, 0, 0, 'button', 0, 1, '/app/vatadmin/basic/UploadLog/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (133, '会员列表', '/member/Member', 'member/Member/index', 'list', 143, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 100, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (134, '列表', 'vat_member_list', '', '', 133, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/Member/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (135, '添加', 'vat_member_add', '', '', 133, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/Member/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (136, '编辑', 'vat_member_edit', '', '', 133, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/Member/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (137, '锁定', 'vat_member_lock', '', '', 133, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/Member/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (138, '解锁', 'vat_member_unlock', '', '', 133, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/Member/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (139, '删除', 'vat_member_delete', '', '', 133, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/Member/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (140, '导入', 'vat_member_import', '', '', 133, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/Member/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (141, '下载', 'vat_member_download', '', '', 133, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/Member/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (142, '批量操作', 'vat_member_batch', '', '', 133, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/Member/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (143, '会员管理', '/member', '', 'user1', 0, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (144, '会员等级', '/member/MemberLevel', 'member/MemberLevel/index', 'list', 143, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (145, '列表', 'vat_member_level_list', '', '', 144, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLevel/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (146, '添加', 'vat_member_level_add', '', '', 144, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLevel/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (147, '编辑', 'vat_member_level_edit', '', '', 144, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLevel/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (148, '锁定', 'vat_member_level_lock', '', '', 144, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLevel/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (149, '解锁', 'vat_member_level_unlock', '', '', 144, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLevel/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (150, '删除', 'vat_member_level_delete', '', '', 144, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLevel/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (151, '导入', 'vat_member_level_import', '', '', 144, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLevel/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (152, '下载', 'vat_member_level_download', '', '', 144, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLevel/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (153, '批量操作', 'vat_member_level_batch', '', '', 144, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLevel/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (154, '积分记录', '/member/MemberPoints', 'member/MemberPoints/index', 'list', 143, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (155, '列表', 'vat_member_points_list', '', '', 154, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberPoints/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (156, '添加', 'vat_member_points_add', '', '', 154, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberPoints/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (157, '编辑', 'vat_member_points_edit', '', '', 154, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberPoints/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (158, '锁定', 'vat_member_points_lock', '', '', 154, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberPoints/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (159, '解锁', 'vat_member_points_unlock', '', '', 154, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberPoints/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (160, '删除', 'vat_member_points_delete', '', '', 154, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberPoints/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (161, '导入', 'vat_member_points_import', '', '', 154, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberPoints/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (162, '下载', 'vat_member_points_download', '', '', 154, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberPoints/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (163, '批量操作', 'vat_member_points_batch', '', '', 154, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberPoints/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (164, '消息通知', '/member/MemberNotice', 'member/MemberNotice/index', 'list', 143, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (165, '列表', 'vat_member_notice_list', '', '', 164, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberNotice/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (166, '添加', 'vat_member_notice_add', '', '', 164, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberNotice/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (167, '编辑', 'vat_member_notice_edit', '', '', 164, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberNotice/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (168, '锁定', 'vat_member_notice_lock', '', '', 164, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberNotice/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (169, '解锁', 'vat_member_notice_unlock', '', '', 164, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberNotice/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (170, '删除', 'vat_member_notice_delete', '', '', 164, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberNotice/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (171, '导入', 'vat_member_notice_import', '', '', 164, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberNotice/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (172, '下载', 'vat_member_notice_download', '', '', 164, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberNotice/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (173, '批量操作', 'vat_member_notice_batch', '', '', 164, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberNotice/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (174, '登录日志', '/member/MemberLoginLog', 'member/MemberLoginLog/index', 'list', 143, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (175, '列表', 'vat_member_login_log_list', '', '', 174, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLoginLog/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (176, '添加', 'vat_member_login_log_add', '', '', 174, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLoginLog/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (177, '编辑', 'vat_member_login_log_edit', '', '', 174, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLoginLog/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (178, '锁定', 'vat_member_login_log_lock', '', '', 174, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLoginLog/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (179, '解锁', 'vat_member_login_log_unlock', '', '', 174, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLoginLog/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (180, '删除', 'vat_member_login_log_delete', '', '', 174, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLoginLog/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (181, '导入', 'vat_member_login_log_import', '', '', 174, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLoginLog/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (182, '下载', 'vat_member_login_log_download', '', '', 174, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLoginLog/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (183, '批量操作', 'vat_member_login_log_batch', '', '', 174, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberLoginLog/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (184, '行为日志', '/member/MemberActionLog', 'member/MemberActionLog/index', 'list', 143, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (185, '列表', 'vat_member_action_log_list', '', '', 184, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberActionLog/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (186, '添加', 'vat_member_action_log_add', '', '', 184, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberActionLog/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (187, '编辑', 'vat_member_action_log_edit', '', '', 184, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberActionLog/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (188, '锁定', 'vat_member_action_log_lock', '', '', 184, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberActionLog/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (189, '解锁', 'vat_member_action_log_unlock', '', '', 184, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberActionLog/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (190, '删除', 'vat_member_action_log_delete', '', '', 184, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberActionLog/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (191, '导入', 'vat_member_action_log_import', '', '', 184, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberActionLog/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (192, '下载', 'vat_member_action_log_download', '', '', 184, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberActionLog/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (193, '批量操作', 'vat_member_action_log_batch', '', '', 184, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberActionLog/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (194, '会员分组', '/member/MemberGroup', 'member/MemberGroup/index', 'list', 143, '', 0, 0, 0, 'menu', 0, 1, '', '', 0, 99, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (195, '列表', 'vat_member_group_list', '', '', 194, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberGroup/list', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (196, '添加', 'vat_member_group_add', '', '', 194, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberGroup/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (197, '编辑', 'vat_member_group_edit', '', '', 194, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberGroup/edit', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (198, '锁定', 'vat_member_group_lock', '', '', 194, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberGroup/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (199, '解锁', 'vat_member_group_unlock', '', '', 194, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberGroup/lock', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (200, '删除', 'vat_member_group_delete', '', '', 194, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberGroup/delete', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (201, '导入', 'vat_member_group_import', '', '', 194, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberGroup/import*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (202, '下载', 'vat_member_group_download', '', '', 194, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberGroup/download*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (203, '批量操作', 'vat_member_group_batch', '', '', 194, '', 1, 0, 0, 'button', 0, 1, '/vatadmin/member/MemberGroup/batch*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id, name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (204, '插件市场', '/plugin', 'plugin/index', 'plugins', 111, '', 0, 0, 0, 'menu', 0, 1, '/app/vatadmin/basic/plugin*', '', 0, 0, 0, 0);
INSERT INTO vat_admin_menu (id,name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (205,'配置组管理','vat_admin_config_group','','',69,'',0,0,0,'button',0,1,'/app/vatadmin/system/ConfigGroup/*','',0,0,0,0);
INSERT INTO vat_admin_menu (id,name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (206,'配置设置功能','vat_admin_config_setting','','',69,'',0,0,0,'button',0,1,'/app/vatadmin/system/Config/editMore','',0,0,0,0);

INSERT INTO vat_admin_menu (id,name, path, component, icon, parent_id, active, hidden, hidden_breadcrumb, affix, type, fullpage, is_permission, permission_route, redirect, cached, sortrank, platform_id, status) VALUES (207,'重置密码','vat_admin_user_reset_password','','',4,'',0,0,0,'button',0,1,'/app/vatadmin/system/User/resetPassword','',0,0,0,0);

COMMIT;

-- ----------------------------
-- Table structure for vat_admin_notice
-- ----------------------------
DROP TABLE IF EXISTS vat_admin_notice;
CREATE TABLE vat_admin_notice (
  id SERIAL PRIMARY KEY,
  admin_id integer NOT NULL DEFAULT 0,
  type integer NOT NULL DEFAULT 0,
  title varchar(255) NOT NULL DEFAULT '',
  content text,
  is_read smallint NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_notice_admin_id ON vat_admin_notice (admin_id);
COMMENT ON TABLE vat_admin_notice IS '消息通知表';

-- ----------------------------
-- Table structure for vat_admin_role
-- ----------------------------
DROP TABLE IF EXISTS vat_admin_role;
CREATE TABLE vat_admin_role (
  id SERIAL PRIMARY KEY,
  name varchar(64) NOT NULL DEFAULT '',
  code varchar(255) NOT NULL DEFAULT '',
  data_type integer NOT NULL DEFAULT 0,
  parent_id integer NOT NULL DEFAULT 0,
  sortrank integer NOT NULL DEFAULT 0,
  status integer NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uni_name UNIQUE (name)
);
COMMENT ON TABLE vat_admin_role IS '用户角色表';

BEGIN;
INSERT INTO vat_admin_role (id, name, code, data_type, parent_id, sortrank, status) VALUES (1, '超级管理员', 'superAdmin', 0, 0, 0, 0);
COMMIT;

-- ----------------------------
-- Table structure for vat_admin_role_menu
-- ----------------------------
DROP TABLE IF EXISTS vat_admin_role_menu;
CREATE TABLE vat_admin_role_menu (
  id SERIAL PRIMARY KEY,
  menu_id integer NOT NULL DEFAULT 0,
  role_id integer NOT NULL DEFAULT 0,
  status integer NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uni_menu_role UNIQUE (menu_id, role_id)
);
COMMENT ON TABLE vat_admin_role_menu IS '角色菜单表';

-- ----------------------------
-- Table structure for vat_admin_user
-- ----------------------------
DROP TABLE IF EXISTS vat_admin_user;
CREATE TABLE vat_admin_user (
  id SERIAL PRIMARY KEY,
  department_id varchar(200) NOT NULL DEFAULT '',
  name varchar(64) NOT NULL DEFAULT '',
  username varchar(64) NOT NULL DEFAULT '',
  mobile varchar(16) NOT NULL DEFAULT '',
  email varchar(64) NOT NULL DEFAULT '',
  password varchar(64) NOT NULL DEFAULT '',
  avatar varchar(255) NOT NULL DEFAULT '',
  roles varchar(200) NOT NULL DEFAULT '',
  online_status integer NOT NULL DEFAULT 0,
  last_login_time timestamp DEFAULT NULL,
  status integer NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uni_username UNIQUE (username)
);
COMMENT ON TABLE vat_admin_user IS '用户表';

BEGIN;
INSERT INTO vat_admin_user (id, department_id, name, username, mobile, email, password, avatar, roles, online_status, last_login_time, status) VALUES (1, '0', '系统管理员', 'admin', '', '', '$2y$10$SRlQ61ey7D2pBDc1VGg.RubKqX15qflhlMh7.FbAzDPi2vFyt9jOG', '', '1', 0, NULL, 0);
COMMIT;

-- ----------------------------
-- Table structure for vat_pages
-- ----------------------------
DROP TABLE IF EXISTS vat_pages;
CREATE TABLE vat_pages (
  id SERIAL PRIMARY KEY,
  "table" varchar(64) NOT NULL DEFAULT '',
  table_code integer NOT NULL DEFAULT 0,
  name varchar(64) NOT NULL DEFAULT '',
  build_module smallint NOT NULL DEFAULT 0,
  build_project varchar(64) NOT NULL DEFAULT '',
  build_app_name varchar(32) NOT NULL DEFAULT '',
  build_controller varchar(64) NOT NULL DEFAULT '',
  build_model varchar(64) NOT NULL DEFAULT '',
  build_menu integer NOT NULL DEFAULT 0,
  build_menu_name varchar(64) NOT NULL DEFAULT '',
  build_view varchar(128) NOT NULL DEFAULT '',
  tpl_json text,
  tpl_system_json text,
  status integer NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP
);
COMMENT ON TABLE vat_pages IS '页面表';

-- ----------------------------
-- Table structure for vat_upload_log
-- ----------------------------
DROP TABLE IF EXISTS vat_upload_log;
CREATE TABLE vat_upload_log (
  id SERIAL PRIMARY KEY,
  admin_id integer NOT NULL DEFAULT 0,
  params varchar(200) NOT NULL DEFAULT '',
  event_key varchar(200) NOT NULL DEFAULT '',
  content varchar(1280) NOT NULL DEFAULT '',
  event_result varchar(320) NOT NULL DEFAULT '',
  createime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP
);
COMMENT ON TABLE vat_upload_log IS '导入日志表';

-- ----------------------------
-- Table structure for vat_crontab
-- ----------------------------
DROP TABLE IF EXISTS vat_crontab;
CREATE TABLE vat_crontab (
  id SERIAL PRIMARY KEY,
  name varchar(100) NOT NULL,
  description text,
  task_type smallint DEFAULT 1,
  cron_expression varchar(50) NOT NULL,
  command text NOT NULL,
  timeout integer DEFAULT 300,
  lock_time integer DEFAULT 0,
  last_run_time timestamp DEFAULT NULL,
  next_run_time timestamp DEFAULT NULL,
  status smallint DEFAULT 0,
  createtime timestamp DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp DEFAULT CURRENT_TIMESTAMP
);
COMMENT ON TABLE vat_crontab IS '定时任务表';

-- ----------------------------
-- Table structure for vat_crontab_log
-- ----------------------------
DROP TABLE IF EXISTS vat_crontab_log;
CREATE TABLE vat_crontab_log (
  id SERIAL PRIMARY KEY,
  cron_id integer NOT NULL,
  task_name varchar(100) NOT NULL,
  run_status varchar(16) NOT NULL DEFAULT 'running',
  output text,
  error text,
  start_time timestamp DEFAULT CURRENT_TIMESTAMP,
  end_time timestamp DEFAULT NULL,
  duration integer DEFAULT NULL,
  pid integer DEFAULT NULL
);
CREATE INDEX idx_cron_log_cron_id ON vat_crontab_log (cron_id);
CREATE INDEX idx_cron_log_start_time ON vat_crontab_log (start_time);
COMMENT ON TABLE vat_crontab_log IS '定时任务日志表';

-- ----------------------------
-- Table structure for vat_member
-- ----------------------------
DROP TABLE IF EXISTS vat_member;
CREATE TABLE vat_member (
  id SERIAL PRIMARY KEY,
  username varchar(64) NOT NULL DEFAULT '',
  password varchar(128) NOT NULL,
  nickname varchar(64) NOT NULL DEFAULT '',
  avatar varchar(255) NOT NULL DEFAULT '',
  email varchar(128) NOT NULL DEFAULT '',
  mobile varchar(20) NOT NULL DEFAULT '',
  level_id integer NOT NULL DEFAULT 0,
  group_id integer NOT NULL DEFAULT 0,
  points decimal(10,2) NOT NULL DEFAULT 0.00,
  last_login_time timestamp NULL,
  status smallint NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uni_member_username UNIQUE (username)
);
COMMENT ON TABLE vat_member IS '会员表';

-- ----------------------------
-- Table structure for vat_member_action_log
-- ----------------------------
DROP TABLE IF EXISTS vat_member_action_log;
CREATE TABLE vat_member_action_log (
  id SERIAL PRIMARY KEY,
  member_id integer NOT NULL DEFAULT 0,
  username varchar(64) NOT NULL,
  title varchar(50) NOT NULL DEFAULT '',
  route varchar(255) NOT NULL DEFAULT '',
  method varchar(64) NOT NULL DEFAULT '',
  params text NOT NULL,
  ip varchar(32) NOT NULL DEFAULT '',
  ip_location varchar(200) NOT NULL DEFAULT '',
  browser varchar(32) NOT NULL DEFAULT '',
  system varchar(64) NOT NULL DEFAULT '',
  user_agent varchar(255) NOT NULL DEFAULT '',
  status smallint NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_action_log_createtime ON vat_member_action_log (createtime);
CREATE INDEX idx_action_log_member_id ON vat_member_action_log (member_id);
CREATE INDEX idx_action_log_username ON vat_member_action_log (username);
COMMENT ON TABLE vat_member_action_log IS '会员行为日志表';

-- ----------------------------
-- Table structure for vat_member_group
-- ----------------------------
DROP TABLE IF EXISTS vat_member_group;
CREATE TABLE vat_member_group (
  id SERIAL PRIMARY KEY,
  name varchar(64) NOT NULL DEFAULT '',
  memo varchar(200) NOT NULL DEFAULT '',
  status smallint NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uni_member_group_name UNIQUE (name)
);
COMMENT ON TABLE vat_member_group IS '会员组表';

-- ----------------------------
-- Table structure for vat_member_level
-- ----------------------------
DROP TABLE IF EXISTS vat_member_level;
CREATE TABLE vat_member_level (
  id SERIAL PRIMARY KEY,
  name varchar(64) NOT NULL DEFAULT '',
  level integer NOT NULL DEFAULT 0,
  min_points integer NOT NULL DEFAULT 0,
  max_points integer NOT NULL DEFAULT 0,
  privileges text,
  sort integer NOT NULL DEFAULT 0,
  status smallint NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uni_member_level_name UNIQUE (name)
);
COMMENT ON TABLE vat_member_level IS '会员等级表';

-- ----------------------------
-- Table structure for vat_member_login_log
-- ----------------------------
DROP TABLE IF EXISTS vat_member_login_log;
CREATE TABLE vat_member_login_log (
  id SERIAL PRIMARY KEY,
  member_id integer NOT NULL DEFAULT 0,
  username varchar(64) NOT NULL,
  ip varchar(32) NOT NULL DEFAULT '',
  ip_location varchar(200) NOT NULL DEFAULT '',
  browser varchar(32) NOT NULL DEFAULT '',
  system varchar(64) NOT NULL DEFAULT '',
  user_agent varchar(255) NOT NULL DEFAULT '',
  login_type varchar(20) NOT NULL DEFAULT '',
  login_status smallint NOT NULL DEFAULT 1,
  login_result varchar(255) NOT NULL DEFAULT '',
  status smallint NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_member_login_log_time ON vat_member_login_log (createtime);
CREATE INDEX idx_member_login_log_member_id ON vat_member_login_log (member_id);
CREATE INDEX idx_member_login_log_username ON vat_member_login_log (username);
COMMENT ON TABLE vat_member_login_log IS '会员登录日志表';

-- ----------------------------
-- Table structure for vat_member_points
-- ----------------------------
DROP TABLE IF EXISTS vat_member_points;
CREATE TABLE vat_member_points (
  id SERIAL PRIMARY KEY,
  member_id integer NOT NULL DEFAULT 0,
  points decimal(10,2) NOT NULL DEFAULT 0.00,
  balance decimal(10,2) NOT NULL DEFAULT 0.00,
  reason varchar(255) NOT NULL DEFAULT '',
  type smallint NOT NULL DEFAULT 1,
  status smallint NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_member_points_member_id ON vat_member_points (member_id);
COMMENT ON TABLE vat_member_points IS '会员积分记录表';

-- ----------------------------
-- Table structure for vat_member_notice
-- ----------------------------
DROP TABLE IF EXISTS vat_member_notice;
CREATE TABLE vat_member_notice (
  id SERIAL PRIMARY KEY,
  member_id integer NOT NULL DEFAULT 0,
  type integer NOT NULL DEFAULT 0,
  title varchar(255) NOT NULL DEFAULT '',
  content text,
  is_read smallint NOT NULL DEFAULT 0,
  createtime timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_member_notice_member_id ON vat_member_notice (member_id);
COMMENT ON TABLE vat_member_notice IS '会员消息通知表';

-- ----------------------------
-- Table structure for vat_task_async
-- ----------------------------
DROP TABLE IF EXISTS vat_task_async;
CREATE TABLE vat_task_async (
  id SERIAL PRIMARY KEY,
  title varchar(200) NOT NULL,
  namespace varchar(255) NOT NULL DEFAULT '',
  exec_class varchar(255) NOT NULL DEFAULT '',
  params text,
  content text,
  start_time integer NOT NULL DEFAULT 0,
  end_time integer NOT NULL DEFAULT 0,
  admin_id integer NOT NULL DEFAULT 0,
  status smallint NOT NULL DEFAULT 0,
  createtime timestamp DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_task_async_admin_id ON vat_task_async (admin_id);
COMMENT ON TABLE vat_task_async IS '异步任务表';
