<?php
namespace Resource\Model;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-应用
 * @author Evan
 * @since 2016年5月11日
 */
class AppModel extends Model {
	protected $trueTableName = AuthTable::TB_APP;
	
	/**
	 * 查询应用列表
	 * @param array $params
	 */
	public function getAppList($params) {
		$appList = $this->where($params)->select();
		return $appList;
	}
	
	/**
	 * 查询应用信息
	 * @param unknown $params
	 */
	public function getAppInfo($params) {
		$appInfo = $this->where($params)->find();
		return $appInfo;
	}
	
	/**
	 * 添加应用
	 * @param unknown $params
	 */
	public function addApp($params) {
		return $this->add($params);
	}
	
	/**
	 * 修改应用信息
	 * @param unknown $params
	 */
	public function updateAppInfo($params) {
		$result = $this->save($params);
		if($result !== false) {
			return true;
		} else {
			return false;
		}
	}
}