<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    function get_ip() {
        static $ip;
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $ip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $ip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $ip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $ip = getenv("HTTP_CLIENT_IP");
            } else {
                $ip = getenv("REMOTE_ADDR");
            }
        }
        if (preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $ip)) {
            return $ip;
        } else {
            return '127.0.0.1';
        }
    }


    public function article(){
        $model = M("user");
        $model->where("id=2");
        $arr = $this->pageT($model,2);
        $this->assign("list",$arr["data"]);
        $this->assign("page",$arr["pages"]);
        $this->display(); // 输出模板
    }
    /**
     * @param $obj数据模型对象
     * @param $size每页显示条数
     * @return array返回数组array("pages"=>$show,"data"=>$list);
     */
    public function pageT($obj,$size){
        $obj1 = clone $obj;
        $count = $obj->count();
        $Page = new \Think\Page($count,$size);
        $Page->setConfig("prev","上一页");
        $Page->setConfig("next","下一页");
        $show = $Page->show();// 分页显示输出
        $list = $obj1->limit($Page->firstRow.','.$Page->listRows)->select();
        return array("pages"=>$show,"data"=>$list);
    }

    public function code(){
        $Verify = new \Think\Verify();
        $Verify->fontSize = 20;
        $Verify->length = 4;
//        $Verify->useImgBg=true;
        $Verify->useZh = true;
        $Verify->fontttf = 'simfang.ttf';
        $Verify->zhSet = "你好的呀";
        $Verify->entry(8);
    }

    public function yan(){
        $Verify = new \Think\Verify();
        $code = I("post.ycode");
        if($Verify->check($code,8)){
            echo "yes";
        }else{
            echo "no";
        }
    }

    public function img(){
        $img = new \Think\Image();
        $img->open('./try.jpg');
//        $img->crop(200,200,200,200)->save("./crop.jpg");
//        $img->water('./wa.png',2,100)->save("./crop.jpg");
        $img->text("think","./a.ttf",100,"#000000",1,20,20)->save("./text.jpg");
    }


    public function delHead(){
        $id = (int)I("get.id");
        $src = I("get.src");
        $model = M("imgs");
        $res = $model->delete($id);
        if($res){
            $real = $_SERVER['DOCUMENT_ROOT'];
            unlink("{$real}"."{$src}");
            $this->success("删除成功");
        }else{
            $this->error("网络繁忙，请稍后重试");
        }
    }

    public function addHead(){
        $info = $this->upload("headPicture");
        if($info){
            $data = array();
            foreach ($info as $val){
                $savename = $val['savename'];
                $savepath = $val["savepath"];
                $path = $savepath.$savename;
                $arr["path"] = $path;
                $data[] = $arr;
            }
            $model = M("imgs");
            $res = $model->addAll($data);
            if($res){
                $this->success("上传成功");
            }
        }else{
            $this->error("上传失败");
        }
    }

    public function upload($cate){
        $upload = new \Think\Upload();// 实例化上传类
//        $upload->maxSize = 3145728 ;// 设置附件上传大小
//        $upload->autoSub = false;//这个属性设置成FALSE就没有时间文件夹
//        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        // 设置附件上传类型
        $upload->rootPath = './Uploads/';
        // 设置附件上传根目录
        $upload->savePath = $cate."/";
        // 设置附件上传（子）目录 // 上传文件
        $info = $upload->upload();
        return $info;
    }


	public function angular(){
		header("Access-Control-Allow-Origin:*");
		$model = M('user');
		$res = $model->select();
		$this->ajaxReturn($res);
	}

	public function angDel($id){
		header("Access-Control-Allow-Origin:*");
		$model = M("user");
		$res = $model->delete($id);
		$this->ajaxReturn($res);
	}
}