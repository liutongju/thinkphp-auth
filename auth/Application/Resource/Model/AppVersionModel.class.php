<?php
namespace Resource\Model;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-应用版本
 * @author Evan
 * @since 2016年5月11日
 */
class AppVersionModel extends Model {
	protected $trueTableName = AuthTable::TB_APP_VERSION;
	
	/**
	 * 查询应用版本列表
	 * @param array $params
	 */
	public function getAppVersionList($params) {
		$versionList = $this->where($params)->order('id DESC')->select();
		return $versionList;
	}

    /**
     * 查询应用版本信息
     * @param array $params
     */
    public function getAppVersionInfo($params) {
        $versionInfo = $this->where($params)->find();
        return $versionInfo;
    }
	
	/**
	 * 添加应用版本
	 * @param $params
	 */
	public function addAppVersion($params) {
		return $this->add($params);
	}
	
	/**
	 * 修改应用版本信息
	 * @param $params
	 */
	public function updateAppVersionInfo($params) {
		$result = $this->save($params);
		if($result !== false) {
			return true;
		} else {
			return false;
		}
	}
}