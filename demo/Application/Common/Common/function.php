<?php

/**
 * 公共函数
 * @author Evan <tangzwgo@163.com>
 * @since 2015-12-07
 */

/**
 * 返回数据
 * @param type $ResponseCode    响应码
 * @param type $ResponseMsg     响应消息
 * @param type $ResponseData    响应数据
 */
function ReturnMsg($ResponseCode = 999,$ResponseMsg = '调用成功',$ResponseData = array()){
    if(!is_numeric($ResponseCode)) {
        return false;
    }
    $result = array(
        'Code'=>$ResponseCode,
        'Msg'=>$ResponseMsg,
        'Data'=>$ResponseData
    );
    return $result;
}

/**
 * 接口数据响应
 * @param type $ResponseCode    响应码
 * @param type $ResponseMsg     响应消息
 * @param type $ResponseData    响应数据
 * @param type $ResponseType    响应数据类型
 */
function Response($ResponseCode = 999,$ResponseMsg = '接口请求成功',$ResponseData = array(),$ResponseType = 'json'){
    if(!is_numeric($ResponseCode)) {
        return '';
    }        
    
    $ResponseType = isset($_GET['format']) ? $_GET['format'] : $ResponseType;
    
    $result = array(
        'Code'=>$ResponseCode,
        'Msg'=>$ResponseMsg,
        'Data'=>$ResponseData
    );       
    
    if($ResponseType == 'json') {
        json($ResponseCode, $ResponseMsg, $ResponseData);
        exit();
    } else if($ResponseType == 'xml') {
        xmlencode($ResponseCode, $ResponseMsg, $ResponseData);
        exit();
    } else if($ResponseType == 'array') {
        var_dump($result);
        exit();
    } else {
        json($ResponseCode, $ResponseMsg, $ResponseData);
        exit();
    }
}

/**
 * 响应Json格式数据
 * @param type $ResponseCode    响应码
 * @param type $ResponseMsg     响应消息
 * @param type $ResponseData    响应数据
 */
function json($ResponseCode = 999,$ResponseMsg = '接口请求成功',$ResponseData = array()){
    if(!is_numeric($ResponseCode)) {
        return '';
    }
    
    $result = array(
        'Code'=>$ResponseCode,
        'Msg'=>$ResponseMsg,
        'Data'=>$ResponseData,
        'Type'=>'json'
    );
    header("Content-type: text/html; charset=utf-8");
    echo json_encode($result);
    exit();
}

/**
 * 响应xml格式数据
 * @param type $ResponseCode    响应码
 * @param type $ResponseMsg     响应消息
 * @param type $ResponseData    响应数据
 */
function xmlencode($ResponseCode = 999,$ResponseMsg = '接口请求成功',$ResponseData = array()) {
    if (!is_numeric($ResponseCode)) {
        return '';
    }

    $result = array(
        'Code'=>$ResponseCode,
        'Msg'=>$ResponseMsg,
        'Data'=>$ResponseData,
        'Type'=>'xml'
    );

    header("Content-Type:text/xml");
    $xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
    $xml .= "<root>\n";

    $xml .= xml_to_encode($result);

    $xml .= "</root>";
    echo $xml;
}

/**
 * 将数据编码成xml格式
 * @param type $data
 * @return type
 */
function xml_to_encode($data) {
    $xml = $attr = "";
    foreach($data as $key => $value) {
        if(is_numeric($key)) {
            $attr = " id='{$key}'";
            $key = "item";
        }
        $xml .= "<{$key}{$attr}>";
        $xml .= is_array($value) ? xml_to_encode($value) : $value;
        $xml .= "</{$key}>\n";
    }
    return $xml;
}

/**
 * 加载图片
 */
function load_img($img) {
    $static_domain = C('STATIC_DOMAIN');
    $img_version = C('IMG_VERSION');
    $url = 'http://' . $static_domain . '/' . $img . '?v=' . $img_version;
    return $url;
}

/**
 * 加载静态文件
 */
function load_static($file) {
    $static_domain = C('STATIC_DOMAIN');
    $url = 'http://' . $static_domain . '/' . $file;
    return $url;
}

/**
 * 加载css文件
 */
function load_css($css) {
    $static_domain = C('STATIC_DOMAIN');
    $css_version = C('CSS_VERSION');
    if (is_array($css)) {
        if (count($css) == 0) {
            return '';
        }

        $url = 'http://' . $static_domain . '/??';
        foreach ($css as $c) {
            $url .= $c . '.css?' . $css_version . ',';
        }
    } else {
        $url = 'http://' . $static_domain . '/' . $css . '.css?v=' . $css_version;
    }

    return $url;
}

/**
 * 加载js文件
 */
function load_js($js) {
    $static_domain = C('STATIC_DOMAIN');
    $js_version = C('JS_VERSION');
    if (is_array($js)) {
        if (count($js) == 0) {
            return '';
        }

        $url = 'http://' . $static_domain . '/??';
        foreach ($js as $j) {
            $url .= $j . '.js?' . $js_version . ',';
        }
    } else {
        $url = 'http://' . $static_domain . '/' . $js . '.js?v=' . $js_version;
    }

    return $url;
}

/**
 * 生成分页
 * @param $url 链接
 * @param $total   记录总数
 * @param $curPage 当前页
 * @param $pageSize    每页显示数量
 */
function create_page($url, $total, $curPage, $pageSize) {
	//链接
	if (!strpos($url, '?')) {
		$url .= '?page=';
	} else {
		$url .= '&page=';
	}

	//计算总共有多少页
	$totalPage = ceil($total / $pageSize);

	//上一页
	if ($curPage <= 1) {
		$prePage = '<li class="disabled"><a href="javascript:;">上一页</a></li>';
	} else {
		$prePage = '<li><a href="' . $url . ($curPage - 1) . '">上一页</a></li>';
	}

	//下一页
	if ($curPage >= $totalPage) {
		$nextPage = '<li class="disabled"><a href="javascript:;">下一页</a></li>';
	} else {
		$nextPage = '<li><a href="' . $url . ($curPage + 1) . '">下一页</a></li>';
	}

	//遍历页数
	$pages = '';
	for ($i = 1; $i <= $totalPage; $i++) {
		if ($i == 1) {
			if ($i == $curPage) {
				$pages .= '<li class="active"><a href="' . $url . $i . '">' . $i . '</a></li>';
			} else {
				$pages .= '<li><a href="' . $url . $i . '">' . $i . '</a></li>';
			}
		}
		if ($i == $totalPage && $totalPage != 1) {
			if ($i == $curPage) {
				$pages .= '<li class="active"><a href="' . $url . $i . '">' . $i . '</a></li>';
			} else {
				$pages .= '<li><a href="' . $url . $i . '">' . $i . '</a></li>';
			}
		}
		if ($i - $curPage >= -5 && $i - $curPage <= 5 && $i != 1 && $i != $totalPage) {
			if ($i == $curPage) {
				$pages .= '<li class="active"><a href="' . $url . $i . '">' . $i . '</a></li>';
			} else {
				$pages .= '<li><a href="' . $url . $i . '">' . $i . '</a></li>';
			}
		}
	}
	
	$pageHtml = '<ul class="pagination pagination-sm no-margin pull-right">';
	$pageHtml .= $prePage;
	$pageHtml .= $pages;
	$pageHtml .= $nextPage;
	$pageHtml .= '</ul>';

	return $pageHtml;
}