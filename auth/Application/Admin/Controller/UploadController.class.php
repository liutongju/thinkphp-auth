<?php
namespace Admin\Controller;

use Think\Controller;
use Common\ORG\Util\ImageCrop;

/**
 * 文件上传接口
 * @author Evan <tangzwgo@163.com>
 * @since 2015-12-14
 */

class UploadController extends Controller {
    /**
     * 文件上传
     * platform：平台 app->app应用 web->网站
     * action：images->图片 package->app安装包 file->其他文件
     * extend：拓展目录
     * upload_file：上传文件
     */
    public function index(){
        header("Access-Control-Allow-Origin: *");
        $platform = isset($_POST['platform']) ? $_POST['platform'] : 'web';
        if($platform == 'web') {
            echo '<script type="text/javascript">document.domain = "'.C('POSTFIX_DOMAIN').'";</script>';
        }

        $file_url = '';

        //上传文件类型
        $action = isset($_POST['action'])?$_POST['action']:'file';
        if(!in_array($action, array('images','package','file','video'))) {
            $action = 'file';
        }

        //扩展路径
        $extend = isset($_POST['extend'])?$_POST['extend']:'';

        if(!isset($_FILES['upload_file'])) {
            return Response(2001, "没有上传文件");
        }

        if ($_FILES['upload_file']['error'] != UPLOAD_ERR_OK) {
            return Response(2002, "文件上传出错");
        }

        //静态资源存放路径
        $static_path = C('STATIC_PATH');

        //文件上传后存放的根路径
        $base_path = $static_path . '/attachment/'.$action.'/';
        !is_dir($base_path) && @mkdir($base_path);

        //扩展目录
        if($extend) {
            $base_path .= $extend.'/';
            !is_dir($base_path) && @mkdir($base_path);
        }

        //路径加上年扩展
        $base_path .= date('Y') . '/';
        !is_dir($base_path) && @mkdir($base_path);

        //路径加上月扩展
        $base_path .= date('m') . '/';
        !is_dir($base_path) && @mkdir($base_path);

        //路径加上日扩展
        $base_path .= date('d') . '/';
        !is_dir($base_path) && @mkdir($base_path);

        //文件最终存放路径
        $save_path = $base_path;

        //文件相对路径
        $rel_path = 'attachment/'.$action.'/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
        if($extend) {
            $rel_path = 'attachment/'.$action.'/' . $extend .'/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
        }

        if($action == 'images') {
            //上传图片
            $suffixs = array('gif', 'jpeg', 'jpg', 'jpe', 'png');

            $basename = basename($_FILES['upload_file']['name']);
            if($basename == 'blob') {
                $basename = $_POST['filename'];
            }

            $pos = strrpos($basename, '.');

            if ($pos === false || $pos == 0 || $pos == (strlen($basename) - 1)) {
                return Response(2003, "文件名错误");
            }
            $filename = substr($basename, 0, $pos);
            $suffix = strtolower(substr($basename, $pos + 1));
            if (!in_array($suffix, $suffixs)) {
                return Response(2004, "图片格式不支持");
            }
            //保存文件
            $data = file_get_contents($_FILES['upload_file']['tmp_name']);

            //删除临时文件
            @unlink($_FILES['upload_file']['tmp_name']);

            //生成一个目录下唯一的文件名
            $filename = only_file_name($save_path, time(), $suffix);

            //生成文件
            @file_put_contents($save_path . $filename, $data);

            if(!file_exists($save_path . $filename)) {
                return Response(2005, "保存图片出错");
            }

            $rel_url2 = $rel_path.$filename;

            //如果为头像，裁剪成正方形
            if($extend = 'authuser') {
                $max = $save_path . $filename;
                $mini = $save_path . 'mini-' .$filename;
                $mini_url = $rel_path . 'mini-' .$filename;

                $ic = new ImageCrop($max, $mini);
                $ic->Crop(128,128,2);
                $ic->SaveImage();
                $ic->destory();

                $rel_url2 = $mini_url;
            }

            $file_url = load_img($rel_url2);

            return Response(999, "图片上传成功",array('file_url'=>$file_url,'rel_url'=>$rel_url2));
        } else if($action == 'package') {
            //安装包
            $suffixs = array('apk', 'ipk');

            $basename = basename($_FILES['upload_file']['name']);
            if($basename == 'blob') {
                $basename = $_POST['filename'];
            }

            $pos = strrpos($basename, '.');

            if ($pos === false || $pos == 0 || $pos == (strlen($basename) - 1)) {
                return Response(2003, "文件名错误");
            }
            $filename = substr($basename, 0, $pos);
            $suffix = strtolower(substr($basename, $pos + 1));
            if (!in_array($suffix, $suffixs)) {
                return Response(2004, "文件格式不支持");
            }
            //保存文件
            $data = file_get_contents($_FILES['upload_file']['tmp_name']);

            //删除临时文件
            @unlink($_FILES['upload_file']['tmp_name']);

            //生成一个目录下唯一的文件名
            $filename = only_file_name($save_path, time(), $suffix);

            //生成文件
            @file_put_contents($save_path . $filename, $data);

            if(!file_exists($save_path . $filename)) {
                return Response(2005, "保存文件出错");
            }
            $file_url = load_img($rel_path.$filename);
            return Response(999, "安装包上传成功",array('file_url'=>$file_url,'rel_url'=>$rel_path.$filename));
        } else if($action == 'video') {
            //视频
            $suffixs = array('mp4', 'wmv', 'mov');

            $basename = basename($_FILES['upload_file']['name']);
            if($basename == 'blob') {
                $basename = $_POST['filename'];
            }

            $pos = strrpos($basename, '.');

            if ($pos === false || $pos == 0 || $pos == (strlen($basename) - 1)) {
                return Response(2003, "文件名错误");
            }
            $filename = substr($basename, 0, $pos);
            $suffix = strtolower(substr($basename, $pos + 1));
            if (!in_array($suffix, $suffixs)) {
                return Response(2004, "文件格式不支持");
            }
            //保存文件
            $data = file_get_contents($_FILES['upload_file']['tmp_name']);

            //删除临时文件
            @unlink($_FILES['upload_file']['tmp_name']);

            //生成一个目录下唯一的文件名
            $filename = only_file_name($save_path, time(), $suffix);

            //生成文件
            @file_put_contents($save_path . $filename, $data);

            if(!file_exists($save_path . $filename)) {
                return Response(2005, "保存文件出错");
            }
            $file_url = load_img($rel_path.$filename);
            return Response(999, "视频上传成功",array('file_url'=>$file_url,'rel_url'=>$rel_path.$filename));
        } else if($action == 'file') {
            //文件
            $basename = basename($_FILES['upload_file']['name']);
            if($basename == 'blob') {
                $basename = $_POST['filename'];
            }

            $pos = strrpos($basename, '.');

            if ($pos === false) {
                return Response(2003, "文件名错误");
            }

            $suffix = strtolower(substr($basename, $pos + 1));

            //保存文件
            $data = file_get_contents($_FILES['upload_file']['tmp_name']);

            //删除临时文件
            @unlink($_FILES['upload_file']['tmp_name']);

            //生成一个目录下唯一的文件名
            $filename = only_file_name($save_path, time(), $suffix);

            //生成文件
            @file_put_contents($save_path . $filename, $data);

            if(!file_exists($save_path . $filename)) {
                return Response(2005, "保存文件出错");
            }

            $file_url = load_img($rel_path.$filename);

            return Response(999, "文件上传成功",array('file_url'=>$file_url,'rel_url'=>$rel_path.$filename));
        }

        return Response(2001, "没有上传文件");
    }
}