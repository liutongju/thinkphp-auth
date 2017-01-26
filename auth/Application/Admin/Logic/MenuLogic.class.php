<?php
namespace Admin\Logic;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 菜单
 * @author Evan
 * @since 2016年5月12日
 */
class MenuLogic extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_MENU;
	
	/**
	 * 添加菜单
	 * @param array $params
	 */
	public function addMenu($params) {
		$pid = $params['pid'];//父菜单id
		$preid = $params['preid'];//当前菜单id
		$menu_name = $params['menu_name'];//目录名
		$function_id = $params['function_id'];//功能id
		$url_extend = $params['url_extend'];//扩展url
		$icon = $params['icon'];//图标
		
		$sort_id = 100000;
		if ($function_id > 0) {
			//判断$function_id是否存在
			$functionInfo = D('Resource/Function', 'Service')->getFunctionInfo(['id'=>$function_id]);
			if (!$functionInfo) {
				return Response('2003', "功能ID:{$function_id}不存在");
			}
		}
		
		if($pid > 0) {
			//如果pid大于0 ，判断该pid是否设置功能，如果设置了功能则取消
			$menuInfo = D('Resource/Menu', 'Service')->getMenuInfo(['id'=>$pid]);
			if(!$menuInfo) {
				return Response('2004', "父菜单:{$function_id}不存在");
			}
			
			if($menuInfo['function_id'] > 0) {
				D('Resource/Menu', 'Service')->updateMenu(['id'=>$pid, 'function_id'=>0, 'url_extend'=>'']);
			}
		}
		
		$childMenuList = D('Resource/Menu', 'Service')->getChildMenuList($pid, $_SESSION['_appid_']);
		
		//处理排序
		if ($preid == 0) {
			//加到主目录下的第一个
			if ($pid > 0) {
				//二级目录
				$min = 1;
				$max = 100000;
				if (is_array($childMenuList) && count($childMenuList) > 0) {
					foreach ($childMenuList as $menu) {
						$max = $menu['sort_id'];
						break;
					}
				}
				$sort_id = intval(($min + $max) / 2);
			} else {
				//添加1级目录，永远是排在最后,如果要调整，手动调整
				$max = 100000;
				if (is_array($childMenuList) && count($childMenuList) > 0) {
					foreach ($childMenuList as $menu) {
						$max = $menu['sort_id'];
					}
				}
				$sort_id = $max + 100000;
			}
		} else {
			//将目录加在当前目录下、这时pid肯定大于0
			$min = 0;
			$max = 0;
			if (is_array($childMenuList) && count($childMenuList) > 0) {
				foreach ($childMenuList as $menu) {
					if ($min > 0) {
						$max = $menu['sort_id']; //下一个
						break; //全部找到退出
					}
					if ($preid == $menu['id']) {
						$min = $menu['sort_id']; //找到当前最小值
					}
				}
			}
			$max <= $min && $max = $min + 200000;
			$sort_id = intval(($min + $max) / 2);
		}
		
		$data = [];
		$data['pid'] = $pid;
		$data['name'] = $menu_name;
		$data['sort_id'] = $sort_id;
		$data['function_id'] = $function_id;
		$data['url_extend'] = $url_extend;
		$data['icon'] = $icon;
		$data['app_id'] = $_SESSION['_appid_'];
		D('Resource/Menu', 'Service')->addMenu($data);
		
		return Response('999', '添加成功');
	}
}