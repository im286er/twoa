<?php
/*主菜单{company|企业信息|fa fa-tachometer,profile|个人信息|fa fa-user}fa fa-tachometer*/
namespace Home\Controller;
use Common\Controller\AmangController;
class MenuController extends AmangController {
	//menu
	public function menu(){
		$authoMenu=$this->get_auth(array("Menu"));
		$this->assign("authoMenu",$authoMenu);
		$this->display("menu");
	}
}