<?php
/*主菜单{company|企业信息,profile|个人信息}*/
namespace Home\Controller;
use Common\Controller\AmangController;
class MenuController extends AmangController {
	//menu
	public function menu(){
		$this->display("menu");
	}
	
}