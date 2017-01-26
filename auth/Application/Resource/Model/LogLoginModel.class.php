<?php
namespace Resource\Model;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-登录日志
 * @author Evan
 * @since 2016年5月11日
 */
class LogLoginModel extends Model {
	protected $trueTableName = AuthTable::TB_LOG_AUTH_LOGIN;

	/**
	 * 添加登录日志
	 * @param unknown $params
	 */
	public function addLoginLog($params) {
		return $this->add($params);
	}
	
	/**
	 * 添加用户访问日志
	 * @param unknown $params
	 */
	public function addAccessLog($params) {
		return M('hiservice_log.log_auth_access')->add($params);
	}
	
	/**
	 * 查询用户登录日志数量
	 * @param unknown $params
	 */
	public function getUserLoginLogCount($params) {
		$num = $this->where($params)->count();
		return intval($num);
	}
	
	/**
	 * 查询用户登录日志列表
	 * @param unknown $params
	 */
	public function getUserLoginLogList($params, $page, $page_size) {
		$logList = $this->where($params)->order('id DESC')->limit($page_size)->page($page)->select();
		return $logList;
	}
}