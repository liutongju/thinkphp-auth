<?php
namespace Common\ORG\Util;
/**
 * API接口公共工具类
 * @author Evan <tangzwgo@163.com>
 * @since 2016-01-06
 */

class ApiTool {
    //应用ID
    private $AppID;
    //应用秘钥
    private $AppSecret;
    //令牌
    private $Token;
    //消息加解密密钥
    private $EncodingAESKey;
    //是否加密
    private $IsEncryption;
    
    /**
     * 构造函数
     */
    public function __construct() {
        //初始化应用配置
        $this->AppID = C('APP_ID');
        $this->AppSecret = C('APP_SECRET');
        $this->Token = C('TOKEN');
        $this->EncodingAESKey = C('ENCODING_AES_KEY');
        $this->IsEncryption = C('IS_ENCRYPTION');
    }
    
    /**
     * 打包数据
     * @param type $data
     */
    public function packData($data) {
        //不需要加密，使用token验证请求合法性
        $data['appid'] = $this->AppID;
        //当前时间戳
        $data['timestamp'] = time();
        
        $sign = md5($this->Token . $data['timestamp'] . $this->Token);
        $data['sign'] = $sign;
        
        return $data;
    }
    
    /**
     * 解包打包数据
     * @param type $data
     */
    public function unPackData($data) {
        $responseData = json_decode($data, true);
        if(!$responseData) {
            //采用xml解析
        }
        
        //不需要解密
        return $responseData;
    }
    
    /**
     * 获取数据
     * @return type
     */
    public function getdata($params) {
        if (!(isset($params) && !empty($params) && count($params) > 0 && isset($params['action']) && !empty($params['action']))) {
            return ReturnMsg(1000, '请求出错');
        }
        $data = $this->packData($params);
        $result = \Org\Util\Http::post('http://' . C('API_DOMAIN') . '/api', $data);
        return $this->unPackData($result);
    }
    
    /**
     * 调用权限系统接口
     * @return type
     */
    public function getAuthData($params) {
    	if (!(isset($params) && !empty($params) && count($params) > 0 && isset($params['action']) && !empty($params['action']))) {
    		return ReturnMsg(1000, '请求出错');
    	}
    	$data = $this->packData($params);
    	$result = \Org\Util\Http::post('http://' . C('AUTH_DOMAIN') . '/api', $data);
    	return $this->unPackData($result);
    }
}