<?php
/*权限管理{authlist|权限列表}*/
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
		
		$authArray=array();
		$models=array("Admin","Home");//根据不同的模块

		foreach ($models as $model) {
			$url=APP_PATH."/{$model}/Controller";
			$files=scandir($url);
			for($i=1;$i<count($files);$i++){//循环目录下所有文件
				preg_match('/([\S]*)Controller.class.php/', $files[$i],$conMatch);
				if(count($conMatch)>1){
					$conFile=file_get_contents($url."/".$conMatch[0]);
					preg_match('/\/\*([\S]*){([\S]*)}\*\//', $conFile,$cMatch);
					$conNameArray=array();
					if(count($cMatch)>1){
						$controll=explode(",", $cMatch[2]);
						for($c=0;$c<count($controll);$c++){
							array_push($conNameArray, explode("|", $controll[$c]));
						}
						array_push($authArray, array("name"=>array("cn"=>$cMatch[1],"en"=>$conMatch[1]),"controller"=>$conNameArray));
					}
					

				}
			}
		}
		

		return $authArray;//返回三维数组信息
		 
	}  
}