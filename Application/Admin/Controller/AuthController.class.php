<?php
namespace Admin\Controller;
// use Think\Controller;
use Common\Controller\AmangController;
class AuthController extends AmangController {
	//重新加载方法
	public function gethtml(){
		if (I("html")=='authlist'){
			$condata=$this->showCon();
			$this->assign("condata",$condata);
		}
		parent::gethtml();
	}
	//取控制器信息了
	private function showCon(){
		$url=APP_PATH."/Home/Controller";
		$files=scandir($url);
		$authArray=array();
		for($i=1;$i<count($files);$i++){
			preg_match('/([\S]*)Controller.class.php/', $files[$i],$conMatch);
			if(count($conMatch)>1){
				$conFile=file_get_contents($url."/".$conMatch[0]);
				preg_match('/\/\*([\S]*){([\S]*)}{([\S]*)}\*\//', $conFile,$cMatch);
				if(count($cMatch)>1){
					// array_push($authArray, array("name"=>$cMatch[1],"controller"=>$cMatch[2]));
					array_push($authArray, array("name"=>$cMatch[1],"controller"=>explode(",", $cMatch[2]),"controllercn"=>explode(",", $cMatch[3])));
				}
				/*用户功能{list,create,edit}*/
				// print_r($authArray);
			}
		}
		return $authArray;
		
	}  
}