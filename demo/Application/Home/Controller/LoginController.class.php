<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 登录控制器
 * @author Evan
 * @since 2016年5月11日
 */
class LoginController extends Controller {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * 用户登录
	 */
	public function index() {
		$this->display('login');
	}

	/**
	 * 登录验证
	 */
	public function ajaxLoginCheck() {
		$username = I('post.username');
		$password = I('post.password');

		//验证验证码是否正确
		$geetest = new \Org\Geetest\web\Geetest();
		$verify = $geetest->checkGeeVerify();
		if(!$verify) {
			return Response(2003, '验证码错误');
		}

		if(!$username) {
			return Response(2001, '请输入用户名');
		}

		if(!$password) {
			return Response(2002, '请输入密码');
		}

		$data = [];
		$data['action'] = 'loginCheck';
		$data['username'] = $username;
		$data['password'] = md5($password);
		$data['ip'] = \Org\Util\Ip::get();
		$apiTool = new \Common\ORG\Util\ApiTool();
		$result = $apiTool->getAuthData($data);

		if($result && $result['Code'] == 999) {
			//登录成功
			$loginData = $result['Data'];

			//初始化权限
			\Org\Auth\AuthUtil::initAuth($loginData);

			//扩展信息
			$extData = $loginData['extData'];
			isset($extData['appList']) && $_SESSION['_appList_'] = $extData['appList'];

			$app_id = C('APP_ID');
			$_SESSION['_curr_appid_'] = $app_id;//当前应用appid
			$_SESSION['_appid_'] = $app_id;//待设置权限应用的appid

			return Response(999, '登录成功', ['callback'=>'/']);
		} else {
			return Response($result['Code'], $result['Msg']);
		}
	}

	/**
	 * 极验验证预处理
	 */
	public function ajaxStartCaptchaServlet() {
		$geetest = new \Org\Geetest\web\Geetest();
		echo $geetest->getResponseStr();
	}

	/**
	 * 退出登录
	 */
	public function logout() {
		session_unset();
		session_destroy();
		header('Location:/login');
		exit();
	}
}
