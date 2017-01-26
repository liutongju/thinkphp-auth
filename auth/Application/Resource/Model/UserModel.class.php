<?php
namespace Resource\Model;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-用户
 * @author Evan
 * @since 2016年5月11日
 */
class UserModel extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_USER;
	
	/**
	 * 查询用户信息
	 * @param array $params
	 * @param string $fields
	 */
	public function getUserInfo($params, $fields = '') {
		if($fields) {
			$userInfo = $this->where($params)->field($fields)->find();
		} else {
			$userInfo = $this->where($params)->find();
		}
		return $userInfo;
	}
	
	/**
	 * 添加用户
	 * @param array $params
	 */
	public function addUser($params) {
		$user_id = $this->add($params);
		return $user_id;
	}
	
	/**
	 * 修改用户
	 * @param array $params
	 */
	public function saveUser($params) {
		$result = $this->save($params);
		if($result !== false) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 查询用户列表
	 * @param array $params
	 */
	public function getUserList($params) {
		$userList = $this->where($params)->order('status DESC,id ASC')->getField('id,username,name,nickname,sex,department_id,position_id,status,create_time',true);
		return $userList;
	}

    /**
     * 获取最大的工号
     * @return unknown
     */
    public function maxWorkNumber() {
        $work_number = $this->where("work_number!=''")->order('work_number DESC')->getField('work_number');
        return $work_number;
    }
}