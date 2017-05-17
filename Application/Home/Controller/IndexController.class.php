<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	$oa_login=session("oa_islogin");
		if(empty($oa_login)){
			echo "这里";
			$this->display("login");
		}else{
			$this->success("已登录",U("Menu/menu"));
		}
    	
    }
    public function login(){
    	if(IS_POST){
    		$user=M("oa_user");
    		$userData=$user->where("user_name='".I("user_name")."' AND user_passwd='".sha1(I("user_passwd"))."'")->find();
    		if($userData["user_id"]>0){
    			session("oa_islogin","1");
    			session("oa_user_name",I("user_name"));
    			$this->success("登录成功",U("Menu/menu"));
    		}else{
    			$this->error("登录失败",U("index/index"),1);
    		}
    	}
    }
}