<?php
return [
    'LOAD_EXT_CONFIG' => 'auth_table,auth_table_sql',

	'MODULE_DENY_LIST' => ['Common','Resource'],//禁止直接通过url访问的模块
	'MODULE_ALLOW_LIST' => ['Api','Admin'],//允许通过url访问的模块
	'DEFAULT_MODULE' => 'Admin',//默认模块
    'DEFAULT_CONTROLLER' => 'Index',//默认控制器

	'DB_TYPE' => 'mysql', // 数据库类型
	'DB_HOST' => '192.168.245.128', // 数据库服务器地址
	'DB_NAME' => 'db_auth', // 数据库名称
	'DB_USER' => 'root', // 数据库用户名
	'DB_PWD' => 'root', // 数据库密码
	'DB_PORT' => '3306', // 数据库端口
	'DB_PREFIX' => '', // 数据表前缀

    //应用配置
    'APP_ID' => 'jqauth', //应用id
    'APP_SECRET' => 'euYQabSS8DPbW7B0sjgqDWOvWHaXdCEM', //应用秘钥
    'TOKEN' => 'ebL9kK9TIhdPIrldg1hJQf0AOUdPwPHi', //令牌
    'ENCODING_AES_KEY' => 'V6R5Ab8yzj47PiBYTCLHNCaBLgBDvDRG2UEntVFKsIR', //消息加解密密钥
    'IS_ENCRYPTION' => 0, //是否加密 1加密 0不加密

	'URI_PREFIX' => '/index.php',//uri前缀（url中隐藏的部分）

	'CSS_VERSION'=>'20170220',//CSS版本号
	'JS_VERSION'=>'20170220',//JS版本号
	'IMG_VERSION'=>'20170220',//图片版本号

	//域名
    'POSTFIX_DOMAIN' => 'juqun.tangzw.com',
    'AUTH_DOMAIN' => 'auth.juqun.tangzw.com',//权限系统域名
    'STATIC_DOMAIN' => 'static.juqun.tangzw.com',//静态资源域名

    'STATIC_PATH' => 'D:/workspace/thinkphp-auth/static/',//静态资源存放位置

    'AUTH_USER_DEFAULT_AVATAR' => 'AdminLTE/dist/img/user2-160x160.jpg',//后台用户默认头像
];
