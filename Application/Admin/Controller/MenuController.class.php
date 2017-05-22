<?php
namespace Admin\Controller;
use Common\Controller\AmangController;
class MenuController extends AmangController {
	//menu
	public function menu(){
		$this->display("menu");
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