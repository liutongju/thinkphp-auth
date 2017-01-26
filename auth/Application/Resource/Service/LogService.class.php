<?php
namespace Resource\Service;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-日志
 * @author Evan
 * @since 2016年5月11日
 */
class LogService extends Model {
	protected $trueTableName = AuthTable::TB_LOG_AUTH_LOGIN;

	/**
	 * 添加登录日志
	 * @param array $params
	 */
	public function addLoginLog($params) {
		$params['create_time'] = time();
		return D('Resource/LogLogin')->addLoginLog($params);
	}
	
	/**
	 * 添加用户访问日志
	 * @param array $params
	 */
	public function addAccessLog($params) {
		$params['create_time'] = time();
		return D('Resource/LogAccess')->addAccessLog($params);
	}
	
	/**
	 * 查询用户登录日志列表
	 * @param $params
	 */
	public function getUserLoginLog($params) {
		$page = 1;
		$page_size = 20;
		isset($params['page']) && $page = $params['page'];
		isset($params['page_size']) && $page_size = $params['page_size'];
		
		$data = [];
		isset($params['app_id']) && $data['app_id'] = $params['app_id'];
		isset($params['user_id']) && $data['user_id'] = $params['user_id'];

        $createTime = [];
        isset($params['start_time']) && $createTime[] = ['EGT', $params['start_time']];
        isset($params['end_time']) && $createTime[] = ['ELT', $params['end_time']];
        $createTime && $data['create_time'] = $createTime;
		
		//查询日志总数
		$total = D('Resource/LogLogin')->getUserLoginLogCount($data);
		
		//计算总页数
		$total_page = ceil($total / $page_size);
		if($page < 1) {
			$page = 1;
		}
		if($page > $total_page) {
			$page = $total_page;
		}
		
		//查询日志列表
		$logList = D('Resource/LogLogin')->getUserLoginLogList($data, $page, $page_size);
		if(is_array($logList) && count($logList) > 0) {
			foreach($logList as $key => $log) {
				//查询用户姓名
				$name = D('Resource/User', 'Service')->getUserNameById($log['user_id']);
				$logList[$key]['name'] = $name;
			}
		}
		
		$result = [];
		$result['page'] = $page;
		$result['page_size'] = $page_size;
		$result['total'] = $total;
		$result['logList'] = $logList;
		return $result;
	}

    /**
     * 查询用户访问日志列表
     * @param $params
     */
    public function getUserAccessLog($params) {
        $page = 1;
        $page_size = 20;
        isset($params['page']) && $page = $params['page'];
        isset($params['page_size']) && $page_size = $params['page_size'];

        $data = [];
        isset($params['app_id']) && $data['app_id'] = $params['app_id'];
        isset($params['user_id']) && $data['user_id'] = $params['user_id'];
        isset($params['function_group_id']) && $data['function_group_id'] = $params['function_group_id'];
        isset($params['function_id']) && $data['function_id'] = $params['function_id'];

        $createTime = [];
        isset($params['start_time']) && $createTime[] = ['EGT', $params['start_time']];
        isset($params['end_time']) && $createTime[] = ['ELT', $params['end_time']];
        $createTime && $data['create_time'] = $createTime;

        //查询日志总数
        $total = D('Resource/LogAccess')->getUserAccessLogCount($data);

        //计算总页数
        $total_page = ceil($total / $page_size);
        if($page < 1) {
            $page = 1;
        }
        if($page > $total_page) {
            $page = $total_page;
        }

        //查询日志列表
        $logList = D('Resource/LogAccess')->getUserAccessLogList($data, $page, $page_size);
        if(is_array($logList) && count($logList) > 0) {
            foreach($logList as $key => $log) {
                //查询用户姓名
                $name = D('Resource/User', 'Service')->getUserNameById($log['user_id']);
                $logList[$key]['name'] = $name;

                //功能组名称
                $functionGroupInfo = D('Resource/Function', 'Service')->getFunctionGroupInfo(['id'=>$log['function_group_id']]);
                $logList[$key]['function_group_name'] = $functionGroupInfo['name'];

                //功能名称
                $functionInfo = D('Resource/Function', 'Service')->getFunctionInfo(['id'=>$log['function_id']]);
                $logList[$key]['function_name'] = $functionInfo['name'];
            }
        }

        $result = [];
        $result['page'] = $page;
        $result['page_size'] = $page_size;
        $result['total'] = $total;
        $result['logList'] = $logList;
        return $result;
    }
}