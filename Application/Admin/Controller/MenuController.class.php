<?php
namespace Admin\Controller;
use Common\Controller\AmongController;
class MenuController extends AmongController {
	//menu
	
	public function menu(){
		$authoMenu=$this->get_auth();
		$this->assign("authoMenu",$authoMenu);	
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