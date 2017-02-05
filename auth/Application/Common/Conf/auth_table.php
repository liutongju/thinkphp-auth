<?php
/**
 * 权限系统表名配置
 * Author: Evan <tangzwgo@gmail.com>
 * Since: 2017/2/5
 */

return [
    'TB_APP' => \Common\ORG\Util\AuthTable::TB_APP, //应用表
    'TB_APP_VERSION' => \Common\ORG\Util\AuthTable::TB_APP_VERSION, //应用版本表
    'TB_AUTH_FUNCTION' => \Common\ORG\Util\AuthTable::TB_AUTH_FUNCTION, //功能表
    'TB_AUTH_FUNCTION_GROUP' => \Common\ORG\Util\AuthTable::TB_AUTH_FUNCTION_GROUP, //功能组表
    'TB_AUTH_USER' => \Common\ORG\Util\AuthTable::TB_AUTH_USER, //系统用户表
    'TB_AUTH_USER_GROUP' => \Common\ORG\Util\AuthTable::TB_AUTH_USER_GROUP, //用户组表
    'TB_AUTH_USER_USERGROUP' => \Common\ORG\Util\AuthTable::TB_AUTH_USER_USERGROUP, //用户用户组关联表
    'TB_AUTH_USERGROUP_FUNCTION' => \Common\ORG\Util\AuthTable::TB_AUTH_USERGROUP_FUNCTION, //用户组功能表
    'TB_AUTH_USER_FUNCTION' => \Common\ORG\Util\AuthTable::TB_AUTH_USER_FUNCTION, //用户功能表
    'TB_AUTH_USER_APP' => \Common\ORG\Util\AuthTable::TB_AUTH_USER_APP, //用户应用表
    'TB_AUTH_DEPARTMENT' => \Common\ORG\Util\AuthTable::TB_AUTH_DEPARTMENT, //部门表
    'TB_AUTH_POSITION' => \Common\ORG\Util\AuthTable::TB_AUTH_POSITION, //岗位表
    'TB_AUTH_MENU' => \Common\ORG\Util\AuthTable::TB_AUTH_MENU, //菜单表

    'TB_LOG_AUTH_LOGIN' => \Common\ORG\Util\AuthTable::TB_LOG_AUTH_LOGIN, //登录日志表
    'TB_LOG_AUTH_ACCESS' => \Common\ORG\Util\AuthTable::TB_LOG_AUTH_ACCESS, //访问日志表
];