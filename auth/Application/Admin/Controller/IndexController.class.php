<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;

/**
 * 首页
 * @author Evan
 * @since 2016年5月12日
 */
class IndexController extends BaseController {
	/**
	 * 构造函数
	 */
	public function __construct() {
		//无需进行权限检测的功能
		\Org\Auth\AuthUtil::setNoAuthFuncList([
				'/admin/index/index',
				'/admin/index/keep',
				'/admin/index/changeApp',
				'/admin/index/updatePassword',
				'/admin/index/refreshAuth',
				'/admin/index/profile',
                '/admin/index/updateAvatar',
		]);
		parent::__construct();
	}
	
	/**
	 * 个人中心
	 */
	public function index() {
        $classList = ['bg-aqua', 'bg-red', 'bg-green', 'bg-yellow', 'bg-maroon', 'bg-orange', 'bg-olive', 'bg-purple'];

        $this->assign('classList', $classList);
		$this->loadFrame('index');
	}
	
	/**
	 * 切换应用
	 */
	public function changeApp() {
		$app_id = I('post.app_id');
	
		if(!$app_id) {
			return Response(2001, '参数错误');
		}
	
		//查询应用信息
		$appInfo = D('Resource/App', 'Service')->getAppInfo(['app_id'=>$app_id]);
	
		if(!$appInfo) {
			return Response(2002, '应用信息不存在');
		}
	
		//判断当前登录用户是否有权限查看该应用数据
		$userInfo = parent::$user;
		$userAppIdList = D('Resource/User', 'Service')->getUserRelationAppIdList($userInfo['id']);
		
		if(!in_array($app_id, $userAppIdList)) {
			return Response(2003, '您没有权限访问该应用');
		}
	
		//允许切换
		$_SESSION['_appid_'] = $app_id;
		return Response(999, "您已成功切换到：{$appInfo['name']}");
	}
	
	/**
	 * 修改用户密码
	 */
	public function updatePassword() {
		$old_password = I('post.old_password');
		$new_password = I('post.new_password');
		$userInfo = self::$user;
		
		if(!$old_password) {
			return Response(2001, '原始密码不能为空');
		}
		
		if(!$new_password) {
			return Response(2002, '新密码不能为空');
		}
		
		$data = [];
		$data['action'] = 'updateUserPassword';
		$data['username'] = $userInfo['username'];
		$data['old_password'] = md5($old_password);
		$data['password'] = $new_password;
		$apiTool = new \Common\ORG\Util\ApiTool();
		$result = $apiTool->getAuthData($data);
		
		if($result && $result['Code'] == 999) {
			//修改成功
			return Response(999, '密码修改成功');
		} else {
			return Response($result['Code'], $result['Msg']);
		}
	}
	
	/**
	 * 心跳
	 */
	public function keep() {}
	
	/**
	 * 刷新权限
	 */
	public function refreshAuth() {
		$userInfo = self::$user;
	
		$data = [];
		$data['action'] = 'refreshAuth';
		$data['username'] = $userInfo['username'];
		$data['password'] = $userInfo['password'];
		$apiTool = new \Common\ORG\Util\ApiTool();
		$result = $apiTool->getAuthData($data);
	
		if($result && $result['Code'] == 999) {
			//刷新成功
			$loginData = $result['Data'];
			
			//初始化权限
			\Org\Auth\AuthUtil::initAuth($loginData);
			
			//扩展信息
			$extData = $loginData['extData'];
			isset($extData['appList']) && $_SESSION['_appList_'] = $extData['appList'];
	
			return Response(999, '刷新权限成功', ['callback'=>'/']);
		} else {
			return Response($result['Code'], $result['Msg']);
		}
	}
	
	/**
	 * 个人主页
	 */
	public function profile() {
		$this->loadFrame('profile');
	}

    /**
     * 更新用户头像
     */
    public function updateAvatar() {
        $userInfo = self::$user;

        $img = I('post.img', '');
        if(!$img) {
            return Response(2001, '参数错误');
        }

        //更新用户头像
        $data = [];
        $data['action'] = 'updateUserAvatar';
        $data['admin_id'] = $userInfo['id'];
        $data['img'] = $img;
        $apiTool = new \Common\ORG\Util\ApiTool();
        $result = $apiTool->getAuthData($data);

        if($result && $result['Code'] == 999) {
            $userInfo['img'] = $img;

            $_SESSION['_user_'] = $userInfo;

            //修改成功
            return Response(999, '头像修改成功');
        } else {
            return Response($result['Code'], $result['Msg']);
        }
    }
}