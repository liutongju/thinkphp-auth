<?php
namespace Admin\Controller;
use Think\Controller;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统安装
 * @author Evan <tangzwgo@163.com>
 * @since 2016-07-27
 */
class InstallController extends Controller {

	private $db_connection = null;

	public function __construct() {
		parent::__construct();

		$connection = mysql_connect(C('DB_HOST'), C('DB_USER'), C('DB_PWD'));
		if($connection) {
			$this->db_connection = $connection;
			mysql_select_db(C('DB_NAME'), $this->db_connection);
		}
	}

	public function __destruct() {
		parent::__destruct();

		if($this->db_connection) {
			mysql_close($this->db_connection);
		}
    }

	/**
	 * 安装入口
	 */
	public function index() {
		//配置
		$db_type = C('DB_TYPE');
		$db_host = C('DB_HOST');
		$db_name = C('DB_NAME');
		$db_user = C('DB_USER');
		$db_pwd = C('DB_PWD');
		$db_port = C('DB_PORT');
		$configList = [];
		$configList[] = ['key' => 'DB_TYPE', 'value' => ($db_type ? $db_type : '-'), 'status' => ($db_type ? 1 : 0)];
		$configList[] = ['key' => 'DB_HOST', 'value' => ($db_host ? $db_host : '-'), 'status' => ($db_host ? 1 : 0)];
		$configList[] = ['key' => 'DB_NAME', 'value' => ($db_name ? $db_name : '-'), 'status' => ($db_name ? 1 : 0)];
		$configList[] = ['key' => 'DB_USER', 'value' => ($db_user ? $db_user : '-'), 'status' => ($db_user ? 1 : 0)];
		$configList[] = ['key' => 'DB_PWD', 'value' => ($db_pwd ? $db_pwd : '-'), 'status' => ($db_pwd ? 1 : 0)];
		$configList[] = ['key' => 'DB_PORT', 'value' => ($db_port ? $db_port : '-'), 'status' => ($db_port ? 1 : 0)];

		//配置是否完整
		$config_status = 0;
		$db_type && $db_host && $db_name && $db_user && $db_pwd && $db_port && $config_status = 1;

		//数据库是否能连接
		$connection_status = 0;
		$config_status && $this->db_connection && $connection_status = 1;

		//数据库是否存在
		$db_exists = 0;
		$connection_status && $db_exists = $this->checkDBExist();

		$database_status = 0;
		$config_status && $connection_status && !$db_exists && $database_status = 1;

		if(!$config_status) {
			$msg = '数据库配置不完整';
		} else if(!$connection_status) {
			$msg = '数据库无法连接';
		} else if(!$db_exists) {
			$msg = '数据库不存在';
		} else {
			$msg = '数据库已创建';
		}

		//数据库
		$database = [
			'name' => $db_name,
			'status' => $database_status,
			'db_exists' => $db_exists,
			'msg' => $msg,
		];

		//数据表
		$tableList = $this->checkTableList($config_status, $connection_status, $db_exists);

		$tb_exists = 1;
        foreach($tableList as $table) {
            if($table['tb_exists'] == 0) {
                $tb_exists = 0;
            }
        }

		//默认用户
		$userInfo = $this->getDefaultUser();

		$this->assign('configList', $configList);
		$this->assign('database', $database);
		$this->assign('tableList', $tableList);
		$this->assign('userInfo', $userInfo);
        $this->assign('config_status', $config_status);
        $this->assign('db_exists', $db_exists);
        $this->assign('tb_exists', $tb_exists);
		$this->display('index');
	}

	/**
	 * 创建数据库
	 * @return [type] [description]
	 */
	public function ajaxCreateDB() {
		if(!$this->db_connection) {
			return Response(2001, '连接数据库失败');
		}

		$sql = "CREATE DATABASE " . C('DB_NAME') . " CHARACTER SET utf8 COLLATE 'utf8_general_ci'";
		$result = mysql_query($sql, $this->db_connection);

		if($result) {
			return Response(999, '数据库创建成功');
		} else {
			return Response(2002, '数据库创建失败[' . mysql_error() . ']');
		}
	}

	/**
	 * 创建数据表
	 * @return [type] [description]
	 */
	public function ajaxCreateTb() {
		$table = I('post.table');

		if(!$table) {
			return Response(2001, '参数错误');
		}

		$tableList = $this->getTableList();

		if(!isset($tableList[$table])) {
			return Response(2002, "无权创建{$table}表");
		}

		$sql = $tableList[$table]['sql'];

		if(!$sql) {
			return Response(2003, "没有找到{$table}表的建表SQL");
		}

		if(!$this->db_connection) {
			return Response(2004, '连接数据库失败');
		}

		if(!mysql_select_db(C('DB_NAME'), $this->db_connection)) {
			return Response(2005, '连接数据库失败');
		}

		$tableExist = $this->checkTableExist($table);

		if($tableExist['tb_exists'] == 1) {
			return Response(2006, $tableExist['msg']);
		}

		$result = mysql_query($sql, $this->db_connection);

		if($result) {
			return Response(999, $table . '表创建成功');
		} else {
			return Response(2002, $table . '表创建失败[' . mysql_error() . ']');
		}
	}

	/**
	 * 一键创建数据表
	 * @return [type] [description]
	 */
	public function ajaxCreateAllTb() {
		if(!$this->db_connection) {
			return Response(2001, '连接数据库失败');
		}

		if(!mysql_select_db(C('DB_NAME'), $this->db_connection)) {
			return Response(2002, '连接数据库失败');
		}

		$tableList = $this->getTableList();

		$total = 0;//总表数
		$success_total = 0;//创建成功数
		$fail_total = 0;//创建失败数
		$exist_total = 0;//已存在数

		foreach ($tableList as $key => $table) {
			//验证表是否创建
			$existInfo = $this->checkTableExist($table['name']);
			if($existInfo['tb_exists'] === 0) {
				$sql = $table['sql'];
				if(!$sql) {
					$fail_total ++;
					continue;
				}

				$result = mysql_query($sql, $this->db_connection);
				if($result) {
					$success_total ++;
				} else {
					$fail_total ++;
				}
			} else {
				$exist_total ++;
			}
			$total ++;
		}

		$msg = "总{$total}张表，{$success_total}张表创建成功，{$exist_total}张表已存在，{$fail_total}张表创建失败";

		return Response(999, $msg);
	}

	/**
	 * 初始化权限
	 * @return [type] [description]
	 */
	public function ajaxInitAuth() {
		$username = I('post.username');
		$password = I('post.password');

		if(!$username || !$password) {
			return Response(2001, '参数错误');
		}

		if(!$this->db_connection) {
			return Response(2001, '连接数据库失败');
		}

		if(!mysql_select_db(C('DB_NAME'), $this->db_connection)) {
			return Response(2002, '连接数据库失败');
		}

		//清空原始权限数据
		$tableList = $this->getTableList();
		foreach($tableList as $table) {
			$sql = "DELETE FROM {$table['name']}";
			mysql_query($sql, $this->db_connection);
			$sql = "ALTER TABLE {$table['name']} AUTO_INCREMENT=1";
			mysql_query($sql, $this->db_connection);
		}

		//生成新数据
		$this->insertAuthData(AuthTable::TB_APP, [
				'id'=>1,
				'app_id'=>C('APP_ID'),
				'name'=>C('APP_NAME'),
				'app_secret'=>C('APP_SECRET'),
				'token'=>C('TOKEN'),
				'encoding_AESKey'=>C('ENCODING_AES_KEY'),
				'is_encryption'=>C('IS_ENCRYPTION'),
				'create_time'=>time(),
				'type'=>1,
				'is_auth'=>1,
				'domain'=>C('AUTH_DOMAIN')
		]);

		$this->insertAllAuthData(AuthTable::TB_AUTH_FUNCTION_GROUP, [
				['id'=>1, 'name'=>'权限管理', 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>2, 'name'=>'账号管理', 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>3, 'name'=>'应用管理', 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>4, 'name'=>'日志管理', 'app_id'=>C('APP_ID'), 'create_time'=>time()],
		]);

		$this->insertAllAuthData(AuthTable::TB_AUTH_FUNCTION, [
				['id'=>1, 'path'=>'/admin/auth/functionGroupList', 'name'=>'功能组列表', 'group_id'=>1, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>2, 'path'=>'/admin/auth/saveFunctionGroup', 'name'=>'保存功能组信息', 'group_id'=>1, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>3, 'path'=>'/admin/auth/userGroupList', 'name'=>'用户组列表', 'group_id'=>1, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>4, 'path'=>'/admin/auth/saveUserGroup', 'name'=>'保存用户组信息', 'group_id'=>1, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>5, 'path'=>'/admin/auth/functionList', 'name'=>'功能列表', 'group_id'=>1, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>6, 'path'=>'/admin/auth/saveFunction', 'name'=>'保存功能信息', 'group_id'=>1, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>7, 'path'=>'/admin/auth/setUserGroupFunction', 'name'=>'设置用户组功能', 'group_id'=>1, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>8, 'path'=>'/admin/auth/saveUserGroupFunction', 'name'=>'保存用户组功能', 'group_id'=>1, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>9, 'path'=>'/admin/auth/menuList', 'name'=>'菜单列表', 'group_id'=>1, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>10, 'path'=>'/admin/auth/addMenu', 'name'=>'添加菜单', 'group_id'=>1, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>11, 'path'=>'/admin/auth/updateMenu', 'name'=>'修改菜单', 'group_id'=>1, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>12, 'path'=>'/admin/auth/deleteMenu', 'name'=>'删除菜单', 'group_id'=>1, 'app_id'=>C('APP_ID'), 'create_time'=>time()],

				['id'=>13, 'path'=>'/admin/user/userList', 'name'=>'用户列表', 'group_id'=>2, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>14, 'path'=>'/admin/user/editUserInfo', 'name'=>'编辑用户信息', 'group_id'=>2, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>15, 'path'=>'/admin/user/saveUser', 'name'=>'保存用户信息', 'group_id'=>2, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>16, 'path'=>'/admin/user/userFunctionList', 'name'=>'用户功能列表', 'group_id'=>2, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>17, 'path'=>'/admin/user/setUserFunction', 'name'=>'设置用户功能', 'group_id'=>2, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>18, 'path'=>'/admin/user/saveUserFunction', 'name'=>'保存用户功能', 'group_id'=>2, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>19, 'path'=>'/admin/user/setUserExtFunction', 'name'=>'设置用户扩展功能', 'group_id'=>2, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>20, 'path'=>'/admin/user/saveUserRelationApp', 'name'=>'保存用户可访问系统', 'group_id'=>2, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>21, 'path'=>'/admin/user/deleteUser', 'name'=>'关闭/开启账号', 'group_id'=>2, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>22, 'path'=>'/admin/user/departmentList', 'name'=>'部门列表', 'group_id'=>2, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>23, 'path'=>'/admin/user/saveDepartment', 'name'=>'保存部门信息', 'group_id'=>2, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>24, 'path'=>'/admin/user/positionList', 'name'=>'岗位列表', 'group_id'=>2, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>25, 'path'=>'/admin/user/savePosition', 'name'=>'保存岗位信息', 'group_id'=>2, 'app_id'=>C('APP_ID'), 'create_time'=>time()],

				['id'=>26, 'path'=>'/admin/app/appList', 'name'=>'应用列表', 'group_id'=>3, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>27, 'path'=>'/admin/app/editApp', 'name'=>'编辑应用', 'group_id'=>3, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>28, 'path'=>'/admin/app/saveApp', 'name'=>'保存应用信息', 'group_id'=>3, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>29, 'path'=>'/admin/app/deleteApp', 'name'=>'删除/开启应用', 'group_id'=>3, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
                ['id'=>30, 'path'=>'/admin/app/versionList', 'name'=>'版本列表', 'group_id'=>3, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
                ['id'=>31, 'path'=>'/admin/app/saveAppVersion', 'name'=>'保存应用版本', 'group_id'=>3, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
                ['id'=>32, 'path'=>'/admin/app/deleteAppVersion', 'name'=>'删除应用版本', 'group_id'=>3, 'app_id'=>C('APP_ID'), 'create_time'=>time()],

				['id'=>33, 'path'=>'/admin/log/loginLog', 'name'=>'登录日志', 'group_id'=>4, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>34, 'path'=>'/admin/log/accessLog', 'name'=>'访问日志', 'group_id'=>4, 'app_id'=>C('APP_ID'), 'create_time'=>time()],
		]);

		$this->insertAuthData(AuthTable::TB_AUTH_USER_GROUP, [
				'id'=>1,
				'app_id'=>C('APP_ID'),
				'name'=>'系统管理员',
				'create_time'=>time()
		]);

		$data = [];
		for($i=1; $i<=34; $i++) {
			$data[] = ['id'=>$i, 'user_group_id'=>1, 'function_id'=>$i, 'create_time'=>time()];
		}
		$this->insertAllAuthData(AuthTable::TB_AUTH_USERGROUP_FUNCTION, $data);

		$this->insertAllAuthData(AuthTable::TB_AUTH_MENU, [
				['id'=>1, 'pid'=>0, 'name'=>'权限管理', 'sort_id'=>1000, 'function_id'=>0, 'icon'=>'fa fa-fw fa-tachometer', 'url_extend'=>'', 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>2, 'pid'=>1, 'name'=>'用户组列表', 'sort_id'=>1100, 'function_id'=>3, 'icon'=>'', 'url_extend'=>'', 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>3, 'pid'=>1, 'name'=>'功能组列表', 'sort_id'=>1200, 'function_id'=>1, 'icon'=>'', 'url_extend'=>'', 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>4, 'pid'=>1, 'name'=>'功能列表', 'sort_id'=>1300, 'function_id'=>5, 'icon'=>'', 'url_extend'=>'', 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>5, 'pid'=>1, 'name'=>'菜单列表', 'sort_id'=>1400, 'function_id'=>9, 'icon'=>'', 'url_extend'=>'', 'app_id'=>C('APP_ID'), 'create_time'=>time()],

				['id'=>6, 'pid'=>0, 'name'=>'账号管理', 'sort_id'=>10000, 'function_id'=>0, 'icon'=>'fa fa-fw fa-users', 'url_extend'=>'', 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>7, 'pid'=>6, 'name'=>'用户列表', 'sort_id'=>11000, 'function_id'=>13, 'icon'=>'', 'url_extend'=>'', 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>8, 'pid'=>6, 'name'=>'部门列表', 'sort_id'=>12000, 'function_id'=>22, 'icon'=>'', 'url_extend'=>'', 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>9, 'pid'=>6, 'name'=>'岗位列表', 'sort_id'=>13000, 'function_id'=>24, 'icon'=>'', 'url_extend'=>'', 'app_id'=>C('APP_ID'), 'create_time'=>time()],

				['id'=>10, 'pid'=>0, 'name'=>'应用管理', 'sort_id'=>100000, 'function_id'=>0, 'icon'=>'fa fa-fw fa-android', 'url_extend'=>'', 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>11, 'pid'=>10, 'name'=>'应用列表', 'sort_id'=>110000, 'function_id'=>26, 'icon'=>'', 'url_extend'=>'', 'app_id'=>C('APP_ID'), 'create_time'=>time()],

				['id'=>12, 'pid'=>0, 'name'=>'日志管理', 'sort_id'=>1000000, 'function_id'=>0, 'icon'=>'fa fa-fw fa-file-code-o', 'url_extend'=>'', 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>13, 'pid'=>12, 'name'=>'登录日志', 'sort_id'=>1100000, 'function_id'=>30, 'icon'=>'', 'url_extend'=>'', 'app_id'=>C('APP_ID'), 'create_time'=>time()],
				['id'=>14, 'pid'=>12, 'name'=>'访问日志', 'sort_id'=>1200000, 'function_id'=>31, 'icon'=>'', 'url_extend'=>'', 'app_id'=>C('APP_ID'), 'create_time'=>time()],
		]);

		$this->insertAuthData(AuthTable::TB_AUTH_USER, [
				'id'=>1,
				'username'=>$username,
				'password'=>md5($password),
				'name'=>$username,
				'nickname'=>$username,
				'sex'=>1,
				'department_id'=>0,
				'position_id'=>0,
				'work_number'=>'',
				'img'=>'AdminLTE/dist/img/user2-160x160.jpg',
				'desc'=>'',
				'wechat'=>'',
				'qq'=>'',
				'email'=>'',
                'mobile'=>'',
				'school'=>'',
				'address'=>'',
				'skill'=>'',
				'create_time'=>time()
		]);

		$this->insertAuthData(AuthTable::TB_AUTH_USER_USERGROUP, [
				'id'=>1,
				'user_group_id'=>1,
				'user_id'=>1,
				'app_id'=>C('APP_ID'),
				'create_time'=>time(),
		]);

		$this->insertAuthData(AuthTable::TB_AUTH_USER_FUNCTION, [
				'id'=>1,
				'user_id'=>1,
				'function_ids'=>'1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34',
				'app_id'=>C('APP_ID'),
				'update_time'=>time(),
		]);

		$this->insertAuthData(AuthTable::TB_AUTH_USER_APP, [
				'id'=>1,
				'uid'=>1,
				'app_id'=>C('APP_ID')
		]);

		return Response(999, '操作成功');
	}

	/**
	 * 检查数据库是否存在
	 * @param unknown $db_name
	 */
	private function checkDbExist() {
		if($this->db_connection && mysql_select_db(C('DB_NAME'), $this->db_connection)) {
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * 验证表是否存在
	 * @param unknown $tb_name
	 */
	private function checkTableExist($tb_name) {
		$sql = "SHOW TABLES LIKE '{$tb_name}'";
		if(mysql_num_rows(mysql_query($sql, $this->db_connection)) === 1) {
		    return ['tb_exists'=>1, 'msg'=>$tb_name . '表已存在'];
		} else {
		    return ['tb_exists'=>0, 'msg'=>$tb_name . '表不存在'];
		}
	}

	/**
	 * 获取表列表
	 * @return [type] [description]
	 */
	private function getTableList() {
		$tableList = [];
		$tableList[C('TB_APP')] = ['name'=>C('TB_APP'), 'sql'=>C('TB_APP_SQL'), 'desc'=>'应用表', 'status'=>0, 'tb_exists'=>0, 'msg'=>'', 'base_auth'=>1];
        $tableList[C('TB_APP_VERSION')] = ['name'=>C('TB_APP_VERSION'), 'sql'=>C('TB_APP_VERSION_SQL'), 'desc'=>'应用版本表', 'status'=>0, 'tb_exists'=>0, 'msg'=>'', 'base_auth'=>0];
		$tableList[C('TB_AUTH_FUNCTION')] = ['name'=>C('TB_AUTH_FUNCTION'), 'sql'=>C('TB_AUTH_FUNCTION_SQL'), 'desc'=>'功能表', 'status'=>0, 'tb_exists'=>0, 'msg'=>'', 'base_auth'=>1];
		$tableList[C('TB_AUTH_FUNCTION_GROUP')] = ['name'=>C('TB_AUTH_FUNCTION_GROUP'), 'sql'=>C('TB_AUTH_FUNCTION_GROUP_SQL'), 'desc'=>'功能组表', 'status'=>0, 'tb_exists'=>0, 'msg'=>'', 'base_auth'=>1];
		$tableList[C('TB_AUTH_USER')] = ['name'=>C('TB_AUTH_USER'), 'sql'=>C('TB_AUTH_USER_SQL'), 'desc'=>'系统用户表', 'status'=>0, 'tb_exists'=>0, 'msg'=>'', 'base_auth'=>0];
		$tableList[C('TB_AUTH_USER_GROUP')] = ['name'=>C('TB_AUTH_USER_GROUP'), 'sql'=>C('TB_AUTH_USER_GROUP_SQL'), 'desc'=>'用户组表', 'status'=>0, 'tb_exists'=>0, 'msg'=>'', 'base_auth'=>1];
		$tableList[C('TB_AUTH_USER_USERGROUP')] = ['name'=>C('TB_AUTH_USER_USERGROUP'), 'sql'=>C('TB_AUTH_USER_USERGROUP_SQL'), 'desc'=>'用户用户组关联表', 'status'=>0, 'tb_exists'=>0, 'msg'=>'', 'base_auth'=>0];
		$tableList[C('TB_AUTH_USERGROUP_FUNCTION')] = ['name'=>C('TB_AUTH_USERGROUP_FUNCTION'), 'sql'=>C('TB_AUTH_USERGROUP_FUNCTION_SQL'), 'desc'=>'用户组功能表', 'status'=>0, 'tb_exists'=>0, 'msg'=>'', 'base_auth'=>1];
		$tableList[C('TB_AUTH_USER_FUNCTION')] = ['name'=>C('TB_AUTH_USER_FUNCTION'), 'sql'=>C('TB_AUTH_USER_FUNCTION_SQL'), 'desc'=>'用户功能表', 'status'=>0, 'tb_exists'=>0, 'msg'=>'', 'base_auth'=>0];
		$tableList[C('TB_AUTH_USER_APP')] = ['name'=>C('TB_AUTH_USER_APP'), 'sql'=>C('TB_AUTH_USER_APP_SQL'), 'desc'=>'用户应用表', 'status'=>0, 'tb_exists'=>0, 'msg'=>'', 'base_auth'=>0];
		$tableList[C('TB_AUTH_DEPARTMENT')] = ['name'=>C('TB_AUTH_DEPARTMENT'), 'sql'=>C('TB_AUTH_DEPARTMENT_SQL'), 'desc'=>'部门表', 'status'=>0, 'tb_exists'=>0, 'msg'=>'', 'base_auth'=>0];
		$tableList[C('TB_AUTH_POSITION')] = ['name'=>C('TB_AUTH_POSITION'), 'sql'=>C('TB_AUTH_POSITION_SQL'), 'desc'=>'岗位表', 'status'=>0, 'tb_exists'=>0, 'msg'=>'', 'base_auth'=>0];
		$tableList[C('TB_AUTH_MENU')] = ['name'=>C('TB_AUTH_MENU'), 'sql'=>C('TB_AUTH_MENU_SQL'), 'desc'=>'菜单表', 'status'=>0, 'tb_exists'=>0, 'msg'=>'', 'base_auth'=>1];
		$tableList[C('TB_LOG_AUTH_LOGIN')] = ['name'=>C('TB_LOG_AUTH_LOGIN'), 'sql'=>C('TB_LOG_AUTH_LOGIN_SQL'), 'desc'=>'登录日志表', 'status'=>0, 'tb_exists'=>0, 'msg'=>'', 'base_auth'=>0];
		$tableList[C('TB_LOG_AUTH_ACCESS')] = ['name'=>C('TB_LOG_AUTH_ACCESS'), 'sql'=>C('TB_LOG_AUTH_ACCESS_SQL'), 'desc'=>'访问日志表', 'status'=>0, 'tb_exists'=>0, 'msg'=>'', 'base_auth'=>0];
		return $tableList;
	}

	/**
	 * 检查表状态
	 * @param  [int] $config_status     配置是否完整1是0否
	 * @param  [int] $connection_status 数据库是否能连接1是0否
	 * @param  [int] $db_exists         数据库是否存在1是0否
	 * @return [array]                  数据表列表
	 */
	private function checkTableList($config_status, $connection_status, $db_exists) {
		if(!$config_status) {
			$msg = '数据库配置不完整';
		} else if(!$connection_status) {
			$msg = '数据库无法连接';
		} else if(!$db_exists) {
			$msg = '数据库不存在';
		}

		$tableList = $this->getTableList();

		if(!$config_status || !$connection_status || !$db_exists) {
			foreach($tableList as $key => $table) {
				$tableList[$key]['msg'] = $msg;
			}

			return $tableList;
		}

		foreach ($tableList as $key => $table) {
			//验证表是否创建
			$existInfo = $this->checkTableExist($table['name']);
			$tableList[$key]['tb_exists'] = $existInfo['tb_exists'];
			$tableList[$key]['msg'] = $existInfo['msg'];
			$existInfo['tb_exists'] === 0 && $tableList[$key]['status'] = 1;
		}

		return $tableList;
	}

	/**
	 * 查询默认用户
	 */
	private function getDefaultUser() {
		if(!$this->db_connection) {
			return null;
		}

		$sql = "SELECT * FROM " . AuthTable::TB_AUTH_USER . " ORDER BY id ASC LIMIT 1";
		$result = mysql_query($sql, $this->db_connection);
		if(!$result) {
			return null;
		}

		$userInfo = mysql_fetch_array($result);

		return $userInfo;
	}

	/**
	 * 插入数据到数据库
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	private function insertAuthData($table, $params) {
		if(!$table || !$params || !is_array($params)) {
			return 0;
		}

		$keys = array_keys($params);
		$values = array_values($params);

		$sql = "INSERT INTO {$table} (`" . implode("`,`", $keys) . "`) VALUES ('" . implode("','", $values) . "')";
		$result = mysql_query($sql, $this->db_connection);

		if($result) {
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * 批量插入数据到数据库
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	private function insertAllAuthData($table, $params) {
		if(!$table || !$params || !is_array($params)) {
			return 0;
		}

		$keys = [];
		$values = [];

		foreach ($params as $param) {
			$keys = array_keys($param);
			$values[] = "('" . implode("','", array_values($param)) . "')";
		}

		$sql = "INSERT INTO {$table} (`" . implode("`,`", $keys) . "`) VALUES " . implode(",", $values);
		$result = mysql_query($sql, $this->db_connection);

		if($result) {
			return 1;
		} else {
			return 0;
		}
	}
}
