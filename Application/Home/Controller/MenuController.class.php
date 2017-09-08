<?php


/*{"control":"Menu","name":"主菜单","icon":"fa fa-tachometer","menus":[{"name":"企业信息","icon":"fa fa-tachometer","menus":"company"},{"name":"个人信息","icon":"fa fa-user","menus":"profile"}]}*/
namespace Home\Controller;
use Common\Controller\AmongController;
class MenuController extends AmongController {
	//menu
	public function menu(){
		if(session("prev_url")!="redirect"){
			/*如果存在参数跳链接就直接跳转*/
			echo "<script>top.location.href='".session("prev_url")."'</script>";
			session("prev_url","redirect");
			exit;
		}
		$authoMenu=$this->get_auth(array("Menu",array("Attend"=>array("checkin"))));
		if(I("html")!==null){
			$this->assign("html",I("html"));
		}
		$this->assign("authoMenu",$authoMenu);
		$this->display("menu");
	}
}