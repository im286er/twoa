<?php


/*{"control":"Menu","name":"主菜单","icon":"fa fa-tachometer","menus":[{"name":"企业信息","icon":"fa fa-tachometer","menus":"company"},{"name":"个人信息","icon":"fa fa-user","menus":"profile"}]}*/
namespace Home\Controller;
use Common\Controller\AmongController;
class MenuController extends AmongController {
	//menu
	public function menu(){
		$authoMenu=$this->get_auth(array("Menu",array("Attend"=>array("checkin"))));
		$this->assign("authoMenu",$authoMenu);
		$this->display("menu");
	}
}