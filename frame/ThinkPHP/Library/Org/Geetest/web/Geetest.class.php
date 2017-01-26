<?php
namespace Org\Geetest\web;

require_once dirname(dirname(__FILE__)) . '/config/config.php';

/**
 * 极验验证
 * @author Evan
 * @since 2016年5月11日
 */
class Geetest {
	private $_gee_type;
	private $_gee_user_id;
	private $_geetestLib;
	private $_gee_server = 0;
	
	public function __construct() {
		$this->initGeetest();
	}
	
	/**
	 * 初始化
	 * @param string $gee_type
	 * @param string $user_id
	 */
	private function initGeetest($gee_type = 'pc', $user_id = 'haixiu') {
		$this->_gee_user_id = $user_id;
		$this->_gee_type = $gee_type;
		$this->_geetestLib = new \Org\Geetest\lib\GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
	}
	
	/**
	 * 获取极验服务器状态
	 */
	private function getGeetestServerStatus() {
		$this->_gee_server = $this->_geetestLib->pre_process($this->_gee_user_id);
	}
	
	/**
	 * 获取极验服务器响应的数据
	 */
	public function getResponseStr() {
		$this->getGeetestServerStatus();
		session_start();
		$_SESSION['gtserver'] = $this->_gee_server;
		$_SESSION['gee_user_id'] = $this ->_gee_user_id;
		return $this->_geetestLib->get_response_str();
	}
	
	/**
	 * 检查验证码是否正确
	 */
	public function checkGeeVerify() {
		$challenge = $_POST['geetest_challenge'];
		$validate = $_POST['geetest_validate'];
		$seccode = $_POST['geetest_seccode'];
		if($_SESSION['gtserver'] == 1) {
			$result = $this->_geetestLib->success_validate($challenge, $validate, $seccode, $this->_gee_user_id);
			if ($result) {
				return true;
			} else{
				return false;
			}
		}else{
			if ($this->_geetestLib->fail_validate($challenge, $validate, $seccode)) {
				return true;
			}else{
				return false;
			}
		}
	}
}