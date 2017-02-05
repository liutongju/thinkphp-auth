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
}
