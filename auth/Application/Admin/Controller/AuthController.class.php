<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;

/**
 * 权限管理
 * @author Evan
 * @since 2016年5月12日
 */
class AuthController extends BaseController {
	/**
	 * 构造函数
	 */
	public function __construct() {
		//无需进行权限检测的功能
		\Org\Auth\AuthUtil::setNoAuthFuncList([
            '/admin/auth/ajaxFunctionList',
		]);
		parent::__construct();
	}
	
	/**
	 * 用户组列表
	 */
	public function userGroupList() {
		$groupList = D('Resource/User', 'Service')->getUserGroupList(['app_id'=>$_SESSION['_appid_']]);
		
		$this->assign('groupList', $groupList);
		$this->loadFrame('userGroupList');
	}
	
	/**
	 * 保存用户组信息
	 */
	public function saveUserGroup() {
		$id = I('post.id');
		$name = I('post.name');
		if(!$name) {
			return Response(2001, '请填写用户组名');
		}
		
		if($id) {
			$result = D('Resource/User', 'Service')->saveUserGroup(['id'=>$id, 'name'=>$name]);
		} else {
			$result = D('Resource/User', 'Service')->addUserGroup(['app_id'=>$_SESSION['_appid_'], 'name'=>$name]);
		}
		
		if($result) {
			return Response(999, '操作成功');
		} else {
			return Response(2002, '操作失败');
		}
	}
	
	/**
	 * 保存用户组功能
	 */
	public function saveUserGroupFunction() {
		$user_group_id = I('post.user_group_id');
		$function_ids = I('post.function_ids');
		
		if(!$user_group_id) {
			return Response(2001, '用户组ID不能为空');
		}
		
		$functionIdList = explode(',', $function_ids);
		
		//删除用户组关联功能
		D('Resource/User', 'Service')->deleteUserGroupRelationFunction($user_group_id, $_SESSION['_appid_']);
		
		//添加新功能
		if(is_array($functionIdList) && count($functionIdList) > 0) {
			foreach($functionIdList as $function_id) {
				D('Resource/User', 'Service')->addUserGroupRelationFunction(['user_group_id'=>$user_group_id, 'function_id'=>$function_id]);
			}
		}
		
		return Response(999, '操作成功');
	}
	
	/**
	 * 设置用户组功能
	 */
	public function setUserGroupFunction() {
		$user_group_id = I('get.user_group_id');
		
		//查询用户组信息
		$userGroupInfo = D('Resource/User', 'Service')->getUserGroupInfo(['id'=>$user_group_id, 'app_id'=>$_SESSION['_appid_']]);
		if(!$userGroupInfo) {
			header('Location:/admin/auth/userGroupList');
			exit();
		}
		
		//查询用户组关联的功能
		$userGroupFunctionIdList = D('Resource/User', 'Service')->getUserGroupRelationFunctionIdList($user_group_id);
		
		//所有功能列表
		$allFunctionList = D('Resource/Function', 'Service')->getAllFunctionList($_SESSION['_appid_']);
		
		//查询所有功能组列表
		$functionGroupList = D('Resource/Function', 'Service')->getFunctionGroupList(['app_id'=>$_SESSION['_appid_']]);
		if(is_array($allFunctionList) && count($allFunctionList) > 0) {
			foreach ($allFunctionList as $key => $function) {
				in_array($function['id'], $userGroupFunctionIdList) && $function['is_checked'] = 1;
				$functionGroupList[$function['group_id']]['functionList'][] = $function;
			}
		}
		
		$this->assign('userGroupInfo', $userGroupInfo);
		$this->assign('functionGroupList', $functionGroupList);
		$this->loadFrame('setUserGroupFunction');
	}
	
	/**
	 * 功能组列表
	 */
	public function functionGroupList() {
		$groupList = D('Resource/Function', 'Service')->getFunctionGroupList(['app_id'=>$_SESSION['_appid_']]);
		
		$this->assign('groupList', $groupList);
		$this->loadFrame('functionGroupList');
	}
	
	/**
	 * 保存功能组信息
	 */
	public function saveFunctionGroup() {
		$id = I('post.id');
		$name = I('post.name');
		if(!$name) {
			return Response(2001, '请填写功能组名');
		}
		
		if($id) {
			$result = D('Resource/Function', 'Service')->saveFunctionGroup(['id'=>$id, 'name'=>$name]);
		} else {
			$result = D('Resource/Function', 'Service')->addFunctionGroup(['app_id'=>$_SESSION['_appid_'], 'name'=>$name]);
		}
		
		if($result) {
			return Response(999, '操作成功');
		} else {
			return Response(2002, '操作失败');
		}
	}
	
	/**
	 * 功能列表
	 */
	public function functionList() {
		//查询功能组列表
		$functionGroupList = D('Resource/Function', 'Service')->getFunctionGroupList(['app_id'=>$_SESSION['_appid_']]);
		
		//查询功能列表
		$functionList = D('Resource/Function', 'Service')->getAllFunctionList($_SESSION['_appid_']);
		
		//拼装数据
		if(is_array($functionList) && count($functionList)>0) {
			foreach($functionList as $function) {
				$functionGroupList[$function['group_id']]['functionList'][] = $function;
			}
		}
		
		$this->assign('functionGroupList', $functionGroupList);
		$this->loadFrame('functionList');
	}

    /**
     * 加载功能组下的功能列表
     */
	public function ajaxFunctionList() {
        $app_id = $_SESSION['_appid_'];
        $function_group_id = I('post.function_group_id');

        $functionList = D('Resource/Function', 'Service')->getFunctionList(['app_id'=>$app_id, 'group_id'=>$function_group_id]);
        !$functionList && $functionList = [];
        return Response(999, '获取数据成功', ['functionList'=>$functionList]);
    }
	
	/**
	 * 保存功能信息
	 */
	public function saveFunction() {
		$id = I('post.id');
		$name = I('post.name');
		$path = I('post.path');
		$group_id = I('post.group_id');
		if(!$name) {
			return Response(2001, '请填写功能名');
		}
		
		if(!$path) {
			return Response(2002, '请填写URL名');
		}
		
		if(!$group_id) {
			return Response(2003, '请选择所属功能组');
		}
		
		//查询URL是否存在
		$functionInfo = D('Resource/Function', 'Service')->getFunctionInfo(['path'=>$path]);
		if($functionInfo && $functionInfo['id'] != $id && $_SESSION['_appid_'] == $functionInfo['app_id']) {
			return Response(2004, 'URL已存在');
		}
	
		if($id) {
			$result = D('Resource/Function', 'Service')->saveFunction(['id'=>$id, 'name'=>$name, 'path'=>$path, 'group_id'=>$group_id]);
		} else {
			$result = D('Resource/Function', 'Service')->addFunction(['app_id'=>$_SESSION['_appid_'], 'name'=>$name, 'path'=>$path, 'group_id'=>$group_id]);
		}
	
		if($result) {
			return Response(999, '操作成功');
		} else {
			return Response(2005, '操作失败');
		}
	}
	
	/**
	 * 菜单列表
	 */
	public function menuList() {
		//查询菜单列表
		$menuList = D('Resource/Menu', 'Service')->getMenuList($_SESSION['_appid_']);

		//查询所有功能列表
		$functionList = D('Resource/Function', 'Service')->getAllFunctionList($_SESSION['_appid_']);
		
		$this->assign('menuList', $menuList);
		$this->assign('functionList', $functionList);
		$this->loadFrame('menuList');
	}
	
	/**
	 * 添加菜单
	 */
	public function addMenu() {
		$pid = intval(I('post.pid'));//父菜单id
		$preid = intval(I('post.preid'));//当前菜单id
		$menu_name = trim(I('post.menu_name'));//目录名
		$function_id = intval(I('post.func_id'));//功能id
		$url_extend = trim(I('post.url_extend'));//扩展url
		$icon = trim(I('post.icon'));//图标
		
		if ($pid <= 0) {
			//$function_id = 0;//一级菜单，功能id为0
			!$icon && $icon = 'fa fa-link';
		}
		if ($menu_name == '') {
			return Response(2001, '目录名不能为空');
		}
		if ($pid > 0 && $function_id <= 0) {
			return Response(2002, '二级目录必须填写功能ID');
		}
		
		$data = [];
		$data['pid'] = $pid;
		$data['preid'] = $preid;
		$data['menu_name'] = $menu_name;
		$data['function_id'] = $function_id;
		$data['url_extend'] = $url_extend;
		$data['icon'] = $icon;
		D('Menu', 'Logic')->addMenu($data);
	}
	
	/**
	 * 修改菜单
	 */
	public function updateMenu() {
		$id = intval(I('post.id'));//菜单id
		$menu_name = trim(I('post.menu_name'));//目录名
		$function_id = intval(I('post.func_id'));//功能id
		$url_extend = trim(I('post.url_extend'));//扩展url
		$sort_id = intval(I('post.sort_id'));//排序
		$icon = trim(I('post.icon'));//图标
		
		$menuInfo = D('Resource/Menu', 'Service')->getMenuInfo(['id'=>$id, 'app_id'=>$_SESSION['_appid_']]);
		
		if (!$menuInfo) {
			return Response(2001, "目录ID:{$id}不存在");
		}
		if ($menu_name == '') {
			return Response(2002, '目录名不能为空');
		}
		if ($menuInfo['pid'] > 0 && $function_id <= 0) {
			return Response(2003, '二级目录必须填写功能ID');
		}
		if ($menuInfo['pid'] == 0) {
			//$function_id = 0; //一级目录路径为0
			!$icon && $icon = 'fa fa-link';
		}
		
		if ($menuInfo['pid'] > 0 || $function_id > 0) {
			//判断func_id是否存在
			$functionInfo = D('Resource/Function', 'Service')->getFunctionInfo(['id'=>$function_id]);
			if (!$functionInfo) {
				return Response(2004, "功能ID:{$function_id}不存在");
			}
		}
		
		if($menuInfo['pid'] == 0 && $function_id > 0) {
			//父菜单设置功能，判断是否有子菜单，如果有子菜单则不允许设置功能
			$childMenuList = D('Resource/Menu', 'Service')->getChildMenuList($id, $_SESSION['_appid_']);
			if($childMenuList) {
				return Response(2005, "该菜单下包含子菜单，不允许设置功能");
			}
		}
		
		$data = [];
		$data['id'] = $id;
		$data['name'] = $menu_name;
		$data['sort_id'] = $sort_id;
		$data['function_id'] = $function_id;
		$data['url_extend'] = $url_extend;
		$data['icon'] = $icon;
		$result = D('Resource/Menu', 'Service')->updateMenu($data);
		
		if($result) {
			return Response(999, '操作成功');
		} else {
			return Response(2006, '操作失败');
		}
	}
	
	/**
	 * 删除菜单
	 */
	public function deleteMenu() {
		$id = intval(I('post.id'));//菜单id
		
		$menuInfo = D('Resource/Menu', 'Service')->getMenuInfo(['id'=>$id, 'app_id'=>$_SESSION['_appid_']]);		
		if (!$menuInfo) {
			return Response(2001, "目录ID:{$id}不存在");
		}
		
		if ($menuInfo['pid'] == 0) {
			//一级目录，检查是否有二级目录
			$childMenuList = D('Resource/Menu', 'Service')->getChildMenuList($id, $_SESSION['_appid_']);			
			if (is_array($childMenuList) && count($childMenuList) > 0) {
				return Response(2002, '请先删除二级目录后再操作');
			}
		}
		
		$result = D('Resource/Menu', 'Service')->deleteMenu($id);
		
		if($result) {
			return Response(999, '操作成功');
		} else {
			return Response(2003, '操作失败');
		}
	}		
}