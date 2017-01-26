<?php
namespace Resource\Service;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-应用
 * @author Evan
 * @since 2016年5月11日
 */
class AppService extends Model {
	protected $trueTableName = AuthTable::TB_APP;
	
	/**
	 * 查询应用列表
	 * @param $params
	 */
	public function getAppList($params) {
		$appList = D('Resource/App')->getAppList($params);
		return $appList;
	}
	
	/**
	 * 查询应用信息
	 * @param $params
	 */
	public function getAppInfo($params) {
		$appInfo = D('Resource/App')->getAppInfo($params);
		return $appInfo;
	}
	
	/**
	 * 添加应用
	 * @param $params
	 */
	public function addApp($params) {
		$params['status'] = 1;
		$params['create_time'] = time();
		return D('Resource/App')->addApp($params);
	}
	
	/**
	 * 更新应用信息
	 * @param $params
	 */
	public function updateAppInfo($params) {
		return D('Resource/App')->updateAppInfo($params);
	}

    /**
     * 查询app版本列表
     * @param $params
     */
	public function getAppVersionList($params) {
        $versionList = D('Resource/AppVersion')->getAppVersionList($params);
        if(is_array($versionList) && count($versionList) > 0) {
            foreach($versionList as $key => $version) {
                $versionList[$key]['package_url'] = load_static($version['package']);

                $versionList[$key]['publish_time'] = date('Y-m-d', $version['publish_time']);
            }
        }
        return $versionList;
    }

    /**
     * 查询版本信息
     * @param $params
     */
    public function getAppVersionInfo($params) {
        $versionInfo = D('Resource/AppVersion')->getAppVersionInfo($params);
        return $versionInfo;
    }

    /**
     * 更新应用版本
     * @param $params
     */
    public function updateAppVersion($params) {
	    return D('Resource/AppVersion')->updateAppVersionInfo($params);
    }

    /**
     * 添加应用版本
     * @param $params
     */
    public function addAppVersion($params) {
        $params['status'] = 1;
        $params['create_time'] = time();
        return D('Resource/AppVersion')->addAppVersion($params);
    }
}