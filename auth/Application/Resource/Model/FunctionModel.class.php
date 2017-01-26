<?php
namespace Resource\Model;
use Think\Model;
use Common\ORG\Util\AuthTable;

/**
 * 权限系统-功能
 * @author Evan
 * @since 2016年5月11日
 */
class FunctionModel extends Model {
	protected $trueTableName = AuthTable::TB_AUTH_FUNCTION;
	
	/**
	 * 查询所有功能列表
	 * @param string $app_id
	 */
	public function getAllFunctionList($app_id) {
		$functionList = $this->where("app_id='{$app_id}'")->getField('id,path,name,group_id,app_id,create_time', true);
		return $functionList;
	}

    /**
     * 查询功能列表
     * @param $params
     */
	public function getFunctionList($params) {
        $functionList = $this->where($params)->select();
        return $functionList;
    }
	
	/**
	 * 查询功能信息
	 * @param array $params
	 */
	public function getFunctionInfo($params) {
		$functionInfo = $this->where($params)->find();
		return $functionInfo;
	}
	
	/**
	 * 添加功能
	 * @param array $params
	 */
	public function addFunction($params) {
		$function_id = $this->add($params);
		return $function_id;
	}
	
	/**
	 * 修改功能
	 * @param array $params
	 */
	public function saveFunction($params) {
		$result = $this->save($params);
		if($result !== false) {
			return true;
		} else {
			return false;
		}
	}
}