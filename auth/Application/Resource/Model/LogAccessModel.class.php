<?php
namespace Resource\Model;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-访问日志
 * @author Evan
 * @since 2016年5月11日
 */
class LogAccessModel extends Model {
	protected $trueTableName = AuthTable::TB_LOG_AUTH_ACCESS;
	
	/**
	 * 添加用户访问日志
	 * @param unknown $params
	 */
	public function addAccessLog($params) {
		return $this->add($params);
	}

    /**
     * 查询用户访问日志数量
     * @param unknown $params
     */
    public function getUserAccessLogCount($params) {
        $num = $this->where($params)->count();
        return intval($num);
    }

    /**
     * 查询用户访问日志列表
     * @param unknown $params
     */
    public function getUserAccessLogList($params, $page, $page_size) {
        $logList = $this->where($params)->order('id DESC')->limit($page_size)->page($page)->select();
        return $logList;
    }
}