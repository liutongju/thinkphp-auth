# 聚群权限管理系统
基于ThinkPHP3.2开发的一套后台权限管理系统，通过简单的配置就能实现功能强大的权限管理。

## 目录说明
auth、demo、static都是独立的项目，配置独立的域名
+ auth : 权限系统目录
+ demo : 一个对接权限系统的例子
+ static : 静态资源目录
+ frame : ThinkPHP框架放置的位置

## 系统界面
更多界面可在[static/auth-ui/](https://github.com/tangzwgo/thinkphp-auth/tree/master/static/auth-ui) 目录下查看

+ 登录页
![](https://github.com/tangzwgo/thinkphp-auth/blob/master/static/auth-ui/login.png?raw=true)

+ 首页
![](https://github.com/tangzwgo/thinkphp-auth/blob/master/static/auth-ui/home.png?raw=true)

## 功能介绍
系统共分为4个模块：权限管理、账号管理、应用管理、日志管理

#### 权限管理
+ 用户组列表

用户组代表一类用户的权限集合，用户通过管理不同的用户组而获得不同的系统权限。
  
包含功能：用户组列表、新增用户组、修改用户组、设置小组权限、查看小组成员。
  
+ 功能组列表

功能组主要作用是将功能分组，方便管理
  
包含功能：新增功能组、修改功能组

+ 功能列表

每个功能对应一个uri，当用户拥有某个功能的权限时才允许访问该uri

包含功能：新增功能、修改功能

+ 菜单列表

菜单是系统最直接的入口，共分为两级，第一级不关联任何功能，第二级菜单对应一个功能(uri)

包含功能：新增菜单、修改菜单、删除菜单

#### 账号管理

+ 用户列表

包含功能：添加用户、修改用户、关闭账号、查看用户权限、设置用户权限、扩展设置

+ 部门列表

包含功能：添加部门、修改部门

+ 岗位列表

包含功能：添加岗位、修改岗位

#### 应用管理

+ 应用列表
包含公司内部管理系统、APP、网站等应用的管理

包含功能：添加应用、修改应用、关闭应用、APP版本列表、添加APP版本、修改APP版本、删除APP版本

#### 日志管理

+ 登录日志

用户登录各个系统的日志

+ 访问日志

用户访问各个功能的日志

#### 其他

+ 首页可切换不用的系统，从而对不同的系统设置权限

+ 修改密码

+ 刷新权限，用户权限是在用户登录后存储在session中的，修改权限后不会更新用户session，所以需要手动刷新才能获取最新的权限

+ 在个人主页可修改头像

## 部署

+ 将auth和static这两个项目部署到服务器上，比如auth项目对应域名auth.juqun.tangzw.com，static项目对应域名static.juqun.tangzw.com

+ 修改auth/Application/Common/Conf/config.php文件（根据自己的实际情况修改）：

--------数据库相关的配置（DB_TYPE、DB_HOST、DB_NAME、DB_USER、DB_PWD、DB_PORT、DB_PREFIX）

--------域名相关的配置（POSTFIX_DOMAIN、AUTH_DOMAIN、STATIC_DOMAIN）

--------静态资源存放的路径（STATIC_PATH）

+ 访问http://AUTH_DOMAIN/install页面，进行权限初始化，你也可以自己手动在数据库中添加初始化权限，权限初始化后切记把这个页面删除或者设为禁止访问

+ 如果系统中有些地方不满足你的要求，你可以自己去修改代码，改成自己需要的样子，然后部署即可。

（如有任何问题或建议欢迎大家提交Issues或者加我微信<tangzwgo>向我反馈，谢谢^^）
