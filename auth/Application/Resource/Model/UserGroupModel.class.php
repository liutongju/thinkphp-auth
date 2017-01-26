<?php
namespace Resource\Model;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-用户组
 * @author Evan
 * @since 2016年5月11日
 */
class UserGroupModel extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_USER_GROUP;
	
	/**
	 * 查询用户组信息
	 * @param array $params
	 */
	public function getUserGroupInfo($params) {
		$userGroupInfo = $this->where($params)->find();
		return $userGroupInfo;
	}

	/**
	 * 查询用户组列表
	 * @param array $params
	 */
	public function getUserGroupList($params) {
		$userGroupList = $this->where($params)->select();
		return $userGroupList;
	}
	
	/**
	 * 添加用户组
	 * @param array $params
	 */
	public function addUserGroup($params) {
		$group_id = $this->add($params);
		return $group_id;
	}
	
	/**
	 * 修改用户组
	 * @param array $params
	 */
	public function saveUserGroup($params) {
		$result = $this->save($params);
		if($result !== false) {
			return true;
		} else {
			return false;
		}
	}	
}