<?php
namespace Common\ORG\Util;

/**
 * 权限系统表配置
 * @author Evan <tangzwgo@foxmail.com>
 * @since 2016年7月20日
 */
class AuthTable {
	const TB_APP = 'tb_app'; //应用表
    const TB_APP_VERSION = 'tb_app_version'; //应用版本表
	const TB_AUTH_FUNCTION = 'tb_auth_function'; //功能表
	const TB_AUTH_FUNCTION_GROUP = 'tb_auth_function_group'; //功能组表
	const TB_AUTH_USER = 'tb_auth_user'; //系统用户表
	const TB_AUTH_USER_GROUP = 'tb_auth_user_group'; //用户组表
	const TB_AUTH_USER_USERGROUP = 'tb_auth_user_usergroup'; //用户用户组关联表
	const TB_AUTH_USERGROUP_FUNCTION = 'tb_auth_usergroup_function'; //用户组功能表
	const TB_AUTH_USER_FUNCTION = 'tb_auth_user_function'; //用户功能表
	const TB_AUTH_USER_APP = 'tb_auth_user_app'; //用户应用表
	const TB_AUTH_DEPARTMENT = 'tb_auth_department'; //部门表
	const TB_AUTH_POSITION = 'tb_auth_position'; //岗位表
	const TB_AUTH_MENU = 'tb_auth_menu'; //菜单表

	const TB_LOG_AUTH_LOGIN = 'tb_log_auth_login'; //登录日志表
	const TB_LOG_AUTH_ACCESS = 'tb_log_auth_access'; //访问日志表

	const TB_APP_SQL = "
CREATE TABLE `".self::TB_APP."` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
	`app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '应用id',
	`name` varchar(64) NOT NULL DEFAULT '' COMMENT '应用名称',
	`app_secret` varchar(64) NOT NULL DEFAULT '' COMMENT '应用秘钥',
	`token` varchar(64) NOT NULL DEFAULT '' COMMENT '令牌',
	`encoding_AESKey` varchar(64) NOT NULL DEFAULT '' COMMENT '消息加解密密钥（43位）',
	`is_encryption` tinyint(3) NOT NULL DEFAULT '1' COMMENT '是否需要加密1是0否',
	`status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '应用状态1正常-1删除',
	`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
	`ip_list` varchar(512) NOT NULL DEFAULT '' COMMENT 'ip白名单，多个用逗号分隔',
	`type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '应用类型1内部系统 2APP 3WEB站点 5第三方应用',
	`is_auth` tinyint(3) NOT NULL DEFAULT '1' COMMENT '是否需要设置权限1是0否',
	`domain` varchar(32) NOT NULL DEFAULT '' COMMENT '域名',
	PRIMARY KEY (`id`),
	UNIQUE KEY `app_id` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应用表'";

	const TB_APP_VERSION_SQL = "
CREATE TABLE `".self::TB_APP_VERSION."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
  `app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '应用id',
  `version` varchar(32) NOT NULL DEFAULT '' COMMENT '版本号',
  `package` varchar(128) NOT NULL DEFAULT '' COMMENT '安装包路径',
  `is_update` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否强制更新1是0否',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态1正常-1删除',
  `update_desc` varchar(2048) NOT NULL DEFAULT '' COMMENT '更新描述',
  `publish_time` int(11) NOT NULL DEFAULT '0' COMMENT '发布时间',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应用版本表'";

	const TB_AUTH_FUNCTION_SQL = "
CREATE TABLE `".self::TB_AUTH_FUNCTION."` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '功能id',
	`path` varchar(128) NOT NULL DEFAULT '' COMMENT '路径',
	`name` varchar(256) NOT NULL DEFAULT '' COMMENT '名称',
	`group_id` int(11) NOT NULL DEFAULT '0' COMMENT '功能组ID',
	`app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '应用id',
	`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
	PRIMARY KEY (`id`),
	UNIQUE KEY `path` (`path`,`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='功能表'";

	const TB_AUTH_FUNCTION_GROUP_SQL = "
CREATE TABLE `".self::TB_AUTH_FUNCTION_GROUP."` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '功能组id',
	`name` varchar(64) NOT NULL DEFAULT '' COMMENT '功能组名',
	`app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '应用id',
	`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='功能组表'";

	const TB_AUTH_USER_SQL = "
CREATE TABLE `".self::TB_AUTH_USER."` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
	`username` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名',
	`password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
	`name` varchar(32) NOT NULL DEFAULT '' COMMENT '姓名',
	`nickname` varchar(32) NOT NULL COMMENT '昵称',
	`sex` tinyint(3) NOT NULL DEFAULT '1' COMMENT '性别1男2女',
	`department_id` int(11) NOT NULL DEFAULT '0' COMMENT '部门id',
	`position_id` int(11) NOT NULL DEFAULT '0' COMMENT '岗位id',
	`work_number` varchar(16) NOT NULL DEFAULT '' COMMENT '工号',
	`img` varchar(128) NOT NULL DEFAULT '' COMMENT '照片',
	`desc` varchar(1024) NOT NULL DEFAULT '' COMMENT '员工介绍',
	`status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态1正常-1删除',
	`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
	`wechat` varchar(32) NOT NULL DEFAULT '' COMMENT '微信号',
	`qq` varchar(16) NOT NULL DEFAULT '' COMMENT 'QQ号',
	`email` varchar(64) NOT NULL DEFAULT '' COMMENT '邮箱',
	`mobile` varchar(12) NOT NULL DEFAULT '' COMMENT '手机',
	`school` varchar(128) NOT NULL DEFAULT '' COMMENT '毕业学校',
	`address` varchar(128) NOT NULL DEFAULT '' COMMENT '地址',
	`skill` varchar(256) NOT NULL DEFAULT '' COMMENT '技能',
	PRIMARY KEY (`id`),
	UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表'";

	const TB_AUTH_USER_GROUP_SQL = "
CREATE TABLE `".self::TB_AUTH_USER_GROUP."` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户组id',
	`name` varchar(64) NOT NULL DEFAULT '' COMMENT '用户组名',
	`app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '应用id',
	`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户组表'";

	const TB_AUTH_USER_USERGROUP_SQL = "
CREATE TABLE `".self::TB_AUTH_USER_USERGROUP."` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
	`user_group_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户组id',
	`user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
	`app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '应用id',
	`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
	PRIMARY KEY (`id`),
	UNIQUE KEY `ugid_uid` (`user_group_id`,`user_id`),
	KEY `user_id` (`user_id`),
	KEY `user_group_id` (`user_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户-用户组关联表'";

	const TB_AUTH_USERGROUP_FUNCTION_SQL = "
CREATE TABLE `".self::TB_AUTH_USERGROUP_FUNCTION."` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
	`user_group_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户组ID',
	`function_id` int(11) NOT NULL DEFAULT '0' COMMENT '功能ID',
	`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
	PRIMARY KEY (`id`),
	UNIQUE KEY `ugid_funcid` (`user_group_id`,`function_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户组-功能关联表'";

	const TB_AUTH_USER_FUNCTION_SQL = "
CREATE TABLE `".self::TB_AUTH_USER_FUNCTION."` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
	`user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
	`function_ids` varchar(2048) NOT NULL DEFAULT '' COMMENT '功能ID串',
	`app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '应用id',
	`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户-功能关联表'";

	const TB_AUTH_USER_APP_SQL = "
CREATE TABLE `".self::TB_AUTH_USER_APP."` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
	`uid` int(11) NOT NULL DEFAULT '0' COMMENT 'id',
	`app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '应用id',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户-应用关联表'";

	const TB_AUTH_DEPARTMENT_SQL = "
CREATE TABLE `".self::TB_AUTH_DEPARTMENT."` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '部门id',
	`name` varchar(64) NOT NULL DEFAULT '' COMMENT '部门名称',
	`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='部门表'";

	const TB_AUTH_POSITION_SQL = "
CREATE TABLE `".self::TB_AUTH_POSITION."` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
	`code` varchar(16) NOT NULL DEFAULT '' COMMENT '职位代码',
	`name` varchar(32) NOT NULL DEFAULT '' COMMENT '职位名称',
	`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='职位表'";

	const TB_AUTH_MENU_SQL = "
CREATE TABLE `".self::TB_AUTH_MENU."` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '菜单id',
	`pid` int(11) NOT NULL DEFAULT '0' COMMENT '父菜单id',
	`name` varchar(32) NOT NULL DEFAULT '' COMMENT '菜单名称',
	`sort_id` int(11) NOT NULL DEFAULT '0' COMMENT '顺序',
	`function_id` int(11) NOT NULL DEFAULT '0' COMMENT '功能id',
	`url_extend` varchar(64) NOT NULL DEFAULT '' COMMENT '扩展url',
	`app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '应用id',
	`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
	`icon` varchar(128) NOT NULL DEFAULT '' COMMENT '图标',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单表'";

	const TB_LOG_AUTH_LOGIN_SQL = "
CREATE TABLE `".self::TB_LOG_AUTH_LOGIN."` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
	`user_id` mediumint(8) DEFAULT '0' COMMENT '用户id',
	`ip` varchar(32) DEFAULT '' COMMENT '登录ip',
	`app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '应用id',
	`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '登录时间',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限系统-用户登录日志表'";

	const TB_LOG_AUTH_ACCESS_SQL = "
CREATE TABLE `".self::TB_LOG_AUTH_ACCESS."` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
	`app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '应用id',
	`user_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '用户id',
	`function_group_id` int(11) NOT NULL DEFAULT '0' COMMENT '功能组id',
	`function_id` int(11) NOT NULL DEFAULT '0' COMMENT '功能id',
	`url` varchar(256) NOT NULL DEFAULT '' COMMENT '访问url',
	`get` varchar(2048) NOT NULL DEFAULT '' COMMENT 'get参数',
	`post` varchar(2048) NOT NULL DEFAULT '' COMMENT 'post参数',
	`server_info` varchar(2048) NOT NULL DEFAULT '' COMMENT '服务器信息',
	`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '访问时间',
	PRIMARY KEY (`id`),
	KEY `user_info` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户访问日志表'";
}
