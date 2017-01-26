<?php
namespace Resource\Model;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-岗位
 * @author Evan
 * @since 2016年5月11日
 */
class PositionModel extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_POSITION;	
		
	/**
	 * 查询岗位信息
	 * @param array $params
	 * @param string $fields
	 */
	public function getPositionInfo($params, $fields = '') {
		if($fields) {
			$positionInfo = $this->where($params)->field($fields)->find();
		} else {
			$positionInfo = $this->where($params)->find();
		}
		return $positionInfo;
	}
	
	/**
	 * 添加岗位
	 * @param array $params
	 */
	public function addPosition($params) {
		$position_id = $this->add($params);
		return $position_id;
	}
	
	/**
	 * 修改岗位
	 * @param array $params
	 */
	public function savePosition($params) {
		$result = $this->save($params);
		if($result !== false) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 查询岗位列表
	 * @param array $params
	 */
	public function getPositionList($params) {
		$positionList = $this->where($params)->getField('id,code,name,create_time', true);
		return $positionList;
	}
}