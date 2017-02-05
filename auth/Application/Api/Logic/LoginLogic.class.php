<?php
namespace Api\Logic;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 登录
 * @author Evan
 * @since 2016年5月16日
 */
class LoginLogic extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_USER;

	/**
	 * 登录验证
	 * @param array $params
	 */
	public function loginCheck($params) {
		$app_id = $params['app_id'];
		$ip = $params['ip'];
		$username = $params['username'];
		$password = $params['password'];
		
		//查询用户信息
		$userInfo = D('Resource/User', 'Service')->getUserInfo(['username'=>$username]);
		if(!$userInfo) {
			return Response(2003, '用户不存在');
		}
		
		//查询用户是否有权限登录该系统
		$userAppIdList = D('Resource/User', 'Service')->getUserRelationAppIdList($userInfo['id']);
		if(!in_array($app_id, $userAppIdList)) {
			return Response(2004, '您没有权限登录该系统');
		}
		
		//验证密码是否正确
		if($password != $userInfo['password']) {
			return Response(2005, '密码错误');
		}
		
		//账号是否已关闭
		if($userInfo['status'] == -1) {
			return Response(2006, '您的账号已失效');
		}
		
		//登录成功
		//1、重置用户权限
		$user_function_ids = D('Resource/User', 'Service')->resetUserFunction($userInfo['id'], $app_id);
		
		//2、查询系统菜单
		$menuList = D('Resource/Menu', 'Service')->getMenuList($app_id);
		
		//3、查询系统功能
		$functionList = D('Resource/Function', 'Service')->getAllFunctionList($app_id);
		
		//4、添加登录日志
		$log = [];
		$log['user_id'] = $userInfo['id'];
		$log['ip'] = $ip;
		$log['app_id'] = $app_id;
		D('Resource/Log', 'Service')->addLoginLog($log);
		
		//5、查询扩展信息
		$extData = $this->getExtData($userInfo['id'], $app_id);
		!$extData && $extData = [];
		
		$data = [];
		$data['userInfo'] = $userInfo;
		$data['user_function_ids'] = $user_function_ids;
		$data['menuList'] = $menuList;
		$data['functionList'] = $functionList;
		$data['extData'] = $extData;
		return Response(999, '登录成功', $data);
	}

	/**
	 * 获取用户扩展信息
	 * @param int $user_id
	 * @param string $app_id
	 */
	private function getExtData($user_id, $app_id) {
		if($app_id == C('APP_ID')) {
			//权限系统，获取用户关联的应用
			$appList = D('Resource/User', 'Service')->getUserRelationAppList($user_id);
			return ['appList'=>$appList];
		}
	}
	
	/**
	 * 修改用户密码
	 * @param array $params
	 */
	public function updateUserPassword($params) {
		$username = $params['username'];
		$old_password = $params['old_password'];
		$password = $params['password'];
		
		//查询用户信息
		$userInfo = D('Resource/User', 'Service')->getUserInfo(['username'=>$username]);
		if(!$userInfo) {
			return Response(2003, '用户不存在');
		}
		
		if($old_password != $userInfo['password']) {
			return Response(2003, '原始密码错误');
		}
		
		if(strlen($password) < 6 || strlen($password) > 16) {
			return Response(2004, '密码长度必须大于6位小于16位');
		}
		
		//修改密码
		$result = D('Resource/User', 'Service')->saveUser(['id'=>$userInfo['id'], 'password'=>md5($password)]);
		if($result) {
			return Response(999, '密码修改成功');
		} else {
			return Response(2005, '密码修改失败');
		}
	}
	
	/**
	 * 刷新权限
	 * @param array $params
	 */
	public function refreshAuth($params) {
		$app_id = $params['app_id'];
		$username = $params['username'];
		$password = $params['password'];
	
		//查询用户信息
		$userInfo = D('Resource/User', 'Service')->getUserInfo(['username'=>$username]);
		if(!$userInfo) {
			return Response(2003, '用户不存在');
		}
	
		//查询用户是否有权限登录该系统
		$userAppIdList = D('Resource/User', 'Service')->getUserRelationAppIdList($userInfo['id']);
		if(!in_array($app_id, $userAppIdList)) {
			return Response(2004, '您没有权限登录该系统');
		}
	
		//验证密码是否正确
		if($password != $userInfo['password']) {
			return Response(2005, '密码错误');
		}
		
		//账号是否已关闭
		if($userInfo['status'] == -1) {
			return Response(2006, '您的账号已失效');
		}
	
		//登录成功
		//1、重置用户权限
		$user_function_ids = D('Resource/User', 'Service')->resetUserFunction($userInfo['id'], $app_id);
	
		//2、查询系统菜单
		$menuList = D('Resource/Menu', 'Service')->getMenuList($app_id);
	
		//3、查询系统功能
		$functionList = D('Resource/Function', 'Service')->getAllFunctionList($app_id);
		
		//4、查询扩展信息
		$extData = $this->getExtData($userInfo['id'], $app_id);
		!$extData && $extData = [];
	
		$data = [];
		$data['userInfo'] = $userInfo;
		$data['user_function_ids'] = $user_function_ids;
		$data['menuList'] = $menuList;
		$data['functionList'] = $functionList;
		$data['extData'] = $extData;
		return Response(999, '刷新成功', $data);
	}
}