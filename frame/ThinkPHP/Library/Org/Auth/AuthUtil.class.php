<?php
namespace Org\Auth;

/**
 * 嗨修养车后台权限工具类
 * @author Evan <tangzwgo@foxmail.com>
 * @since 2016年5月26日
 */
class AuthUtil {
	public static $user;//登录用户信息
	
	public static $page_name;//当前页面功能名
	public static $curr_path;//当前链接
	public static $currFunction;//当前功能
	public static $menuList;//菜单列表
	public static $curr_parent_menu;//当前菜单所属的父菜单
	public static $curr_menu;//当前菜单
	public static $noAuthFuncList = [];//不检查权限功能列表
	
	/**
	 * 设置无需进行权限检测的功能
	 * @param array $funcList 不进行权限检测的链接
	 */
	public static function setNoAuthFuncList($funcList = []) {
		if(is_array($funcList) && count($funcList) > 0) {
			foreach ($funcList as $func) {
				!in_array($func, self::$noAuthFuncList) && self::$noAuthFuncList[] = $func;
			}
		} else if(is_string($funcList) && strlen($funcList) > 0) {
			!in_array($funcList, self::$noAuthFuncList) && self::$noAuthFuncList[] = $funcList;
		}
		return self::$noAuthFuncList;
	}
	
	/**
	 * 检查用户是否登录
	 * @param string $login_url 登录页面链接
	 */
	public static function checkLogin($login_url = '/login') {
		if(isset($_SESSION['_user_'])) {
			self::$user = $_SESSION['_user_'];
			return self::$user;
		} else {
			header("location:{$login_url}");
			exit(0);
		}
	}
	
	/**
	 * 检测权限
	 * @param string $uri_prefix 隐藏掉的url前缀，
     * @param function $callback 回调函数
	 * 如：完整的url：/index.php/admin/login/index  实际访问的url：/login/index  隐藏的url：/index.php/admin
	 */
	public static function checkAuth($uri_prefix = '', $callback = null) {
		//当前访问的url地址
		$request_uri = strtolower(__ACTION__);
		if($uri_prefix && strpos($request_uri, strtolower($uri_prefix)) === 0) {
			$request_uri = substr($request_uri, strlen($uri_prefix));
		}
		 
		//所有的功能列表
		$allFunctionList = $_SESSION['_allFunctionList_'];
		 
		//用户拥有的功能列表
		$function_ids = $_SESSION['_function_ids_'];
		$functionIdList = explode(',', $function_ids);
		 
		//判断url地址是否在功能列表中
		$in_function_list = false;
		$functionInfo = [];
		if(is_array($allFunctionList) && count($allFunctionList) > 0) {
			foreach($allFunctionList as $function_id => $function) {
				if($request_uri == strtolower($function['path'])) {
					$in_function_list = true;
					$functionInfo = $function;
					self::$page_name = $function['name'];
					self::$curr_path = $function['path'];
					self::$currFunction = $function;
				}
			}
		}

		//如果当前访问链接不在功能列表中，判断是否在不需设置权限的功能列表中
		if(!$in_function_list) {
			if(is_array(self::$noAuthFuncList) && count(self::$noAuthFuncList) > 0) {
				foreach (self::$noAuthFuncList as $noAuthFunction) {
					if($request_uri == strtolower($noAuthFunction)) {
						$in_function_list = true;
						$functionInfo = ['id'=>$noAuthFunction];
						$functionIdList[] = $noAuthFunction;
						self::$page_name = '无需权限功能';
						self::$curr_path = $noAuthFunction;
					}
				}
			}
		}

		if(!$in_function_list && AUTH_CHECK) {
            if(!is_null($callback) && is_callable($callback)) {
                call_user_func($callback, [
                    'code'=>404,
                    'msg'=>'您访问的页面不存在',
                    'tips'=>'提示：如果您确定您访问的链接是正确的，请联系管理员检查该链接是否添加到权限系统'
                ]);
                exit();
            } else {
                return self::Response(1001, '您访问的链接不存在');
            }
		}
		 
		//验证用户是否有该权限
		if(!in_array($functionInfo['id'], $functionIdList) && AUTH_CHECK) {
            if(!is_null($callback) && is_callable($callback)) {
                call_user_func($callback, [
                    'code'=>401,
                    'msg'=>'您没有权限访问该页面',
                    'tips'=>'提示：如果您需要该权限，请联系管理员开通'
                ]);
                exit();
            } else {
                return self::Response(1002, '您没有权限访问该链接');
            }
		}
	}
	
	/**
	 * 生成菜单
	 */
	public static function createMenu() {
		$function_ids = $_SESSION['_function_ids_'];
		$functionIdList = explode(',', $function_ids);
		 
		$allFunctionList = $_SESSION['_allFunctionList_'];
		 
		$allMenuList = $_SESSION['_allMenuList_'];
		$menuList = [];
		if(is_array($allMenuList) && count($allMenuList) > 0) {
			foreach ($allMenuList as $menu) {
				if(isset($menu['next']) || $menu['info']['function_id'] > 0) {
					$parentMenu = $menu['info'];
					$parent_function_id = $menu['info']['function_id'];
					$childMenuList = $menu['next'];
					if($parent_function_id > 0 && $allFunctionList[$parent_function_id]['path'] == self::$curr_path) {
						self::$curr_parent_menu = $parentMenu['name'];
						self::$curr_menu = $parentMenu['name'];
					}
					
					$parent_menu_path = $allFunctionList[$parent_function_id]['path'] . $parentMenu['info']['url_extend'];
					$menuList[$parentMenu['name']]['info'] = ['path'=>$parent_menu_path, 'icon'=>$parentMenu['icon'], 'name'=>$parentMenu['name']];
					$menuList[$parentMenu['name']]['subMenuList'] = [];
					
					if(is_array($childMenuList) && count($childMenuList) > 0) {
						foreach ($childMenuList as $childMenu) {
							$child_function_id = $childMenu['info']['function_id'];
							if(in_array($child_function_id, $functionIdList) || !AUTH_CHECK) {
								$menu_name = $childMenu['info']['name'];
								$menu_path = $allFunctionList[$child_function_id]['path'] . $childMenu['info']['url_extend'];
								$menuList[$parentMenu['name']]['subMenuList'][$menu_name] = ['path'=>$menu_path, 'icon'=>$childMenu['info']['icon'], 'name'=>$childMenu['info']['name']];
									
								if($allFunctionList[$child_function_id]['path'] == self::$curr_path) {
									self::$curr_parent_menu = $parentMenu['name'];
									self::$curr_menu = $menu_name;
								}
							}
						}
					}
				}
			}
		}
		self::$menuList = $menuList;
		
		return self::$menuList;
	}
	
	/**
	 * 初始化权限
	 * @param array $authData
	 */
	public static function initAuth($authData) {
		$userInfo = $authData['userInfo'];//用户信息
		$user_function_ids = $authData['user_function_ids'];//用户具备的功能id串
		$menuList = $authData['menuList'];//菜单列表
		$functionList = $authData['functionList'];//功能列表
		
		$_SESSION['_user_'] = $userInfo;
		$_SESSION['_function_ids_'] = $user_function_ids;
		$_SESSION['_allMenuList_'] = $menuList;
		$_SESSION['_allFunctionList_'] = $functionList;
	}
	
	/**
	 * 用户访问日志
	 */
	public static function userAccessLog() {
		$userInfo = self::$user;
		$functionInfo = self::$currFunction;
		
		$get = json_encode($_GET) . '';
		$post = json_encode($_POST) . '';
		if(strlen($get) > 2048) {
			$get = substr($get, 0, 2048);
		}
		if(strlen($post) > 2048) {
			$post = substr($post, 0, 2048);
		}
		
		$data = [];
		$data['action'] = 'addUserAccessLog';
		$data['user_id'] = intval($userInfo['id']);
		$data['function_group_id'] = intval($functionInfo['group_id']);
		$data['function_id'] = intval($functionInfo['id']);
		$data['url'] = self::$curr_path;
		$data['get'] = $get;
		$data['post'] = $post;
		$data['server_info'] = self::getServerInfo();
		return $data;
	}
	
	/**
	 * 获取服务器信息
	 * @return type
	 */
	private static function getServerInfo() {
		$ip = \Org\Util\Ip::get();
		if(isset($_SERVER['SERVER_ADDR'])) {
			$SERVER_ADDR = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
			$HTTP_REFERER = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			$REQUEST_URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
			$SERVER_NAME = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
			$serverInfo = 'SERVER_IP:' . $SERVER_ADDR . ';REFERER:' . $HTTP_REFERER . ';SERVER_NAME:' . $SERVER_NAME . ';URI:' . $REQUEST_URI;
		} else {
			$serverInfo = 'USER:' . $_SERVER['USER'] . ';HOME:' . $_SERVER['HOME'] . ';PWD:' . $_SERVER['PWD'] . ';SCRIPT_FILENAME:' . $_SERVER['SCRIPT_FILENAME'];
		}
		return 'IP:' . $ip . ';' . $serverInfo;
	}
	
	/**
	 * 响应数据
	 * @param int $ResponseCode 响应码
	 * @param string $ResponseMsg 响应信息
	 * @param array $ResponseData 响应数据
	 */
	private static function Response($ResponseCode = 999, $ResponseMsg = '接口请求成功', $ResponseData = []) {
		if(!is_numeric($ResponseCode)) {
			return '';
		}
		
		$result = array(
				'Code'=>$ResponseCode,
				'Msg'=>$ResponseMsg,
				'Data'=>$ResponseData,
				'Type'=>'json'
		);
		header("Content-type: text/html; charset=utf-8");
		echo json_encode($result);
		exit();
	}
}