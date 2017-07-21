<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-05-18 15:57:50
 * @Last Modified by:   vition
 * @Last Modified time: 2017-07-21 15:59:01
 */

/*用户功能{userlist|用户列表|fa fa-users,create|新建用户|glyphicon glyphicon-user,ubase|基础信息|glyphicon glyphicon-send}fa fa-users*/
namespace Home\Controller;
use Common\Controller\AmongController;
class UserController extends AmongController {
	protected $baseInfo;//定义基本信息
	protected $user;//用户模型
	//重组gethtml方法
	function _initialize(){
		parent::_initialize();
		$this->baseInfo=D("Info");
		$this->user=D("User");
		// parent::_initialize();
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

				$allRole=$this->baseInfo->role()->search_role(0,true);
				$this->assign("allRoleArray",$allRole);

				if(I("html")=="ubase"){
					$leaderData=$this->baseInfo->place()->get_leader();
					$this->assign("place_leader",$leaderData);
				}
				break;
			case 'edit':
				# code...
				break;
			default:
				break;
		}
		parent::gethtml();
	}
	//用户新建和修改
	public function con_user(){
		if(IS_POST){
			$userData=$_POST["data"];
			// $userId=$userData["user_id"];
			// unset($userData["user_id"]);
			if(empty($userData["user_passwd"])){
				if($_POST["type"]=="add"){
					$userData["user_passwd"]=sha1("Aa1234567");//初始化密码
				}else{
					unset($userData["user_passwd"]);
				}
			}else{
				$userData["user_passwd"]=sha1($userData["user_passwd"]);//密码加密
			}
			 if($userData["user_sex"]=="男"){
				$userData["user_avatar"]="/assets/avatars/man.png";
			}else if($userData["user_sex"]=="女"){
				$userData["user_avatar"]="/assets/avatars/lady.png";
			}

			switch ($_POST["type"]) {
				case 'add':
					$this->user->add($userData);
					break;
				case 'update':
					echo $this->user->set_user($userData["user_id"],$userData);
					break;
				case 'state':
					$resultData=$this->user->set_state($_POST["user_id"],$_POST["user_state"]);
					
					if($resultData>0){
						$content="成功改变用户状态";
						$this->assign("content",$content);
						echo $this->fetch("Common@alert/success");	
					}
					
					break;
				default:
					# code...
					break;
			}
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
						$palceHtml.="<option class='ubase-select' data-input='place-data' data-checked='{$placeArray["place_manager"]}' value='{$placeArray["place_id"]}'>{$placeArray["place_name"]}</option>";
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
					$departmentData=$this->baseInfo->department()->add_department($_POST['value'],$_POST['checked']);
					if($departmentData>0){
						$newResult=$this->baseInfo->department()->find_department(0,$_POST['value']);
						$jsonData=array("msg"=>"success","option"=>"<option class='ubase-select' data-input='department-data' data-type='group' data-checked='{$newResult["department_leader"]}' value='{$newResult["department_id"]}'>{$newResult["department_name"]}</option>");
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
						$addResult=$this->baseInfo->place()->add_place($_POST["department"],$_POST["value"],$_POST["checked"],$_POST["subgroup"]);

					}else{
						$addResult=$this->baseInfo->place()->add_place($_POST["department"],$_POST["value"],$_POST["checked"]);
					}
					if($addResult>0){
						$placeData=$this->baseInfo->place()->find_place($addResult);
						$jsonData=array("msg"=>"success","option"=>"<option class='ubase-select' data-input='place-data' data-checked='{$placeData["place_manager"]}' value='{$placeData["place_id"]}'>{$placeData["place_name"]}</option>");
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
						$resultData=$this->baseInfo->department()->set_department($_POST["key"],$_POST["value"],$_POST['checked']);
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
					$resultData = $this->baseInfo->place()->set_place($_POST["key"],$_POST["department"],$_POST["value"],$_POST["checked"],$group);

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

					if($_POST["key"]<=2){
						echo "该角色不允许删除";
					}else{
						if ($_POST["type"]=="role"){
							$returnArray= $this->baseInfo->role()->search_role($_POST["key"]);
							if (!empty($returnArray)) {
								echo "该分组下含有其他角色，请删除角色再删除";
								return false;
							}
						}
						$delResult= $this->baseInfo->role()->del_role($_POST["key"]);
					}
					
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
			$state=array("","","");
			$state[$userData["user_state"]]='disabled="disabled"';
			$this->assign("state",$state);
			$this->assign("userData",$userData);
			$userListHtml.=$this->fetch("user/html/user_list");
		 } 

		echo json_encode(array("userhtml"=>$userListHtml,"pagehtml"=>$pageShow)) ;
		// print_r($userData);
	}
	//显示分组
	function show_group(){
		if(IS_POST){

			$resultDataArray=$this->baseInfo->group()->search_group($_POST["group_id"]);
			$subgroupHtml="<option value='0'>所有分组</option>";
			foreach ($resultDataArray as $resultData) {
				$subgroupHtml.="<option value='{$resultData["group_id"]}'>{$resultData["group_name"]}</option>";
			}

			$placetDataArray=$this->baseInfo->place()->search_place($_POST["group_id"],0);
			$placeHtml="<option value='0'>所有职位</option>";
			foreach ($placetDataArray as $placeData) {
				$placeHtml.="<option value='{$placeData["place_id"]}'>{$placeData["place_name"]}</option>";
			}

			$directorArray=$this->baseInfo->user()->show_director($_POST["group_id"]);
			$directorHtml="";
			foreach ($directorArray as $directorData) {
				$directorHtml.="<option value='{$directorData["user_code"]}'>{$directorData["user_name"]}</option>";
			}

			print_r($abcr);
			$manager=$this->user->get_manager($_POST["group_id"]);
			$json='{"group":"'.$subgroupHtml.'","place":"'.$placeHtml.'","manager":"'.$manager.'","director":"'.$directorHtml.'"}';

			echo $json;


		}
	}
	/**
	 * [show_place 显示职位]
	 * @return [type] [description]
	 */
	function show_place(){
		if(IS_POST){
			$placetDataArray=$this->baseInfo->place()->search_place($_POST["department_id"],$_POST["group_id"]);

			$placeHtml="<option value='0'>所有职位</option>";
			foreach ($placetDataArray as $placeData) {
				$placeHtml.="<option value='{$placeData["place_id"]}'>{$placeData["place_name"]}</option>";
			}


			$manager=$this->user->get_manager($_POST["department_id"],$_POST["group_id"]);

			$directorArray=$this->baseInfo->user()->show_director($_POST["department_id"],false);
			$directorHtml="";
			foreach ($directorArray as $directorData) {
				$directorHtml.="<option value='{$directorData["user_code"]}'>{$directorData["user_name"]}</option>";
			}

			$json='{"place":"'.$placeHtml.'","manager":"'.$manager.'","director":"'.$directorHtml.'"}';
			echo $json;
		}
	}
	/**
	 * [show_role description]
	 * @return [type] [description]
	 */
	function show_role(){
		if(IS_POST){
			$roleDataArray=$this->baseInfo->role()->search_role($_POST["role_upper"]);
			$roleHtml='<option value="0">所有角色</option>';
			foreach ($roleDataArray as $roleData) {
				$roleHtml.='<option value="'.$roleData["role_id"].'">'.$roleData["role_name"].'</option>';
			}

			echo $roleHtml;
		}
	}
	//取用户模板
	function get_usertemplate(){
		if(IS_POST){
			$user=D("User");
			$company=$this->baseInfo->company()->search_company();
			$department=$this->baseInfo->department()->search_department();
			$roles=$this->baseInfo->role()->search_role();
			if(isset($_POST["user_id"])){
				$resultData=$user->find_user($_POST["user_id"]);
				$this->assign("userinfo",$resultData);
				$add="false";

			}else{
				$add="true";
				$newcode=$user->get_new_code();	
				$this->assign("newcode",$newcode);
				$resultData["user_department"]=1;
				$resultData["user_group"]=1;
				$resultData["user_role"]=1;
				$thisRole["role_upper"]=1;
				
			}
		}

		$group=$this->baseInfo->group()->search_group($resultData["user_department"]);
		$place=$this->baseInfo->place()->search_place($resultData["user_department"],$resultData["user_group"]);
		$thisRole=$this->baseInfo->role()->find_role($resultData["user_role"]);
		$role=$this->baseInfo->role()->search_role($thisRole["role_upper"]);
		$directorArray=$this->baseInfo->user()->show_director($resultData["user_department"],false);
		$this->assign("groupArray",$group);
		$this->assign("placeArray",$place);
		$this->assign("roleArray",$role);
		$this->assign("add",$add);
		
		$this->assign("companyArray",$company);
		$this->assign("departmentArray",$department);
		$this->assign("directorArray",$directorArray);

		$this->assign("rolesArray",$roles);
		echo $this->fetch("userinfo");
	}

	function default_role(){
		if(IS_POST){
			switch ($_POST["type"]) {
				case 'get':
					if($_POST["place_id"]<=2){
						echo "对不起！您无权操作此角色";
					}else{
						echo $this->baseInfo->place()->find_place($_POST["place_id"])["place_role"];
					}
					# code...
					break;
				case 'put':
					if($_POST["place_role"]<=2){
						echo "对不起！您无权操作此角色";
					}else{
						echo $this->baseInfo->place()->set_place_role($_POST["place_id"],$_POST["place_role"]);
					}
					# code...
					break;
				default:
					# code...
					break;
			}
		}
	}
	/**
	 * [change_extent 改变管理层管辖的部门]
	 * @return [type] [description]
	 */
	function change_extent(){
		if(IS_POST){
			switch ($_POST["type"]) {
				case "extent":
					$placeArray=$this->baseInfo->place()->find_place($_POST["place_id"]);

					$optionalHtml="";
					$selectedHtml="";

					$condition=array();
					$condition["department_leader"]=array("EQ",0);

					
					if(!empty($placeArray["place_extent"])){
						$condition["department_id"]=array("IN",$placeArray["place_extent"]);
						$selectedArray=$this->baseInfo->department()->search_department(0,0,0,$condition);
						foreach ($selectedArray as $selecte) {
							$selectedHtml.="<option value='{$selecte["department_id"]}'>{$selecte["department_name"]}</option>";
						}
						$condition["department_id"]=array("NOT IN",$placeArray["place_extent"]);
					}

					$optionalArray=$this->baseInfo->department()->search_department(0,0,0,$condition);
					foreach ($optionalArray as $optional) {
						$optionalHtml.="<option value='{$optional["department_id"]}'>{$optional["department_name"]}</option>";
					}
					echo '{"optional":"'.$optionalHtml.'","selected":"'.$selectedHtml.'"}';


					break;
				case 'add':
					$this->baseInfo->place()->add_extent($_POST["place_id"],$_POST["place_extent"]);
					break;
				
				case 'reduce':
					$this->baseInfo->place()->reduce_extent($_POST["place_id"],$_POST["place_extent"]);
					break;
				default:
					# code...
					break;
			}
		}
	}
}

//select user_id,user_name,user_code from oa_user where user_place in (select place_id from oa_place where find_in_set(1,place_extent));