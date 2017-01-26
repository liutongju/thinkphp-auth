<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;

/**
 * 应用
 * @author Evan <tangzwgo@foxmail.com>
 * @since 2016年7月24日
 */
class AppController extends BaseController {
	/**
	 * 构造函数
	 */
	public function __construct() {
		//无需进行权限检测的功能
		\Org\Auth\AuthUtil::setNoAuthFuncList([
		]);
		parent::__construct();
	}
	
	/**
	 * 应用列表
	 */
	public function appList() {
		//查询应用列表
		$appList = D('Resource/App', 'Service')->getAppList();				
		
		$this->assign('appList', $appList);
		
		$this->loadFrame('appList');
	}
	
	/**
	 * 编辑应用
	 */
	public function editApp() {
		$id = intval(I('get.id'));
		
		if($id) {
			$appInfo = D('Resource/App', 'Service')->getAppInfo(['id'=>$id]);
			$this->assign('appInfo', $appInfo);
		}
		
		$this->loadFrame('editApp');
	}
	
	/**
	 * 保存应用信息
	 */
	public function saveApp() {
		$id = I('post.id');
		$name = I('post.name');
		$app_id = I('post.app_id');
		$app_secret = I('post.app_secret');
		$token = I('post.token');
		$encoding_AESKey = I('post.encoding_AESKey');
		$is_encryption = I('post.is_encryption');
		$type = I('post.type');
		$is_auth = I('post.is_auth');
		$domain = I('post.domain');
		$ip_list = I('post.ip_list');
		if(!$app_id) {
			return Response(2001, '请填写应用ID');
		}
		if(!$name) {
			return Response(2002, '请填写应用名称');
		}
		if(!$token) {
			return Response(2002, '请填写应用令牌');
		}
		
		//查询应用ID是否存在
		$appInfo = D('Resource/App', 'Service')->getAppInfo(['app_id'=>$app_id,'status'=>1]);
		if($appInfo && $appInfo['id'] != $id) {
			return Response(2004, '应用ID已存在');
		}
		
		if($id) {
			$params = array();
			$params['id'] = $id;
            $params['name'] = $name;
            $params['app_id'] = $app_id;            
            $params['app_secret'] = $app_secret;
            $params['token'] = $token;
            $params['encoding_AESKey'] = $encoding_AESKey;
            $params['is_encryption'] = $is_encryption;
            $params['type'] = $type;
            $params['is_auth'] = $is_auth;
            $params['domain'] = $domain;
            $params['ip_list'] = $ip_list;
		
			$result = D('Resource/App', 'Service')->updateAppInfo($params);
		} else {
			//添加
			$params = array();
			$params['id'] = $id;
            $params['name'] = $name;
            $params['app_id'] = $app_id;            
            $params['app_secret'] = $app_secret;
            $params['token'] = $token;
            $params['encoding_AESKey'] = $encoding_AESKey;
            $params['is_encryption'] = $is_encryption;
            $params['type'] = $type;
            $params['is_auth'] = $is_auth;
            $params['domain'] = $domain;
            $params['ip_list'] = $ip_list;
			$result = D('Resource/App', 'Service')->addApp($params);
		}
		
		if($result) {
			return Response(999, '操作成功');
		} else {
			return Response(2006, '操作失败');
		}
	}
	
	/**
	 * 删除/开启应用
	 */
	public function deleteApp() {
		$id = intval(I('post.id'));
        $status = intval(I('post.status'));
		
		if(!$id || !in_array($status, [1, -1])) {
			return Response(2001, '参数错误');
		}
		
 		$result = D('Resource/App', 'Service')->updateAppInfo(['id'=>$id, 'status'=>$status]);
		
		if($result) {
			return Response(999, '操作成功');
		} else {
			return Response(2003, '操作失败');
		}
	}

    /**
     * 版本列表
     */
	public function versionList() {
        $id = intval(I('get.id'));
        if(!$id) {
            header("Location:/admin/app/appList");
            exit();
        }

        $appInfo = D('Resource/App', 'Service')->getAppInfo(['id'=>$id]);
        if(!$appInfo || $appInfo['status'] == -1 || $appInfo['type'] != 2) {
            header("Location:/admin/app/appList");
            exit();
        }

        $versionList = D('Resource/App', 'Service')->getAppVersionList(['status'=>1, 'app_id'=>$appInfo['app_id']]);

        $this->assign('appInfo', $appInfo);
        $this->assign('versionList', $versionList);
        $this->loadFrame('versionList');
    }

    /**
     * 保存应用版本
     */
    public function saveAppVersion() {
        $id = I('post.id');
        $app_id = I('post.app_id');
        $version = I('post.version');
        $package = I('post.package');
        $is_update = intval(I('post.is_update', 0));
        $publish_time = I('post.publish_time');
        $update_desc = I('post.update_desc');

        if(!$app_id || !$version || !$package || !$publish_time || !$update_desc) {
            return Response(2001, '参数错误');
        }

        $data = [];
        $data['app_id'] = $app_id;
        $data['version'] = $version;
        $data['package'] = $package;
        $data['is_update'] = $is_update;
        $data['publish_time'] = strtotime($publish_time);
        $data['update_desc'] = $update_desc;

        if($id) {
            $appInfo = D('Resource/App', 'Service')->getAppInfo(['id'=>$id]);
            if(!$appInfo) {
                return Response(2002, '参数错误');
            }

            $data['id'] = $id;

            $result = D('Resource/App', 'Service')->updateAppVersion($data);
        } else {
            $result = D('Resource/App', 'Service')->addAppVersion($data);
        }

        if($result) {
            return Response(999, '操作成功');
        } else {
            return Response(2003, '操作失败');
        }
    }

    /**
     * 删除应用版本
     */
    public function deleteAppVersion() {
        $id = intval(I('post.id'));

        $versionInfo = D('Resource/App', 'Service')->getAppVersionInfo(['id'=>$id]);
        if(!$versionInfo) {
            return Response(2001, '未查询到版本信息');
        }

        $result = D('Resource/App', 'Service')->updateAppVersion(['id'=>$id, 'status'=>-1]);

        if($result) {
            return Response(999, '操作成功');
        } else {
            return Response(2003, '操作失败');
        }
    }
}