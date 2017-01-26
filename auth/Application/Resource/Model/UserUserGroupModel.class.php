<?php
namespace Resource\Model;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-用户-用户组
 * @author Evan
 * @since 2016年5月11日
 */
class UserUserGroupModel extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_USER_USERGROUP;		
	
	/**
	 * 查询用户组下的用户id列表
	 * @param int $user_group_id
	 */
	public function getGroupUserIdList($user_group_id) {
		$userIdList = $this->where("user_group_id='{$user_group_id}'")->getField('user_id', true);
		return $userIdList;
	}
	
	/**
	 * 查询用户关联的用户组列表
	 * @param int $user_id
	 * @param string $app_id
	 */
	public function getUserRelationUserGroupList($user_id, $app_id) {
		$userGroupList = $this->where("user_id='{$user_id}' AND app_id='{$app_id}'")->select();
		return $userGroupList;
	}
	
	/**
	 * 查询用户组下的成员数
	 * @param unknown $group_id
	 */
	public function getUserGroupUserNum($group_id) {
		$user_num = $this->where("user_group_id='{$group_id}'")->count();
		return intval($user_num);
	}	
	
	/**
	 * 删除用户关联的用户组
	 * @param int $user_id
	 * @param string $app_id
	 */
	public function deleteUserRelationGroup($user_id, $app_id) {
		$result = $this->where("user_id='{$user_id}' AND app_id='{$app_id}'")->delete();
		if($result !== false) {
			return true;
		} else {
			return false;
		}
	}		

	/**
	 * 添加用户关联的用户组
	 * @param array $params
	 */
	public function addUserRelationGroup($params) {
		$result = $this->add($params);
		return $result;
	}		
}