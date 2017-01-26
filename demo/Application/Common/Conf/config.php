<?php
return [
    'LOAD_EXT_CONFIG' => '',

	'MODULE_DENY_LIST' => ['Common','Resource'],//禁止直接通过url访问的模块
	'DEFAULT_MODULE' => 'Home',//默认模块
    'DEFAULT_CONTROLLER' => 'Index',//默认控制器

	'DB_TYPE' => 'mysql', // 数据库类型
	'DB_HOST' => '192.168.245.128', // 数据库服务器地址
	'DB_NAME' => 'db_auth', // 数据库名称
	'DB_USER' => 'root', // 数据库用户名
	'DB_PWD' => 'root', // 数据库密码
	'DB_PORT' => '3306', // 数据库端口
	'DB_PREFIX' => '', // 数据表前缀

    //应用配置
    'APP_ID' => 'demo', //应用id
    'APP_SECRET' => 'oyR1bNo8OCM1NuGziBUZ6elFKCw56Vtb', //应用秘钥
    'TOKEN' => 'SfDuZ1IW1mG9VF6xc6A8LLrNWg8f8vkj', //令牌
    'ENCODING_AES_KEY' => 'xU9WavQpT5XQ15v1jIMP1VLBoIHNOQfN93dRRapVZvF', //消息加解密密钥
    'IS_ENCRYPTION' => 0, //是否加密 1加密 0不加密
    'APP_NAME' => '系统Demo', //应用名称

	'CSS_VERSION'=>'20160720',//CSS版本号
	'JS_VERSION'=>'20160720',//JS版本号
	'IMG_VERSION'=>'20160720',//图片版本号

	//域名
	'POSTFIX_DOMAIN' => 'juqun.tangzw.com',
	'AUTH_DOMAIN' => 'auth.juqun.tangzw.com',//权限系统域名
	'STATIC_DOMAIN' => 'static.juqun.tangzw.com',//静态资源域名
    'DEMO_DOMAIN' => 'demo.juqun.tangzw.com',//Demo

	'STATIC_PATH' => 'D:/workspace/thinkphp-auth/static',//静态资源存放位置
];
