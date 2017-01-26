<?php
namespace Resource\Model;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-用户-应用
 * @author Evan
 * @since 2016年5月11日
 */
class UserAppModel extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_USER_APP;	
	
	/**
	 * 查询用户关联的应用列表
	 * @param unknown $user_id
	 */
	public function getUserRelationAppList($user_id) {
		$tb_user_app = AuthTable::TB_AUTH_USER_APP;
		$tb_app = AuthTable::TB_APP;
		
		$userAppList = M("{$tb_user_app} haua")
		->join("{$tb_app} ha ON ha.app_id=haua.app_id")
		->where("uid='{$user_id}'")
		->getField('haua.app_id, haua.uid, ha.name, ha.is_auth, ha.domain, ha.type', true);
		return $userAppList;
	}			
	
	/**
	 * 删除用户关联的app
	 * @param int $user_id
	 */
	public function deleteUserRelationApp($user_id) {
		$result = $this->where("uid='{$user_id}'")->delete();
		if($result !== false) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 添加用户关联的应用
	 * @param unknown $params
	 */
	public function addUserRelationApp($params) {
		return $this->add($params);
	}
	
	/**
	 * 查询某个应用下的用户id列表
	 * @param unknown $app_id
	 */
	public function getAppUserIdList($app_id) {
		$uidList = $this->where("app_id='{$app_id}'")->getField('uid', true);
		return $uidList;
	}	
}