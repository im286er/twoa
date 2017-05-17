<?php
namespace Home\Controller;
use Think\Controller;
class MenuController extends AmangController {
	//menu
	public function menu(){
		$this->display("menu");
	}
	//company
	public function company(){
		$this->display("company");
	}
	//个人信息
	public function profile(){
		$this->display("profile");
	}
	//退出
	public function logout(){
		if(IS_POST){
			session("oa_islogin",NULL);
    		session("oa_user_name",NULL);
    		echo "logout";
		}
	}
}