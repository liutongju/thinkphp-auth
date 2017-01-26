<?php
namespace Resource\Model;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-用户-功能
 * @author Evan
 * @since 2016年5月11日
 */
class UserFunctionModel extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_USER_FUNCTION;	
	
	/**
	 * 删除用户权限
	 * @param int $user_id
	 * @param string $app_id
	 */
	public function deleteUserFunction($user_id, $app_id) {
		$result = $this->where("user_id='{$user_id}' AND app_id='{$app_id}'")->delete();
		if($result !== false) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 添加用户权限
	 * @param array $params
	 */
	public function addUserFunction($params) {
		$result = $this->add($params);
		return $result;
	}		
}