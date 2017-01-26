<?php
namespace Resource\Model;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-功能组
 * @author Evan
 * @since 2016年5月11日
 */
class FunctionGroupModel extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_FUNCTION_GROUP;
		
	/**
	 * 查询功能组信息
	 * @param array $params
	 */
	public function getFunctionGroupInfo($params) {
		$groupInfo = $this->where($params)->find();
		return $groupInfo;
	}
	
	/**
	 * 查询功能组列表
	 * @param array $params
	 */
	public function getFunctionGroupList($params) {
		$groupList = $this->where($params)->getField('id,name,app_id,create_time', true);
		return $groupList;
	}
	
	/**
	 * 添加功能组
	 * @param array $params
	 */
	public function addFunctionGroup($params) {
		$group_id = $this->add($params);
		return $group_id;
	}
	
	/**
	 * 修改功能组
	 * @param array $params
	 */
	public function saveFunctionGroup($params) {
		$result = $this->save($params);
		if($result !== false) {
			return true;
		} else {
			return false;
		}
	}	
}