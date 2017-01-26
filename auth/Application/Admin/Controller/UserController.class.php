<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;

/**
 * 账号
 * @author Evan <tangzwgo@foxmail.com>
 * @since 2016年7月23日
 */
class UserController extends BaseController {
	/**
	 * 构造函数
	 */
	public function __construct() {
		//无需进行权限检测的功能
		\Org\Auth\AuthUtil::setNoAuthFuncList([]);
		parent::__construct();
	}
	
	/**
	 * 用户列表
	 */
	public function userList() {
		$username = trim(I('get.username'));
		$name = trim(I('get.name'));
		$department_id = intval(I('get.department_id', -1));
		$user_group_id = intval(I('get.user_group_id'));
	
		$data = [];
		$username && $data['username'] = $username;
		$name && $data['name'] = $name;
		if($department_id != -1) {
			$data['department_id'] = $department_id;
		}
		if($_SESSION['_appid_'] != 'jqauth') {
			$data['app_id'] = $_SESSION['_appid_'];
			$data['status'] = 1;
		}
	
	
		if($user_group_id) {
			//查询用户组下的用户列表
			$userList = D('Resource/User', 'Service')->getGroupUserList($user_group_id);
				
			//查询用户组信息
			$userGroupInfo = D('Resource/User', 'Service')->getUserGroupInfo(['id'=>$user_group_id]);
			$this->assign('userGroupInfo', $userGroupInfo);
		} else {
			//查询所有用户
			$userList = D('Resource/User', 'Service')->getUserList($data);
		}
	
		//查询岗位列表
		$positionList = D('Resource/User', 'Service')->getPositionList();
	
		//查询部门列表
		$departmentList = D('Resource/User', 'Service')->getDepartmentList();
	
		//拼装数据
		$userDepartmentList = [];
		if(is_array($userList) && count($userList)>0) {
			foreach($userList as $user) {
				if(isset($departmentList[$user['department_id']])) {
					!isset($userDepartmentList[$user['department_id']]['departmentInfo']) && $userDepartmentList[$user['department_id']]['departmentInfo'] = $departmentList[$user['department_id']];
					$userDepartmentList[$user['department_id']]['userList'][] = $user;
				} else {
					!isset($userDepartmentList[$user['department_id']]['departmentInfo']) && $userDepartmentList[$user['department_id']]['departmentInfo'] = ['id'=>0, 'name'=>'未定义部门'];
					$userDepartmentList[$user['department_id']]['userList'][] = $user;
				}
			}
		}
	
		$this->assign('userDepartmentList', $userDepartmentList);
		$this->assign('userList', $userList);
		$this->assign('departmentList', $departmentList);
		$this->assign('positionList', $positionList);
	
		$this->assign('username', $username);
		$this->assign('name', $name);
		$this->assign('department_id', $department_id);
		$this->assign('user_group_id', $user_group_id);
	
		$this->loadFrame('userList');
	}
	
	/**
	 * 编辑用户信息
	 */
	public function editUserInfo() {
		$id = intval(I('get.id'));
	
		if($id) {
			$userInfo = D('Resource/User', 'Service')->getUserInfo(['id'=>$id]);
			$this->assign('userInfo', $userInfo);
		}
	
		//查询部门列表
		$departmentList = D('Resource/User', 'Service')->getDepartmentList();
	
		//查询岗位列表
		$positionList = D('Resource/User', 'Service')->getPositionList();
	
		$this->assign('departmentList', $departmentList);
		$this->assign('positionList', $positionList);
		$this->loadFrame('editUserInfo');
	}
	
	/**
	 * 保存用户信息
	 */
	public function saveUser() {
		$id = I('post.user_id');
        $name = I('post.name');
        $nickname = I('post.nickname');
        $sex = I('post.sex');
        $wechat = I('post.wechat');
        $qq = I('post.qq');
        $email = I('post.email');
        $mobile = I('post.mobile');
		$username = I('post.username');
		$password = I('post.password');
		$department_id = I('post.department_id');
		$position_id = I('post.position_id');
		$school = I('post.school');
		$address = I('post.address');
		$skill = trim(I('post.skill'),',');
        $desc = I('post.desc');
		$img = I('post.img');

		if(!$username) {
			return Response(2001, '请填写用户名');
		}
		if(!$id && !$password) {
			return Response(2002, '请填写密码');
		}
		if(!$name) {
			return Response(2003, '请填写用户姓名');
		}
	
		//查询用户名是否存在
		$usernameInfo = D('Resource/User', 'Service')->getUserInfo(['username'=>$username]);
		if($usernameInfo && $usernameInfo['id'] != $id) {
			return Response(2004, '用户名已存在');
		}

        $data = [];
        $data['name'] = $name;
        $data['nickname'] = $nickname;
        $data['sex'] = $sex;
        $data['wechat'] = $wechat;
        $data['qq'] = $qq;
        $data['email'] = $email;
        $data['mobile'] = $mobile;
        $data['username'] = $username;
        $password && $data['password'] = md5($password);
        $data['department_id'] = $department_id;
        $data['position_id'] = $position_id;
        $data['school'] = $school;
        $data['address'] = $address;
        $data['skill'] = $skill;
        $data['desc'] = $desc;
        $data['img'] = $img;
        if($id) {
            //查询用户信息
            $userInfo = D('Resource/User', 'Service')->getUserInfo(['id'=>$id]);
            if(!$userInfo) {
                return Response(2005, '用户id错误');
            }

            if(!$userInfo['work_number']) {
                //生成工号
                $work_number = D('Resource/User', 'Service')->createNextWorkNumber();
                $data['work_number'] = $work_number;
            }
            $data['id'] = $id;
            $result = D('Resource/User', 'Service')->saveUser($data);
        } else {
            //生成工号
            $work_number = D('Resource/User', 'Service')->createNextWorkNumber();
            $data['work_number'] = $work_number;
            $result = D('Resource/User', 'Service')->addUser($data);
        }
	
		if($result) {
			return Response(999, '操作成功');
		} else {
			return Response(2006, '操作失败');
		}
	}
	
	/**
	 * 关闭/开启账号
	 */
	public function deleteUser() {
		$user_id = intval(I('post.user_id'));
		$status = intval(I('post.status'));//1开启 -1关闭
	
		if(!$user_id) {
			return Response(2001, '参数错误');
		}
	
		if(!in_array($status, [1, -1])) {
			return Response(2002, '参数错误');
		}
	
		$result = D('Resource/User', 'Service')->saveUser(['id'=>$user_id, 'status'=>$status]);
	
		if($result) {
			return Response(999, '操作成功');
		} else {
			return Response(2003, '操作失败');
		}
	}
	
	/**
	 * 用户功能列表
	 */
	public function userFunctionList() {
		$user_id = I('get.user_id');
	
		//查询用户信息
		$userInfo = D('Resource/User', 'Service')->getUserInfo(['id'=>$user_id]);
		if(!$userInfo) {
			header('Location:/admin/auth/userList');
			exit();
		}
	
		//用户关联的功能
		$userFunctionIdList = D('Resource/User', 'Service')->getUserRelationFunctionIdList($user_id, $_SESSION['_appid_']);
	
		//所有功能列表
		$allFunctionList = D('Resource/Function', 'Service')->getAllFunctionList($_SESSION['_appid_']);
	
		//查询所有功能组列表
		$functionGroupList = D('Resource/Function', 'Service')->getFunctionGroupList(['app_id'=>$_SESSION['_appid_']]);
		if(is_array($allFunctionList) && count($allFunctionList) > 0) {
			foreach ($allFunctionList as $key => $function) {
				in_array($function['id'], $userFunctionIdList) && $function['is_checked'] = 1;
				$functionGroupList[$function['group_id']]['functionList'][] = $function;
			}
		}
	
		$this->assign('userInfo', $userInfo);
		$this->assign('functionGroupList', $functionGroupList);
		$this->loadFrame('userFunctionList');
	}
	
	/**
	 * 设置用户功能
	 */
	public function setUserFunction() {
		$user_id = I('get.user_id');
	
		//查询用户信息
		$userInfo = D('Resource/User', 'Service')->getUserInfo(['id'=>$user_id]);
		if(!$userInfo) {
			header('Location:/admin/auth/userList');
			exit();
		}
	
		//用户关联的用户组列表
		$userGroupIdList = D('Resource/User', 'Service')->getUserRelationUserGroupIdList($user_id, $_SESSION['_appid_']);
	
		//查询所有用户组列表
		$userGroupList = D('Resource/User', 'Service')->getUserGroupList(['app_id'=>$_SESSION['_appid_']]);
		if(is_array($userGroupList) && count($userGroupList) > 0) {
			foreach($userGroupList as $key => $group) {
				in_array($group['id'], $userGroupIdList) && $userGroupList[$key]['is_checked'] = 1;
			}
		}
	
		$this->assign('userInfo', $userInfo);
		$this->assign('userGroupList', $userGroupList);
		$this->loadFrame('setUserFunction');
	}
	
	/**
	 * 保存用户功能
	 */
	public function saveUserFunction() {
		$user_id = I('post.user_id');
		$user_group_ids = I('post.user_group_ids');
	
		if(!$user_id) {
			return Response(2001, '用户ID不能为空');
		}
	
		$userGroupIdList = explode(',', $user_group_ids);
	
		//删除用户关联的用户组
		D('Resource/User', 'Service')->deleteUserRelationGroup($user_id, $_SESSION['_appid_']);
	
		//添加新功能
		if(is_array($userGroupIdList) && count($userGroupIdList) > 0) {
			foreach($userGroupIdList as $user_group_id) {
				D('Resource/User', 'Service')->addUserRelationGroup(['user_group_id'=>$user_group_id, 'user_id'=>$user_id, 'app_id'=>$_SESSION['_appid_']]);
			}
		}
	
		return Response(999, '操作成功');
	}
	
	/**
	 * 用户额外功能设置
	 */
	public function setUserExtFunction() {
		$user_id = I('get.user_id');
	
		//查询用户信息
		$userInfo = D('Resource/User', 'Service')->getUserInfo(['id'=>$user_id]);
	
		//=======================1、用户可访问的系统
		//查询用户已设置的应用列表
		$userAppIdList = D('Resource/User', 'Service')->getUserRelationAppIdList($user_id);
	
		//查询应用列表
		$appList = D('Resource/App', 'Service')->getAppList(['status'=>1, 'is_open'=>3]);
		if(is_array($appList) && count($appList) > 0) {
			foreach ($appList as $key => $app) {
				in_array($app['app_id'], $userAppIdList) && $appList[$key]['is_checked'] = 1;
			}
		}
	
		$this->assign('userInfo', $userInfo);
		$this->assign('appList', $appList);
		$this->loadFrame('setUserExtFunction');
	}
	
	/**
	 * 保存用户关联的app
	 */
	public function saveUserRelationApp() {
		$user_id = I('post.user_id');
		$app_ids = I('post.app_ids');
	
		if(!$user_id) {
			return Response(2001, '用户组ID不能为空');
		}
	
		$appIdList = explode(',', $app_ids);
	
		//删除用户关联的系统
		D('Resource/User', 'Service')->deleteUserRelationApp($user_id);
	
		//添加新应用
		if(is_array($appIdList) && count($appIdList) > 0) {
			foreach($appIdList as $app_id) {
				D('Resource/User', 'Service')->addUserRelationApp(['uid'=>$user_id, 'app_id'=>$app_id]);
			}
		}
	
		return Response(999, '操作成功');
	}
	
	/**
	 * 部门列表
	 */
	public function departmentList() {
		$departmentList = D('Resource/User', 'Service')->getDepartmentList();
	
		$this->assign('departmentList', $departmentList);
		$this->loadFrame('departmentList');
	}
	
	/**
	 * 保存部门信息
	 */
	public function saveDepartment() {
		$id = I('post.id');
		$name = I('post.name');
		if(!$name) {
			return Response(2001, '请填写部门名');
		}
	
		if($id) {
			$result = D('Resource/User', 'Service')->saveDepartment(['id'=>$id, 'name'=>$name]);
		} else {
			$result = D('Resource/User', 'Service')->addDepartment(['name'=>$name]);
		}
	
		if($result) {
			return Response(999, '操作成功');
		} else {
			return Response(2002, '操作失败');
		}
	}
	
	/**
	 * 岗位列表
	 */
	public function positionList() {
		$positionList = D('Resource/User', 'Service')->getPositionList();
	
		$this->assign('positionList', $positionList);
		$this->loadFrame('positionList');
	}
	
	/**
	 * 保存岗位信息
	 */
	public function savePosition() {
		$id = I('post.id');
		$name = I('post.name');
		$code = strtoupper(I('post.code'));
		if(!$name) {
			return Response(2001, '请填写岗位名');
		}
	
		if(!$code) {
			return Response(2002, '请填写岗位编号');
		}
	
		//判断岗位编号是否重复
		$positionInfo = D('Resource/User', 'Service')->getPositionInfo(['code'=>$code]);
		if($positionInfo && $positionInfo['id'] != $id) {
			return Response(2003, '岗位编号不能重复');
		}
	
		if($id) {
			$result = D('Resource/User', 'Service')->savePosition(['id'=>$id, 'name'=>$name, 'code'=>$code]);
		} else {
			$result = D('Resource/User', 'Service')->addPosition(['name'=>$name, 'code'=>$code]);
		}
	
		if($result) {
			return Response(999, '操作成功');
		} else {
			return Response(2002, '操作失败');
		}
	}
}