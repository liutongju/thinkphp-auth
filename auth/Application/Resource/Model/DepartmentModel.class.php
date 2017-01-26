<?php
namespace Resource\Model;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-部门
 * @author Evan
 * @since 2016年5月11日
 */
class DepartmentModel extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_DEPARTMENT;		
	
	/**
	 * 查询部门信息
	 * @param array $params
	 * @param string $fields
	 */
	public function getDepartmentInfo($params, $fields = '') {
		if($fields) {
			$departmentInfo = $this->where($params)->field($fields)->find();
		} else {
			$departmentInfo = $this->where($params)->find();
		}
		return $departmentInfo;
	}
	
	/**
	 * 添加部门
	 * @param array $params
	 */
	public function addDepartment($params) {
		$department_id = $this->add($params);
		return $department_id;
	}
	
	/**
	 * 修改部门
	 * @param array $params
	 */
	public function saveDepartment($params) {
		$result = $this->save($params);
		if($result !== false) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 查询部门列表
	 * @param array $params
	 */
	public function getDepartmentList($params) {
		$departmentList = $this->where($params)->getField('id,name,create_time', true);
		return $departmentList;
	}	
}