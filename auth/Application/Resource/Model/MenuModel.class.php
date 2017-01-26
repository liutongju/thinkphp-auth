<?php
namespace Resource\Model;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-菜单
 * @author Evan
 * @since 2016年5月11日
 */
class MenuModel extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_MENU;
	
	/**
	 * 查询菜单列表
	 * @param array $params
	 */
	public function getMenuList($params) {
		$menuList = $this->where($params)->order('sort_id ASC')->select();
		return $menuList;
	}
	
	/**
	 * 添加菜单
	 * @param array $params
	 */
	public function addMenu($params) {
		return $this->add($params);
	}

	/**
	 * 查询菜单信息
	 * @param array $params
	 */
	public function getMenuInfo($params) {
		$menuInfo = $this->where($params)->find();
		return $menuInfo;
	}

	/**
	 * 修改菜单信息
	 * @param array $params
	 */
	public function updateMenu($params) {
		$result = $this->save($params);
		if($result !== false) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 删除菜单信息
	 * @param array $menu_id
	 */
	public function deleteMenu($menu_id) {
		$result = $this->where("id='{$menu_id}'")->delete();
		if($result !== false) {
			return true;
		} else {
			return false;
		}
	}
}