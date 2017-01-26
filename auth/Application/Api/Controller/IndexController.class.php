<?php
namespace Api\Controller;
use Think\Controller;

/**
 * 权限系统接口
 * @author Evan
 * @since 2016年5月16日
 */

class IndexController extends Controller {
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * 接口入口
	 */
	public function index() {
		//检测请求是否合法
		$params = api_check();
		
		//判断请求接口是否存在
		$action = $params['action'];
		if(!method_exists($this,$action)) {
			return Response(1008, '请求不合法');
		}
		
		//调用接口
		$this->$action($params);
	}

	/**
	 * 登录验证
	 * @param array $params
	 * app_id：应用id
	 * ip：IP地址
	 * username：用户名
	 * password：密码
	 */
	private function loginCheck($params) {
		$app_id = $params['appid'];
		$ip = $params['ip'] ? $params['ip'] : \Org\Util\Ip::get();
		$username = $params['username'] ? $params['username'] : '';
		$password = $params['password'] ? $params['password'] : '';
		
		if(!$username) {
			return Response(2001, '用户名不能为空');
		}
		
		if(!$password) {
			return Response(2002, '密码不能为空');
		}
		
		$data = [];
		$data['app_id'] = $app_id;
		$data['ip'] = $ip;
		$data['username'] = $username;
		$data['password'] = $password;
		D('Login', 'Logic')->loginCheck($data);
	}
	
	/**
	 * 修改用户密码
	 * @param array $params
	 * username：用户名
	 * old_password：原始密码
	 * password：新密码
	 */
	private function updateUserPassword($params) {
		$username = $params['username'] ? $params['username'] : '';
		$old_password = $params['old_password'] ? $params['old_password'] : '';
		$password = $params['password'] ? $params['password'] : '';
		
		if(!$username) {
			return Response(2001, '用户名不能为空');
		}
		
		if(!$old_password) {
			return Response(2002, '原始密码不能为空');
		}
		
		if(!$password) {
			return Response(2003, '新密码不能为空');
		}
		
		$data = [];
		$data['username'] = $username;
		$data['old_password'] = $old_password;
		$data['password'] = $password;
		D('Login', 'Logic')->updateUserPassword($data);
	}
	
	/**
	 * 刷新权限
	 * @param array $params
	 * app_id：应用id
	 * username：用户名
	 * password：密码
	 */
	private function refreshAuth($params) {
		$app_id = $params['appid'];
		$username = $params['username'] ? $params['username'] : '';
		$password = $params['password'] ? $params['password'] : '';
		
		if(!$username) {
			return Response(2001, '用户名不能为空');
		}
		
		if(!$password) {
			return Response(2002, '密码不能为空');
		}
		
		$data = [];
		$data['app_id'] = $app_id;
		$data['username'] = $username;
		$data['password'] = $password;
		D('Login', 'Logic')->refreshAuth($data);
	}

	/**
	 * 查询用户信息
	 * @param array $params
	 * uid：用户id
	 */
	private function getUserInfo($params) {
		$uid = intval($params['uid']);
		if(!$uid) {
			return Response(2001, 'UID不能为空');
		}
		
		$userInfo = D('Resource/User', 'Service')->getUserInfo(['id'=>$uid]);
		if($userInfo) {
			return Response(999, '获取数据成功', ['userInfo'=>$userInfo]);
		} else {
			return Response(2002, '获取数据失败');
		}
	}
	
	/**
	 * 查询用户列表
	 * @param array $params
	 */
	private function getUserList($params) {
		$userList = D('Resource/User', 'Service')->getUserList(['status'=>1]);
		if($userList) {
			return Response(999, '获取数据成功', ['userList'=>$userList]);
		} else {
			return Response(2002, '获取数据失败');
		}
	}
	
	/**
	 * 添加用户访问日志
	 * @param $params
	 */
	private function addUserAccessLog($params) {
		$app_id = $params['appid'];
		$user_id = intval($params['user_id']);
		$function_group_id = intval($params['function_group_id']);
		$function_id = intval($params['function_id']);
		$url = $params['url'] ? $params['url'] : '';
		$get = $params['get'] ? $params['get'] : '';
		$post = $params['post'] ? $params['post'] : '';
		$server_info = $params['server_info'] ? $params['server_info'] : '';
		
		$data = [];
		$data['app_id'] = $app_id;
		$data['user_id'] = $user_id;
		$data['function_group_id'] = $function_group_id;
		$data['function_id'] = $function_id;
		$data['url'] = $url;
		$data['get'] = $get;
		$data['post'] = $post;
		$data['server_info'] = $server_info;
		$result = D('Resource/Log', 'Service')->addAccessLog($data);
		if($result) {
			return Response(999, '操作成功');
		} else {
			return Response(2001, '操作失败');
		}
	}

    /**
     * 修改用户头像
     * @param array $params
     * admin_id：用户ID
     * avatar：头像
     */
    private function updateUserAvatar($params) {
        $admin_id = $params['admin_id'] ? intval($params['admin_id']) : 0;
        $img = $params['img'] ? $params['img'] : '';

        if(!$admin_id || !$img) {
            return Response(2001, '参数错误');
        }

        $data = [];
        $data['id'] = $admin_id;
        $data['img'] = $img;
        $result = D('Resource/User', 'Service')->saveUser($data);
        if($result) {
            return Response(999, '操作成功');
        } else {
            return Response(2002, '操作失败');
        }
    }
}