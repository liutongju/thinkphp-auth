<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;

/**
 * 日志管理
 * @author Evan <tangzwgo@foxmail.com>
 * @since 2016年5月27日
 */
class LogController extends BaseController {
	/**
	 * 构造函数
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
     * 用户登录日志
     */
    public function loginLog(){
        $user_id = I('get.user_id');
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');
        
        //分页
        $page = I('get.page', 1);
        $pagesize = 20;
        
        $params = [];
        $params['page'] = $page;
        $params['page_size'] = $pagesize;
        $params['app_id'] = $_SESSION['_appid_'];
        $user_id && $params['user_id'] = $user_id;
        $start_time && $params['start_time'] = strtotime($start_time.' 00:00:00');
        $end_time && $params['end_time'] = strtotime($end_time.' 23:59:59');
        
        $result = D('Resource/Log', 'Service')->getUserLoginLog($params);
        
        $page = $result['page'];
		$page_size = $result['page_size'];
		$total = $result['total'];
		$logList = $result['logList'];
        
        //分页
        $url = '/log/loginLog?user_id='.$user_id.'&start_time='.$start_time.'&end_time='.$end_time;
        $page_html = create_page($url, $total, $page, $page_size);
        
        //后台用户列表
        $userList = D('Resource/User', 'Service')->getUserList(['status'=>1]);
        
        $this->assign('logList',$logList);
        $this->assign('userList',$userList);
        
        $this->assign('page_size', $page_size);
        $this->assign('total', $total);
        $this->assign('page_html', $page_html);
        
        $this->assign('user_id',$user_id);
        $this->assign('start_time',$start_time);
        $this->assign('end_time',$end_time);
        
        $this->loadFrame('loginLog');
    }

    /**
     * 用户访问日志
     */
    public function accessLog() {
        $app_id = $_SESSION['_appid_'];

        $user_id = I('get.user_id');
        $function_group_id = I('get.function_group_id');
        $function_id = I('get.function_id');
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');

        //分页
        $page = I('get.page', 1);
        $pagesize = 20;

        $params = [];
        $params['page'] = $page;
        $params['page_size'] = $pagesize;
        $params['app_id'] = $app_id;
        $user_id && $params['user_id'] = $user_id;
        $function_group_id && $params['function_group_id'] = $function_group_id;
        $function_id && $params['function_id'] = $function_id;
        $start_time && $params['start_time'] = strtotime($start_time.' 00:00:00');
        $end_time && $params['end_time'] = strtotime($end_time.' 23:59:59');

        $result = D('Resource/Log', 'Service')->getUserAccessLog($params);

        $page = $result['page'];
        $page_size = $result['page_size'];
        $total = $result['total'];
        $logList = $result['logList'];

        //分页
        $url = "/log/accessLog?user_id={$user_id}&start_time={$start_time}&end_time={$end_time}";
        $page_html = create_page($url, $total, $page, $page_size);

        //后台用户列表
        $userList = D('Resource/User', 'Service')->getUserList(['status'=>1]);

        //功能组列表
        $functionGroupList = D('Resource/Function', 'Service')->getFunctionGroupList(['app_id'=>$app_id]);

        if($function_group_id) {
            //功能组下的功能列表
            $functionList = D('Resource/Function', 'Service')->getFunctionList(['app_id'=>$app_id, 'group_id'=>$function_group_id]);
            $this->assign('functionList',$functionList);
        }

        $this->assign('logList',$logList);
        $this->assign('userList',$userList);
        $this->assign('functionGroupList',$functionGroupList);

        $this->assign('page_size', $page_size);
        $this->assign('total', $total);
        $this->assign('page_html', $page_html);

        $this->assign('user_id',$user_id);
        $this->assign('function_group_id',$function_group_id);
        $this->assign('function_id',$function_id);
        $this->assign('start_time',$start_time);
        $this->assign('end_time',$end_time);

        $this->loadFrame('accessLog');
    }
}