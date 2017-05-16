<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	$oa_login=session("oa_islogin");
		if(empty($oa_login)){
			$this->display("login");
		}else{
			$this->success("已登录",U("Menu/index"));
		}
    	
    }
    public function login(){
    	if(IS_POST){
    		$user=M("oa_user");
    		$userData=$user->where("user_name='".I("user_name")."' AND user_passwd='".sha1(I("user_passwd"))."'")->find();
    		if($userData["user_id"]>0){
    			session("oa_islogin","1");
    			$this->success("登录成功",U("Menu/index"));
    		}
    	}
    }
}