<?php
namespace Resource\Service;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-菜单
 * @author Evan
 * @since 2016年5月11日
 */
class MenuService extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_MENU;
	
	/**
	 * 查询菜单列表
	 * @param string $app_id
	 */
	public function getMenuList($app_id) {
		$menuList = [];
		//查询一级菜单
		$parentMenuList = D('Resource/Menu')->getMenuList(['pid'=>0, 'app_id'=>$app_id]);
		if(is_array($parentMenuList) && count($parentMenuList) > 0) {
			foreach ($parentMenuList as $menu) {
				$menu_id = $menu['id'];
				$menuList[$menu_id]['info'] = $menu;
				
				//查询子菜单
				$childMenuList = D('Resource/Menu')->getMenuList(['pid'=>$menu_id, 'app_id'=>$app_id]);
				$child = [];
				if(is_array($childMenuList) && count($childMenuList) > 0) {
					foreach($childMenuList as $childMenu) {
						$child[$childMenu['id']]['info'] = $childMenu;
					}
				}
				$menuList[$menu_id]['next'] = $child;
			}
		}
		return $menuList;
	}
	
	/**
	 * 查询子菜单列表
	 * @param int $pid
	 */
	public function getChildMenuList($pid, $app_id) {
		$childMenuList = D('Resource/Menu')->getMenuList(['pid'=>$pid, 'app_id'=>$app_id]);
		return $childMenuList;
	}
	
	/**
	 * 添加菜单
	 * @param array $params
	 */
	public function addMenu($params) {
		$params['create_time'] = time();
		$menu_id = D('Resource/Menu')->addMenu($params);
		return $menu_id;
	}

	/**
	 * 查询菜单信息
	 * @param array $params
	 */
	public function getMenuInfo($params) {
		$menuInfo = D('Resource/Menu')->getMenuInfo($params);
		return $menuInfo;
	}
	
	/**
	 * 修改菜单信息
	 * @param array $params
	 */
	public function updateMenu($params) {
		return D('Resource/Menu')->updateMenu($params);
	}
	
	/**
	 * 删除菜单
	 * @param unknown $params
	 */
	public function deleteMenu($menu_id) {
		return D('Resource/Menu')->deleteMenu($menu_id);
	}
}