<?php
namespace Resource\Service;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-用户
 * @author Evan
 * @since 2016年5月11日
 */
class UserService extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_USER;
	
	/**
	 * 查询用户信息
	 * @param array $params
	 */
	public function getUserInfo($params) {
		$userInfo = D('Resource/User')->getUserInfo($params);
		if($userInfo) {
			//查询岗位信息
			$positionInfo = $this->getPositionInfo(['id'=>$userInfo['position_id']]);
			$userInfo['position_name'] = $positionInfo['name'];
			
			//查询部门信息
			$departmentInfo = $this->getDepartmentInfo(['id'=>$userInfo['department_id']]);
			$userInfo['department_name'] = $departmentInfo['name'];
			
			//将技能拆分成数组
			$userInfo['skill'] && $userInfo['skillList'] = explode(',', $userInfo['skill']);

			//用户头像
            $userInfo['img_url'] = $userInfo['img'] ? load_img($userInfo['img']) : load_img(C('AUTH_USER_DEFAULT_AVATAR'));
		}
		return $userInfo;
	}
	
	/**
	 * 查询用户姓名
	 * @param $uid
	 */
	public function getUserNameById($uid) {
		$userInfo = D('Resource/User')->getUserInfo(['id'=>$uid], 'name');
		return $userInfo['name'];
	}
	
	/**
	 * 查询用户列表
	 * @param array $params
	 */
	public function getUserList($params) {
		if(isset($params['app_id'])) {
			//查询可访问该系统的用户
			$uidList = D('Resource/UserApp')->getAppUserIdList($params['app_id']);
			unset($params['app_id']);
			$params['id'] = ['in',strval(implode(',', $uidList))];
		}
		
		$userList = D('Resource/User')->getUserList($params);
		
		return $userList;
	}
	
	/**
	 * 添加用户
	 * @param array $params
	 */
	public function addUser($params) {
		$params['create_time'] = time();
		return D('Resource/User')->addUser($params);
	}
	
	/**
	 * 修改用户
	 * @param array $params
	 */
	public function saveUser($params) {
		return D('Resource/User')->saveUser($params);
	}

	/**
	 * 查询用户组下的用户列表
	 * @param int $user_group_id
	 */
	public function getGroupUserList($user_group_id) {
		$userIdList = D('Resource/UserUserGroup')->getGroupUserIdList($user_group_id);
		$userList = D('Resource/User')->getUserList(['id'=>['in', strval(implode(',', $userIdList))]]);
		return $userList;
	}
	
	/**
	 * 重置用户权限
	 * @param int $user_id
	 * @param string $app_id
	 */
	public function resetUserFunction($user_id, $app_id) {
		//查询用户关联的功能id列表
		$functionIdList = $this->getUserRelationFunctionIdList($user_id, $app_id);
		
		//删除用户原功能
		D('Resource/UserFunction')->deleteUserFunction($user_id, $app_id);
		
		//重新添加用户权限
		$data = [];
		$data['user_id'] = $user_id;
		$data['function_ids'] = implode(',', $functionIdList);
		$data['app_id'] = $app_id;
		D('Resource/UserFunction')->addUserFunction($data);
		return $data['function_ids'];
	}
	
	/**
	 * 查询用户关联的功能列表
	 * @param int $user_id
	 * @param string $app_id
	 */
	public function getUserRelationFunctionIdList($user_id, $app_id) {
		//查询用户关联的用户组
		$userGroupList = D('Resource/UserUserGroup')->getUserRelationUserGroupList($user_id, $app_id);
		
		$usergroupIdList = [];
		if(is_array($userGroupList) && count($userGroupList)>0) {
			foreach($userGroupList as $userGroup) {
				$usergroupIdList[] = $userGroup['user_group_id'];
			}
		}
		
		//查询功能组关联的功能列表
		$userGroupFunctionList = D('Resource/UserGroupFunction')->getUserGroupRelationFunctionList($usergroupIdList);
		
		$functionIdList = [];
		if(is_array($userGroupFunctionList) && count($userGroupFunctionList)>0) {
			foreach($userGroupFunctionList as $userGroupFunction) {
				$functionIdList[] = $userGroupFunction['function_id'];
				
			}
		}
		
		return $functionIdList;
	}
	
	/**
	 * 查询用户关联的用户组Id列表
	 * @param $user_id
	 * @param $app_id
	 */
	public function getUserRelationUserGroupIdList($user_id, $app_id) {
		//查询用户关联的用户组
		$userGroupList = D('Resource/UserUserGroup')->getUserRelationUserGroupList($user_id, $app_id);
		
		$userGroupIdList = [];
		if(is_array($userGroupList) && count($userGroupList)>0) {
			foreach($userGroupList as $userGroup) {
				$userGroupIdList[] = $userGroup['user_group_id'];		
			}
		}
		
		return $userGroupIdList;
	}
	
	/**
	 * 查询用户组关联的功能id列表
	 * @param $user_group_id
	 */
	public function getUserGroupRelationFunctionIdList($user_group_id) {
		$userGroupFunctionList = D('Resource/UserGroupFunction')->getUserGroupRelationFunctionList($user_group_id);
		
		$functionIdList = [];
		if(is_array($userGroupFunctionList) && count($userGroupFunctionList)>0) {
			foreach($userGroupFunctionList as $userGroupFunction) {
				$functionIdList[] = $userGroupFunction['function_id'];
		
			}
		}
		
		return $functionIdList;
	}

	/**
	 * 查询用户组信息
	 * @param array $params
	 */
	public function getUserGroupInfo($params) {
		$groupInfo = D('Resource/UserGroup')->getUserGroupInfo($params);
		return $groupInfo;
	}
	
	/**
	 * 查询用户组列表
	 * @param array $params
	 */
	public function getUserGroupList($params) {
		$groupList = D('Resource/UserGroup')->getUserGroupList($params);
		if(is_array($groupList) && count($groupList) > 0) {
			foreach ($groupList as $key => $group) {
				//查询用户组成员数
				$user_num = D('Resource/UserUserGroup')->getUserGroupUserNum($group['id']);
				$groupList[$key]['user_num'] = $user_num;
			}
		}
		return $groupList;
	}
	
	/**
	 * 添加用户组
	 * @param array $params
	 */
	public function addUserGroup($params) {
		$params['create_time'] = time();
		return D('Resource/UserGroup')->addUserGroup($params);
	}
	
	/**
	 * 修改用户组
	 * @param array $params
	 */
	public function saveUserGroup($params) {
		return D('Resource/UserGroup')->saveUserGroup($params);
	}
	
	/**
	 * 删除用户关联的用户组
	 * @param int $user_id
	 * @param string $app_id
	 */
	public function deleteUserRelationGroup($user_id, $app_id) {
		return D('Resource/UserUserGroup')->deleteUserRelationGroup($user_id, $app_id);
	}
	
	/**
	 * 删除用户组关联的功能
	 * @param int $user_group_id
	 */
	public function deleteUserGroupRelationFunction($user_group_id) {
		return D('Resource/UserGroupFunction')->deleteUserGroupRelationFunction($user_group_id);
	}
	
	/**
	 * 添加用户关联的用户组
	 * @param array $params
	 */
	public function addUserRelationGroup($params) {
		$params['create_time'] = time();
		$result = D('Resource/UserUserGroup')->addUserRelationGroup($params);
		return $result;
	}
	
	/**
	 * 添加用户组关联的功能
	 * @param array $params
	 */
	public function addUserGroupRelationFunction($params) {
		$params['create_time'] = time();
		$result = D('Resource/UserGroupFunction')->addUserGroupRelationFunction($params);
		return $result;
	}
	
	/**
	 * 查询用户关联的应用id列表
	 * @param $user_id
	 */
	public function getUserRelationAppIdList($user_id) {
		$userAppList = D('Resource/UserApp')->getUserRelationAppList($user_id);
		
		$userAppIdList = [];
		if(is_array($userAppList) && count($userAppList) > 0) {
			foreach ($userAppList as $app) {
				$userAppIdList[] = $app['app_id'];
			}
		}
		
		return $userAppIdList;
	}
	
	/**
	 * 查询用户关联的应用列表
	 * @param $user_id
	 */
	public function getUserRelationAppList($user_id) {
		$userAppList = D('Resource/UserApp')->getUserRelationAppList($user_id);		
		return $userAppList;
	}
	
	/**
	 * 删除用户关联的应用
	 * @param int $user_id
	 */
	public function deleteUserRelationApp($user_id) {
		return D('Resource/UserApp')->deleteUserRelationApp($user_id);
	}
	
	/**
	 * 添加用户关联的应用
	 * @param $params
	 */
	public function addUserRelationApp($params) {
		return D('Resource/UserApp')->addUserRelationApp($params);
	}
	
	/**
	 * 查询部门信息
	 * @param array $params
	 */
	public function getDepartmentInfo($params) {
		$departmentInfo = D('Resource/Department')->getDepartmentInfo($params);
		return $departmentInfo;
	}
	
	/**
	 * 查询部门列表
	 * @param array $params
	 */
	public function getDepartmentList($params) {
		$departmentList = D('Resource/Department')->getDepartmentList($params);
		return $departmentList;
	}
	
	/**
	 * 添加部门
	 * @param array $params
	 */
	public function addDepartment($params) {
		$params['create_time'] = time();
		return D('Resource/Department')->addDepartment($params);
	}
	
	/**
	 * 修改部门
	 * @param array $params
	 */
	public function saveDepartment($params) {
		return D('Resource/Department')->saveDepartment($params);
	}
	
	/**
	 * 查询岗位信息
	 * @param array $params
	 */
	public function getPositionInfo($params) {
		$positionInfo = D('Resource/Position')->getPositionInfo($params);
		return $positionInfo;
	}
	
	/**
	 * 查询岗位列表
	 * @param array $params
	 */
	public function getPositionList($params) {
		$positionList = D('Resource/Position')->getPositionList($params);
		return $positionList;
	}
	
	/**
	 * 添加岗位
	 * @param array $params
	 */
	public function addPosition($params) {
		$params['create_time'] = time();
		return D('Resource/Position')->addPosition($params);
	}
	
	/**
	 * 修改岗位
	 * @param array $params
	 */
	public function savePosition($params) {
		return D('Resource/Position')->savePosition($params);
	}

    /**
     * 获取下一个工号
     */
    public function createNextWorkNumber() {
        $max_number = D('Resource/User')->maxWorkNumber();
        $numberTmp = explode('.', $max_number);
        $number = intval($numberTmp[1]);
        $number += 1;

        $code = 'No.';	//修改code
        $len = 6 - strlen($number);
        while($len > 0){
            $code .= '0';
            $len --;
        }
        $code .= $number;
        return $code;
    }
}