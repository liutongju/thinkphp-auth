<?php
namespace Resource\Model;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-用户组功能
 * @author Evan
 * @since 2016年5月11日
 */
class UserGroupFunctionModel extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_USERGROUP_FUNCTION;		
	
	/**
	 * 查询用户组关联的功能列表
	 * @param array $userGroupIdList
	 */
	public function getUserGroupRelationFunctionList($userGroupIdList) {
		if(is_array($userGroupIdList) && count($userGroupIdList) > 0) {
			$where = "user_group_id IN (" . implode(',', $userGroupIdList) . ")";
		} else {
			$where = "user_group_id='{$userGroupIdList}'";
		}
		$userGroupFunctionList = $this->where($where)->select();
		return $userGroupFunctionList;
	}		
	
	/**
	 * 删除用户组关联的功能
	 * @param int $user_id
	 * @param string $app_id
	 */
	public function deleteUserGroupRelationFunction($user_group_id) {
		$result = $this->where("user_group_id='{$user_group_id}'")->delete();
		if($result !== false) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 添加用户组关联的功能
	 * @param array $params
	 */
	public function addUserGroupRelationFunction($params) {
		$result = $this->add($params);
		return $result;
	}		
}