<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-05-18 15:57:50
 * @Last Modified by:   vition
 * @Last Modified time: 2017-07-12 13:50:38
 */

/*用户功能{list|用户列表,create|新建用户,edit|编辑用户,ubase|基础信息,addinfo|添加信息}*/
namespace Home\Controller;
use Common\Controller\AmangController;
class UserController extends AmangController {
	protected $baseInfo;//定义基本信息
	protected $user;//用户模型
	//重组gethtml方法
	function _initialize(){
		$this->baseInfo=D("Info");
		$this->user=D("User");
	}
	/**
	 * [gethtml 重写gethtml方法]
	 * @return [type] []
	 */
	public function gethtml(){
		switch (I("html")) {
			case 'userlist'://用户列表
				$count=$this->user->count();
				$Page=new \Think\Page($count,10);
				$pageShow=$Page->show();

				$company=$this->baseInfo->company()->search_company();

				$department=$this->baseInfo->department()->search_department();
				$userData=$this->user->search_all($Page->firstRow,$Page->listRows);
				$this->assign("companyArray",$company);
				$this->assign("departmentArray",$department);
				$this->assign("userlist",$userData);
				$this->assign("page",$pageShow);


				break;
			case 'create': case 'ubase'://基础信息
				$userData= $this->user->get_new_code();	

				$this->assign("user_code",$userData);

				$companyData=$this->baseInfo->company()->search_company();
				$this->assign("user_companys",$companyData);

				$departmentData=$this->baseInfo->department()->search_department();
				$this->assign("user_department",$departmentData);

				$placeData=$this->baseInfo->place()->search_place();
				$this->assign("user_place",$placeData);

				$roleData=$this->baseInfo->role()->search_role();
				$this->assign("role_group",$roleData);

				break;
			case 'edit':
				# code...
				break;
			default:
				break;
		}
		parent::gethtml();
	}
	//新建用户
	public function create(){

		if(IS_POST){

			$userData=$_POST;
			
			if(empty($userData["user_passwd"])){
				$userData["user_passwd"]=sha1("Aa1234567");//初始化密码
			}else{
				$userData["user_passwd"]=sha1($userData["user_passwd"]);//密码加密
			}
			if($userData["user_sex"]=="男"){
				$userData["user_avatar"]="/assets/avatars/man.png";
			}else if($userData["user_sex"]=="女"){
				$userData["user_avatar"]="/assets/avatars/lady.png";
			}	
			$userData["user_quit"]="0000-00-00";
			$result=$this->user->add($userData);
			echo $result;
		}
	} 
	//查看下级信息
	public function showlowe(){

		// print_r($_POST);
		if(IS_POST){
			switch ($_POST['type']) {
				case 'place':

					$groupData=$this->baseInfo->group()->search_group($_POST["id"]);

					$palceHtml="";
					if(isset($_POST["department"])){
						$placeData=$this->baseInfo->place()->search_place($_POST["department"],$_POST["id"]);	
					}else{
						$placeData=$this->baseInfo->place()->search_place($_POST["id"]);	
					}
					
					foreach ($placeData as $placeArray) {
						$palceHtml.="<option class='ubase-select' data-input='place-data' data-manager='{$placeArray["place_manager"]}' value='{$placeArray["place_id"]}'>{$placeArray["place_name"]}</option>";
					}
					if(!isset($_POST["sub"])){
						$sgHtml="";
						foreach ($groupData as $group) {
							$sgHtml.="<option class='ubase-select' data-type='place' data-sub='true' data-input='group-data2' value='{$group["group_id"]}'>{$group["group_name"]}</option>";
						}
						echo json_encode(array("group"=>$sgHtml,"place"=>$palceHtml));
					}else{
						echo json_encode(array("place"=>$palceHtml));
					}
					
					
					break;	
				case 'group':
					$groupData=$this->baseInfo->group()->search_group($_POST["id"]);

					$html="";
					foreach ($groupData as $group) {
						$html.="<option class='ubase-select' data-input='group-data' value='{$group["group_id"]}'>{$group["group_name"]}</option>";
					}
					echo $html;
					break;	
				case 'role':

					$role=D("Group");
					$roleHtml="";
					$roleData=$this->baseInfo->role()->search_role($_POST["id"]);
					foreach ($roleData as $roleArray) {
						$roleHtml.="<option class='ubase-select' data-input='role-data' value='{$roleArray["role_id"]}'>{$roleArray["role_name"]}</option>";
					}
					echo $roleHtml;
					break;
				default:
					# code...
					break;
			}
		}
	}
	//添加信息
	public function addinfo(){

		if(IS_POST){
			switch ($_POST['type']) {
				case "company"://新增公司
					$resultData=$this->baseInfo->company()->add_company($_POST['value']);
					if($resultData>0){
						$newResult=$this->baseInfo->company()->find_company($resultData);
						$jsonData=array("msg"=>"success","option"=>"<option class='ubase-select' data-input='company-data' value='{$newResult["company_id"]}'>{$newResult["company_name"]}</option>");
					}else{
						$jsonData=array("msg"=>$resultData);
					}
					echo json_encode($jsonData);
				break;
				case "department"://新增部门
					$departmentData=$this->baseInfo->department()->add_department($_POST['value']);
					if($departmentData>0){
						$newResult=$this->baseInfo->department()->find_department(0,$_POST['value']);
						$jsonData=array("msg"=>"success","option"=>"<option class='ubase-select' data-input='department-data' data-type='group' value='{$newResult["department_id"]}'>{$newResult["department_name"]}</option>");
					}else{
						$jsonData=array("msg"=>$departmentData);
					}
					echo json_encode($jsonData);
				break;
				case "group"://新增分组
				// print_r($_POST);
				$groupData=$this->baseInfo->group()->add_group($_POST['department'],$_POST['value']);	
				if($groupData>0){
					$newResult=$this->baseInfo->group()->find_group($groupData);
					$jsonData=array("msg"=>"success","option"=>"<option class='ubase-select' data-input='group-data' value='{$newResult["group_id"]}'>{$newResult["group_name"]}</option>");
				}else{
					$jsonData=array("msg"=>$groupData);
				}
					echo json_encode($jsonData);
				break;
				case "place":

					if(isset($_POST["subgroup"])){
						$addResult=$this->baseInfo->place()->add_place($_POST["department"],$_POST["value"],$_POST["manager"],$_POST["subgroup"]);

					}else{
						$addResult=$this->baseInfo->place()->add_place($_POST["department"],$_POST["value"],$_POST["manager"]);
					}
					if($addResult>0){
						$placeData=$this->baseInfo->place()->find_place($addResult);
						$jsonData=array("msg"=>"success","option"=>"<option class='ubase-select' data-input='place-data' data-manager='{$placeData["place_manager"]}' value='{$placeData["place_id"]}'>{$placeData["place_name"]}</option>");
					}else{
						$jsonData=array("msg"=>$addResult);
					}

				// $jsonData=array("msg"=>print_r($_POST));
					echo json_encode($jsonData);
				break;
				case "role": case "subrole":
					if($_POST['type']=="role"){
						$input='role-group-data';
						$type="data-type='role-data'";
					}else{
						$type='';
						$input='role-data';
					}

					$role_upper=isset($_POST["department"])?$_POST["department"]:0;
					$addResult=$this->baseInfo->role()->add_role($_POST["value"],$role_upper);
					if($addResult>0){
						$roleData=$this->baseInfo->role()->is_role($_POST["value"],$role_upper);
						$jsonData=array("msg"=>"success","option"=>"<option class='ubase-select' {$type} data-input='{$input}' value='{$roleData["role_id"]}'>{$roleData["role_name"]}</option>");
					}else{
						$jsonData=array("msg"=>$addResult);
					}
					echo json_encode($jsonData);
				break;

			}
		}
	}
	//更新信息
	public function updateinfo(){
		if(IS_POST){
			switch ($_POST['type']) {
				case "company":
					$resultData=$this->baseInfo->company()->set_company($_POST["key"],$_POST["value"]);
					if($resultData>0){
						echo "success";
					}else{
						echo "修改失败";
					}
				break;
				case "department": case "group":
					if($_POST["type"]=="department"){
						$resultData=$this->baseInfo->department()->set_department($_POST["key"],$_POST["value"]);
					}else{
						$resultData=$this->baseInfo->group()->set_group($_POST["key"],$_POST["value"],$_POST["department"]);
					}
					if($resultData>0){
						echo "success";
					}else{
						echo $resultData;
					}
				break;
				case "place":
					// print_r($_POST);

					$group=isset($_POST["group"])?$_POST["group"]:0;
					$resultData = $this->baseInfo->place()->set_place($_POST["key"],$_POST["department"],$_POST["value"],$_POST["manager"],$group);

					if($resultData>0){
						echo "success";
					}else{
						echo $resultData;
					}
				break;
				case "role": case "subrole":
					// print_r($_POST);
					$role_upper=isset($_POST["department"])?$_POST["department"]:0;

					$resultData=$this->baseInfo->role()->set_role($_POST["key"],$_POST["value"],$role_upper);
					if($resultData>0){
						echo "success";
					}else{
						echo $resultData;
					}
				break;
			}
		}
	}
	// 删除信息
	public function delinfo(){
		if(IS_POST){
			switch ($_POST['type']) {
				case "company":
					$delResult=$this->baseInfo->company()->del_company($_POST["key"]);

				break;
				case "department": case "group":
					$group=D("Group");
					if($_POST["type"]=="department"){
						$delResult= $this->baseInfo->department()->del_department($_POST["key"]);
					}else{
						$delResult= $this->baseInfo->group()->del_group($_POST["key"]);
					}
				break;
				case "place":

					$delResult= $this->baseInfo->place()->del_place($_POST["key"]);
				break;

				break;
				case "role": case "subrole":
					// print_r($_POST);
					if ($_POST["type"]=="role"){
						$returnArray= $this->baseInfo->role()->search_role($_POST["key"]);
						if (!empty($returnArray)) {
							echo "该分组下含有其他角色，请删除角色再删除";
							return false;
						}
					}
					$delResult= $this->baseInfo->role()->del_role($_POST["key"]);
				break;
			}
			if($delResult>0){
				echo "success";
			}else{
				echo "删除失败";
			}
		}
	}

	/**
	 * [search_user 用户列表中查询并返回]
	 * @return [type] [description]
	 */
	function search_user(){
		$user=D("User");
		$count=$user->where("user_state=1")->count();

		$condition=array();
		foreach ($_POST["condition"] as $key => $value) {
			if ($value["value"]!=""){
				if($value["name"]=="user_state"){
					$condition[$value["name"]]=array("EQ","{$value["value"]}");
				}else{
					$condition[$value["name"]]=array("LIKE","%{$value["value"]}%");
				}
				
			}
		}
		// print_r($condition);
		$count=$user->where($condition)->count();

		$Page=new \Think\Page($count,$_POST["limit"]);
		$pageShow=$Page->show();

		$userDataArray=$user->search_all($Page->firstRow,$Page->listRows,$condition);
		$userHtml="";
		foreach ($userDataArray as $userData) {
			if($userData["user_state"]=="在职"){
				$button='<button class="btn btn-xs btn-info user-edit" data-toggle="modal" data-target="#userModal"><i class="ace-icon fa fa-pencil bigger-120"></i></button><button class="btn btn-xs btn-success" disabled="disabled"><i class="ace-icon fa fa-check-circle bigger-120"></i></button><button class="btn btn-xs btn-warning"><i class="ace-icon fa fa-question-circle bigger-120"></i></button><button class="btn btn-xs btn-danger"><i class="ace-icon fa fa-times-circle bigger-120"></i></button>';
			}else if($userData["user_state"]=="未激活"){
				$button='<button class="btn btn-xs btn-info user-edit" data-toggle="modal" data-target="#userModal"><i class="ace-icon fa fa-pencil bigger-120"></i></button><button class="btn btn-xs btn-success" ><i class="ace-icon fa fa-check-circle bigger-120"></i></button><button class="btn btn-xs btn-warning" disabled="disabled"><i class="ace-icon fa fa-question-circle bigger-120"></i></button><button class="btn btn-xs btn-danger"><i class="ace-icon fa fa-times-circle bigger-120"></i></button>';
			}else{
				$button='<button class="btn btn-xs btn-info user-edit" data-toggle="modal" data-target="#userModal"><i class="ace-icon fa fa-pencil bigger-120"></i></button><button class="btn btn-xs btn-success"><i class="ace-icon fa fa-check-circle bigger-120"></i></button><button class="btn btn-xs btn-warning"><i class="ace-icon fa fa-question-circle bigger-120"></i></button><button class="btn btn-xs btn-danger" disabled="disabled"><i class="ace-icon fa fa-times-circle bigger-120"></i></button>';
			}
		 	$userHtml.='<tr><td><label class="pos-rel"><input class="ace" type="checkbox"><span class="lbl"></span></label></td><td>'.$userData["user_name"].'</td><td>'.$userData["user_code"].'</td><td>'.$userData["user_company"].'</td><td>'.$userData["user_group"].'</td><td>'.$userData["user_subgroup"].'</td><td>'.$userData["user_role"].'</td><td>'.$userData["user_sex"].'</td><td>'.$userData["user_entry"].'</td><td>'.$userData["user_born"].'</td><td>'.$userData["user_state"].'</td><td><div class="hidden-sm hidden-xs btn-group user-control" data-userid="'.$userData["user_id"].'">'.$button.'</div></td></tr>';
		 } 

		echo json_encode(array("userhtml"=>$userHtml,"pagehtml"=>$pageShow)) ;
		// print_r($userData);
	}
	//显示分组
	function show_subgroup(){
		if(IS_POST){

			$resultDataArray=$this->baseInfo->subgroup()->search_group($_POST["group_id"]);
			$subgroupHtml="<option value=''>所有分组</option>";
			foreach ($resultDataArray as $resultData) {
				$subgroupHtml.="<option value='{$resultData["subgroup_id"]}'>{$resultData["subgroup_name"]}</option>";
			}

			$placetDataArray=$this->baseInfo->place()->search_place($_POST["group_id"],0);
			$placeHtml="<option value=''>所有职位</option>";
			foreach ($placetDataArray as $placeData) {
				$placeHtml.="<option value='{$placeData["place_id"]}'>{$placeData["place_name"]}</option>";
			}
			// echo "{'subgroup':'".$subgroupHtml."','place':'".$placeHtml."'}";
			$json='{"subgroup":"'.$subgroupHtml.'","place":"'.$placeHtml.'"}';
			// echo '{subgroup:"<option value="">所有分组</option>",place:"<option value="">所有职位</option><option value="5">"}';
			echo $json;
			// {'subgroup':'<option value=''>所有分组</option>','place':'<option value=''>所有职位</option><option value='5'></option>'}
			// echo json_encode(array("subgroup"=>urlencode($subgroupHtml),"place"=>urlencode($placeHtml)));

		}
	}
	function show_place(){
		if(IS_POST){
			$subgroup=D("Info");
			$placetDataArray=$subgroup->search_place($_POST["group_id"],$_POST["subgroup_id"]);

			$placeHtml='<option value="">所有职位</option>';
			foreach ($placetDataArray as $placeData) {
				$placeHtml.='<option value="'.$placeData["place_id"].'">'.$placeData["place_name"].'</option>';
			}

			echo $placeHtml;
		}
	}
	//取用户模板
	function get_usertemplate(){
		if(IS_POST){
			$user=D("User");
			$resultData=$user->find_user($_POST["user_id"]);
		}

		$company=$this->baseInfo->company()->search_company();
		$department=$this->baseInfo->department()->search_department();
		$group=$this->baseInfo->group()->search_group($resultData["user_group"]);
		$place=$this->baseInfo->place()->search_place($resultData["user_group"],$resultData["user_subgroup"]);
		$roles=$this->baseInfo->role()->search_role();
		$thisRole=$this->baseInfo->role()->find_role($resultData["user_role"]);
		$role=$this->baseInfo->role->search_role($thisRole["role_upper"]);
		$this->assign("companyArray",$company);
		$this->assign("groupArray",$department);
		$this->assign("subgroupArray",$group);
		$this->assign("placeArray",$place);
		$this->assign("rolesArray",$roles);
		$this->assign("roleArray",$role);



		$this->assign("userinfo",$resultData);
		echo $this->fetch("userinfo");
	}
}