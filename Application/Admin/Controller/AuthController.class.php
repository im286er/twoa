<?php
/*权限管理{authlist|权限列表|fa fa-lock}glyphicon glyphicon-user*/
namespace Admin\Controller;
// use Think\Controller;
use Common\Controller\AmangController;
class AuthController extends AmangController {
	protected $baseInfo;
	protected $rauth;

	public function _initialize(){
		$this->baseInfo=D("Info");
		$this->rauth=D("Rauth");
	}

	//重新加载方法
	public function gethtml(){
		if (I("html")=='authlist'){
			$condata=$this->showCon();
			$rolesDataArray=$this->baseInfo->role()->search_role();
			$this->assign("rolesDataArray",$rolesDataArray);
			// print_r($condata);
			$this->assign("condata",$condata);
		}
		parent::gethtml();
	}

	/**
	 * [show_role 显示角色]
	 * @return [type] [description]
	 */
	function show_role(){
		if(IS_POST){
			$roleDataArray=$this->baseInfo->role()->search_role($_POST["role_id"]);
			echo $this->baseInfo->role()->getLastSql();
			$roleHtml="<option value='0'>选择角色</option>";
			foreach ($roleDataArray as $roleData) {
				$roleHtml.="<option value='{$roleData['role_id']}'>{$roleData['role_name']}</option>";
			}
			echo $roleHtml;
		}
	}

	function show_rauth(){
		$rauth=$this->rauth->find_auth("",false,$_POST["role_id"]);
		echo json_encode($rauth);
	}

	function set_rauth(){
		if(IS_POST){
			echo $this->rauth->add_auth($_POST["rauth_role"],$_POST["rauth_auth"]);
		}
	}
	//取控制器信息了

	private function showCon(){
		$authArray=array();
		$models=array("Admin","Home");//根据不同的模块

		foreach ($models as $model) {
			$url=APP_PATH."/{$model}/Controller";
			$tempModel=array();
			$files=scandir($url);
			for($i=1;$i<count($files);$i++){//循环目录下所有文件
				preg_match('/([\S]*)Controller.class.php/', $files[$i],$conMatch);
				if(count($conMatch)>1){
					$conFile=file_get_contents($url."/".$conMatch[0]);
					preg_match('/\/\*([\S]*){([\S\ ]*)}([\S\ ]*)\*\//', $conFile,$cMatch);
					$conNameArray=array();
					if(count($cMatch)>1){
						// echo $cMatch[0];
						$controll=explode(",", $cMatch[2]);
						for($c=0;$c<count($controll);$c++){
							array_push($conNameArray, explode("|", $controll[$c]));
						}
						array_push($tempModel, array("name"=>array("cn"=>$cMatch[1],"en"=>$conMatch[1],"icon"=>$cMatch[3]),"controller"=>$conNameArray));
					}
				}
			}
			$authArray[$model]=$tempModel;
		}
		

		return $authArray;//返回三维数组信息
		 
	}  
}