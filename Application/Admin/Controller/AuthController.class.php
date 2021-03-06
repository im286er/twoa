<?php


/*{"control":"Auth","name":"权限管理","icon":"glyphicon glyphicon-user","menus":[{"name":"页面权限","icon":"fa fa-eye","menus":"authlist"},{"name":"数据表权限","icon":"fa fa-database","menus":"authtable"}]}*/


namespace Admin\Controller;
// use Think\Controller;
use Common\Controller\AmongController;
class AuthController extends AmongController {
	protected $baseInfo;
	protected $rauth;

	public function _initialize(){
		$this->baseInfo=D("Info");
		$this->rauth=D("Rauth");
	}

	//重新加载方法
	public function gethtml(){
		switch (I("html")) {
			case 'authlist':
				$condata=$this->showCon();
				// print_r($condata);
				$rolesDataArray=$this->baseInfo->role()->search_role();
				$this->assign("rolesDataArray",$rolesDataArray);
				$this->assign("condata",$condata);
				break;
			case 'authtable':
				$rolesDataArray=$this->baseInfo->role()->search_role();
				$this->assign("rolesDataArray",$rolesDataArray);

				$dataTablesArray=$this->showModel();
				$adminTableAuth=$this->rauth->find_table("2");

				$tableAuth=array();
				$adminNewAuth=array();
				foreach ($dataTablesArray as $tableName) {
					if($adminTableAuth[$tableName]==null){
						$tableAuth[$tableName]=array("select");
						$adminNewAuth[$tableName]=array("select","insert","update","delete");
					}
				}
				if(!empty($tableAuth)){
					$this->appTableAuth($tableAuth);
					$this->appTableAuth($adminNewAuth,"2");
				}
				// print_r($adminTableAuth);
				$this->assign("dataTablesArray",$dataTablesArray);
			default:
				# code...
				break;
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
			// echo $this->baseInfo->role()->getLastSql();
			$roleHtml="<option value='0'>选择角色</option>";
			foreach ($roleDataArray as $roleData) {
				$roleHtml.="<option value='{$roleData['role_id']}'>{$roleData['role_name']}</option>";
			}
			if($_POST["stype"]=="auth"){
				$auth=$this->rauth->find_auth("",false,$_POST["role_id"]);
			}else{
				$auth=$this->rauth->find_table($_POST["role_id"]);
			}
			
			echo json_encode(array("roleHtml"=>$roleHtml,"auth"=>$auth));
		}
	}

	/**
	 * [show_rauth 显示权限]
	 * @return [type] [description]
	 */
	function show_rauth(){
		$rauth=$this->rauth->find_auth("",false,$_POST["role_id"]);
		echo json_encode($rauth);
	}
	/**
	 * [set_rauth 设置权限]
	 */
	function set_rauth(){
		if(IS_POST){
			if($_POST["rauth_role"]<=0){
				echo "不能更改此角色权限";
			}else{
				echo $this->rauth->add_auth($_POST["rauth_role"],$_POST["rauth_auth"]);
			}
			
		}
	}

	/**
	 * [show_autable 显示数据表]
	 * @return [type] [description]
	 */
	function show_autable(){
		$table=$this->rauth->find_table($_POST["role_id"]);
		echo json_encode($table);
	}

	/**
	 * [set_autable 设置数据表权限]
	 */
	function set_autable(){
		if(IS_POST){
			if($_POST["rauth_role"]<0){
				echo "不能更改此角色权限";
			}else{
				echo $this->rauth->add_table($_POST["rauth_role"],$_POST["rauth_table"]);
			}
			
		}
	}

	/**
	 * [showCon 取控制器，即显示权限]
	 * @return [type] [description]
	 */
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
					// preg_match('/\/\*([\S]*){([\S\ ]*)}([\S\ ]*)\*\//', $conFile,$cMatch);
					preg_match('/\/\*(\{[\ \S]*\})\*\//', $conFile,$cMatch);
					// print_r($cMatch[1]);
					// echo $cMatch[1];
					if(count($cMatch)>1){
						array_push($tempModel,json_decode($cMatch[1],true));
					}
				}
			}
			$authArray[$model]=$tempModel;
		}
		return $authArray;//返回三维数组信息
	}
	/**
	 * [showModel 取数据表]
	 * @return [type] [返回数据表名数组]
	 */
	private function showModel(){
		$url=APP_PATH."/Common/Model";
		$elimArray=array("InfoModel.class.php","AmongModel.class.php");
		$files=scandir($url);
		$dataTables=array();
		for($i=1;$i<count($files);$i++){//循环目录下所有文件
			preg_match('/([\S]*)Model.class.php/', $files[$i],$conMatch);
			if(!in_array($conMatch[0], $elimArray)){
				if(count($conMatch)>1){
					// echo "oa_".strtolower($conMatch[1])."</br>";
					array_push($dataTables, "oa_".strtolower($conMatch[1]));
				}
			}
			
		}
		return $dataTables;
	}
	/**
	 * [appTableAuth 追加表管理权]
	 * @param  [type]  $tableArr [二维数据，array("表名"=>array("select",[,"insert","update","delete"]))]
	 * @param  integer $role_id  [指定role id,默认0则追加全部没有这个表权限的角色]
	 * @return [type]            [description]
	 */
	function appTableAuth($tableArr,$role_id=0){
		$roleTableAuth=array();
		if($role_id==0){
			$roleTableAuth=$this->rauth->field("rauth_role,rauth_table")->where("rauth_role<>'2'")->search_auth();

		}else{
			$roleTableAuth=$this->rauth->field("rauth_role,rauth_table")->where("rauth_role={$role_id}")->search_auth();
		}
		foreach ($roleTableAuth as $roleAuthInfo) {
			$tempAuth=json_decode($roleAuthInfo["rauth_table"],true);
			foreach ($tableArr as $tableName=> $tableAtuh) {
				if($tempAuth[$tableName]==null){
					$tempAuth[$tableName]=$tableAtuh;
				}
			}
			$this->rauth->set_table($roleAuthInfo["rauth_role"],json_encode($tempAuth));
		}
		

		
	}
}

