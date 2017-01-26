<?php
namespace Resource\Service;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-功能
 * @author Evan
 * @since 2016年5月11日
 */
class FunctionService extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_FUNCTION;
	
	/**
	 * 查询功能列表
	 * @param string $app_id
	 */
	public function getAllFunctionList($app_id) {
		$functionList = D('Resource/Function')->getAllFunctionList($app_id);
		return $functionList;
	}

    /**
     * 查询功能列表
     * @param $params
     * @return mixed
     */
	public function getFunctionList($params) {
        $functionList = D('Resource/Function')->getFunctionList($params);
        return $functionList;
    }
	
	/**
	 * 查询功能信息
	 * @param string $params
	 */
	public function getFunctionInfo($params) {
		$functionInfo = D('Resource/Function')->getFunctionInfo($params);
		return $functionInfo;
	}
	
	/**
	 * 添加功能
	 * @param array $params
	 */
	public function addFunction($params) {
		$params['create_time'] = time();
		return D('Resource/Function')->addFunction($params);
	}
	
	/**
	 * 修改功能
	 * @param array $params
	 */
	public function saveFunction($params) {
		return D('Resource/Function')->saveFunction($params);
	}
	
	/**
	 * 查询功能组信息
	 * @param array $params
	 */
	public function getFunctionGroupInfo($params) {
		$groupInfo = D('Resource/FunctionGroup')->getFunctionGroupInfo($params);
		return $groupInfo;
	}
	
	/**
	 * 查询功能组列表
	 * @param array $params
	 */
	public function getFunctionGroupList($params) {
		$groupList = D('Resource/FunctionGroup')->getFunctionGroupList($params);		
		return $groupList;
	}
	
	/**
	 * 添加功能组
	 * @param array $params
	 */
	public function addFunctionGroup($params) {
		$params['create_time'] = time();
		return D('Resource/FunctionGroup')->addFunctionGroup($params);
	}
	
	/**
	 * 修改功能组
	 * @param array $params
	 */
	public function saveFunctionGroup($params) {
		return D('Resource/FunctionGroup')->saveFunctionGroup($params);
	}
}