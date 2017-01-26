<?php
namespace Home\Controller;

use Think\Controller;
use \Org\Auth\AuthUtil;
use \Common\ORG\Util\ApiTool;

/**
 * 基础控制器
 * @author Evan
 * @since 2016年5月11日
 */
class BaseController extends Controller {
	public static $user;//登录用户信息
	
	private static $_menuList;//菜单列表
	private static $_curr_parent_menu;//当前菜单所属的父菜单
	private static $_curr_menu;//当前菜单
	
	/**
	 * 构造函数
	 */
    public function __construct() {
        parent::__construct();
        
        //登录检测
        self::$user = AuthUtil::checkLogin('/login');

        //权限检测、判断是否为 ajax 请求
        if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){
            \Org\Auth\AuthUtil::checkAuth(C('URI_PREFIX'));
        }else{
            //普通请求，如果无权限时重定向到error页面
            \Org\Auth\AuthUtil::checkAuth(C('URI_PREFIX'), function($params) {
                $this->loadFrame('Public:error', $params);
            });
        };
        
        //生成菜单
        self::$_menuList = AuthUtil::createMenu();
        self::$_curr_parent_menu = AuthUtil::$curr_parent_menu;
        self::$_curr_menu = AuthUtil::$curr_menu;
        
    	//添加用户访问日志，无需设置权限的功能不添加日志
        if(!in_array(AuthUtil::$curr_path, AuthUtil::$noAuthFuncList)) {
        	$apiTool = new ApiTool();
        	$apiTool->getAuthData(AuthUtil::userAccessLog());
        }
    }
    
    /**
     * 页面渲染
     * @param string $template_file
     */
    protected function loadFrame($template_file = '', $params = []) {
        if(is_array($params) && count($params) > 0) {
            foreach($params as $key => $value) {
                $this->assign($key, $value);
            }
        }

    	$this->assign('_user_',  self::$user);
    	$this->assign('_menuList_', self::$_menuList);
    	$this->assign('_curr_parent_menu_', self::$_curr_parent_menu);
    	$this->assign('_curr_menu_', self::$_curr_menu);
    	
    	$this->display('Public:header');
    	$this->display('Public:left');
    	$this->display($template_file);
    	$this->display('Public:footer');
    }
}