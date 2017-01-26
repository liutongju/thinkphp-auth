<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
//调试模式
define('APP_DEBUG', true);

// 定义公共模块目录
define('COMMON_PATH','./Application/Common/');

// 定义应用目录
define('APP_PATH','./Application/');

// 定义运行时目录
define('RUNTIME_PATH','/cache/Runtime/Auth/');

//日志路径
define('LOG_PATH', '/log/web/auth/');

//是否进行权限检测，正式环境请设为true
define('AUTH_CHECK', true);

// 引入ThinkPHP入口文件
require '../frame/ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单