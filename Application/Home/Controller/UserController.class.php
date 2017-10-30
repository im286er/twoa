<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-05-18 15:57:50
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-04 15:46:22
 */

/*{"control":"User","name":"人事管理","icon":"fa fa-users","menus":[{"name":"新建用户","icon":"glyphicon glyphicon-user","menus":"create"},{"name":"用户列表","icon":"fa fa-users","menus":"userlist"},{"name":"档案管理","icon":"fa fa-file-archive-o","menus":"archives"},{"name":"基础信息","icon":"glyphicon glyphicon-send","menus":"ubase"},{"name":"图表统计","icon":"fa fa-bar-chart-o","menus":"charts"}]}*/
namespace Home\Controller;
use Common\Controller\AmongController;
class UserController extends AmongController {
	protected $baseInfo;//定义基本信息
	protected $user;//用户模型
	protected $archives;
	protected $weixinQy;
	//重组gethtml方法
	function __construct(){
		parent::__construct();
		$this->baseInfo=D("Info");
		$this->user=D("User");
		$this->archives=D("Archives");
		$this->weixinQy=$this->Wxqy->secret("GDTwLEDVdhiCz0ViKGOVKJZbQc3eLN9URy9ugIVJNm0");
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
			case 'charts':
				$companyData=$this->baseInfo->company()->search_company();
				$this->assign("companyDataArray",$companyData);

				$departmentData=$this->baseInfo->department()->search_department();
				$this->assign("departmentDataArray",$departmentData);
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
			if(empty($userData["user_passwd"])){
				if($_POST["type"]=="add"){
					$userData["user_passwd"]=sha1("Aa1234567");//初始化密码
				}else{
					unset($userData["user_passwd"]);
				}
			}else{
				$userData["user_passwd"]=sha1($userData["user_passwd"]);//密码加密
			}
			if($_POST["type"]!="state"){
				$deparWxId=$this->baseInfo->department()->getWxId($userData["user_department"]);
				if($userData["user_group"]==0){
					$department=$deparWxId;
				}else{
					$department=$this->baseInfo->group()->getWxId($userData["user_group"]);
				}
	
				$placeWxStr=$this->baseInfo->place()->find_place($userData["user_place"])["place_name"];
				$gender=1;
				if($userData["user_sex"]=="女"){
					$gender=2;
				}
			}
			

			switch ($_POST["type"]) {
				case 'add':
					if($userData["user_sex"]=="男"){
						$userData["user_avatar"]="/assets/avatars/man.png";
					}else if($userData["user_sex"]=="女"){
						$userData["user_avatar"]="/assets/avatars/lady.png";
					}
					if(isset($userData["user_roles"])==Null && isset($userData["user_role"])==Null){
						$userData["user_role"]=5;//这里后续需要定义
					}
					$result=$this->user->add($userData);
					if($result>0){
						if(count($userData)>3){
							$this->weixinQy->user()->createUser(array("userid"=>$userData["user_code"],"name"=>$userData["user_name"],"department"=>$department,"position"=>$placeWxStr,"mobile"=>$userData["user_phone"],"gender"=>$gender));
						}else{
							$this->weixinQy->user()->createUser(array("userid"=>$userData["user_code"],"name"=>$userData["user_name"],"gender"=>$gender));
		
						}
					}
					break;
				case 'update':
					if(isset($userData["user_roles"])==Null && isset($userData["user_role"])==Null){
						$place_role=$this->baseInfo->place()->find_place($userData["user_place"])["place_role"];
						$role=$this->baseInfo->role()->find_role($place_role);
						$userData["user_roles"]=$role["role_upper"];
						$userData["user_role"]=$role["role_id"];
					}
					$result=$this->user->set_user($userData["user_id"],$userData);
					if($result>0){
						$this->weixinQy->user()->updateUser(array("userid"=>$userData["user_code"],"name"=>$userData["user_name"],"department"=>$department,"position"=>$placeWxStr,"mobile"=>$userData["user_phone"],"gender"=>$gender));
						echo 1;
					}
					break;
				case 'state':
					$resultData=$this->user->set_state($_POST["user_id"],$_POST["user_state"]);
					
					if($resultData>0){
						$userInfo=$this->user->find_user($_POST["user_id"]);
						if($_POST["user_state"]==2){
							$this->weixinQy->user()->deleteUser($userInfo["user_code"]);
						}else{
							$this->weixinQy->user()->updateUser(array("userid"=>$userInfo["user_code"],"enable"=>$_POST["user_state"]));
						}
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
					$resultData=$this->baseInfo->company()->add_company(array("company_name"=>$_POST['value']));

					if($resultData>0){
						$newResult=$this->baseInfo->company()->find_company($resultData);
						$jsonData=array("msg"=>"success","option"=>"<option class='ubase-select' data-input='company-data' value='{$newResult["company_id"]}'>{$newResult["company_name"]}</option>");
					}else{
						$jsonData=array("msg"=>$resultData);
					}
					echo json_encode($jsonData);
				break;
				case "department"://新增部门
					$wxResult=$this->weixinQy->user()->createDepartment(array("name"=>$_POST['value'],"parentid"=>1));
					if($wxResult->errcode==0){
						$departmentData=$this->baseInfo->department()->add_department(array("department_name"=>$_POST['value'],"department_leader"=>$_POST['checked'],"department_wxid"=>$wxResult->id));
					}
					
					if($departmentData>0){
						$newResult=$this->baseInfo->department()->find_department(0,$_POST['value']);
						$jsonData=array("msg"=>"success","option"=>"<option class='ubase-select' data-input='department-data' data-type='group' data-checked='{$newResult["department_leader"]}' value='{$newResult["department_id"]}'>{$newResult["department_name"]}</option>");
					}else{
						if($wxResult->errcode==0){
							$this->weixinQy->user()->deleteDepartment($wxResult->id);
						}
						$jsonData=array("msg"=>$departmentData);
					}
					echo json_encode($jsonData);
				break;
				case "group"://新增分组
				// print_r($_POST);
				$wxId=$this->baseInfo->department()->getWxId($_POST['department']);
				$wxResult=$this->weixinQy->user()->createDepartment(array("name"=>$_POST['value'],"parentid"=>$wxId));
				if($wxResult->errcode==0){
					$groupData=$this->baseInfo->group()->add_group(array("group_department"=>$_POST['department'],"group_name"=>$_POST['value'],"group_wxid"=>$wxResult->id));	
				}
				
				if($groupData>0){
					$newResult=$this->baseInfo->group()->find_group($groupData);
					$jsonData=array("msg"=>"success","option"=>"<option class='ubase-select' data-input='group-data' value='{$newResult["group_id"]}'>{$newResult["group_name"]}</option>");
				}else{
					if($wxResult->errcode==0){
						$this->weixinQy->user()->deleteDepartment($wxResult->id);
					}
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
						$wxId=$this->baseInfo->department()->getWxId($_POST["key"]);
					}else{
						$resultData=$this->baseInfo->group()->set_group($_POST["key"],$_POST["value"],$_POST["department"]);
						$wxId=$this->baseInfo->group()->getWxId($_POST["key"]);

					}
					if($resultData>0){
						$wxInfo=array("id"=>$wxId,"name"=>$_POST["value"]);
						$this->weixinQy->user()->updateDepartment($wxInfo);
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
					// $group=D("Group");
					if($_POST["type"]=="department"){
						$wxId=$this->baseInfo->department()->getWxId($_POST["key"]);
						$delResult= $this->baseInfo->department()->del_department($_POST["key"]);
					}else{
						$wxId=$this->baseInfo->group()->getWxId($_POST["key"]);
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
				$this->weixinQy->user()->deleteDepartment($wxId);
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

		if($_POST["p"]>ceil($count/$_POST["limit"])){
			$_POST["p"]=1;
		}
		
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
			$directorHtml="<option value='0'>无</option>";
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

			$directorHtml="<option value='0'>无</option>";
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

	/**
	 * [default_role 设置默认角色]
	 * @return [type] [description]
	 */
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
	/**
	 * [has_username 判断是否存在用户]
	 * @return boolean [description]
	 */
	function has_username(){
		if(IS_POST){
			$user_id=$this->user->has_username($_POST["user_username"]);
			if($user_id>0){
				echo "error";
			}else{
				echo "success";
			}	
		}
	}
	/**
	 * [charts_data 筛选数据画图]
	 * @return [type] [description]
	 */
	function charts_data(){
		$chartsData=$this->user->table("oa_user u")->field("c.company_name user_company,count(u.user_id) count")->join("left join oa_company c on c.company_id=u.user_company")->group("u.user_company")->where("u.user_company in ({$_POST["postData"]})")->select();
		echo json_encode($chartsData);
	}

	/*下列是档案管理涉及到的方法*/

	/**
	 * [getCreateArch 获取档案模板]
	 * @return [type] [description]
	 */
	function getCreateArch(){
		if(IS_POST){
			if(isset($_POST["user_id"])){
				$add="false";
			}else{
				$add="true";
			}
			$this->assign("add",$add);
			echo $this->fetch("archiveinfo");
		}
	}

	/**
	 * [nameTransform 通过编码获取用户姓名]
	 */
	function nameTransform(){
		if(IS_POST){
			echo $this->user->nameTrans($_POST["user_code"],3)["user_name"];
		}
	}
	/**
	 * [archiveinfoive 添加档案]
	 * @return [type] [description]
	 */
	function conArchives(){
		if(IS_POST){
			$filePath=$_POST["filePath"];
			$archivesData=$_POST["data"];
			$user_name=$this->user->nameTrans($archivesData["archives_usercode"],3)["user_name"];
			if(empty($user_name)){
				echo "error";
				return false;
			}
			if(!$this->archives->has_auth("update")){
				return false;
			}
			foreach ($_POST["files"] as $name => $blobData) {
				$dirFile=explode("_", $name);
				$archivesData[$name]=blob_to_file($blobData,$archivesData["archives_usercode"],$filePath.$dirFile[1]);
			}
			switch ($_POST["type"]) {
				case 'insert':
					echo $this->archives->add_archive($archivesData);
					break;
				case 'update':
					echo $this->archives->setArchive($archivesData["archives_usercode"],$archivesData);
					# code...
					break;
				default:
					# code...
					break;
			}
		}
	}
	/**
	 * [search_archives 档案查询并返回]
	 * @return [type] [description]
	 */
	function search_archives(){
		$archives=D("Archives");
		$count=$archives->count();
		$condition=array();
		foreach ($_POST["condition"] as $key => $value) {
			if ($value["value"]!=""){
				if($value["name"]=="user_state"){
					$condition["u.".$value["name"]]=array("EQ","{$value["value"]}");
				}else if($value["name"]=="archives_idexp"){
					// echo  $value;
					$condition[$value["name"]]=array("EXP","<=date_sub(now(),interval {$value["value"]})");
				}else{
					$condition["u.".$value["name"]]=array("LIKE","%{$value["value"]}%");
				}
				
			}
		}
		$count=$archives->join("left join oa_user u on u.user_code=oa_archives.archives_usercode")->where($condition)->count();


		if($_POST["p"]>ceil($count/$_POST["limit"])){
			$_POST["p"]=1;
		}
		$Page=new \Think\Page($count,$_POST["limit"]);

		$pageShow=$Page->show();

		$archivesDataArray=$archives->search_all($Page->firstRow,$Page->listRows,$condition);
		// echo $archives->getLastSql();
		$archiveListHtml="";
		
		foreach ($archivesDataArray as $archivesData) {
			$this->assign("archivesData",$archivesData);
			$archiveListHtml.=$this->fetch("user/html/archive_list");
		 } 

		echo json_encode(array("userhtml"=>$archiveListHtml,"pagehtml"=>$pageShow)) ;
		// print_r($userData);
	}
	function uploadFile(){
		if(IS_POST){
			$dirFile=explode("_", $_POST["key"]);
			// echo $_POST["filePath"];
			$user_name=$this->user->nameTrans($_POST["data"]["archives_usercode"],3)["user_name"];
			echo "名字".$user_name."名字";
			blob_to_file($_POST["data"],$_POST["name"],$_POST["filePath"].$dirFile[1]);
		}
	}

	function get_archivetemplate(){
		if(IS_POST){
			$archives=D("Archives");

			if(isset($_POST["archives_id"])){
				$archiveData=$archives->find_archive($_POST["archives_id"]);
				$this->assign("archiveData",$archiveData);
				$add="false";

			}else{
				$add="true";
				
			}
		}

		// $group=$this->baseInfo->group()->search_group($resultData["user_department"]);
		// $place=$this->baseInfo->place()->search_place($resultData["user_department"],$resultData["user_group"]);
		// $thisRole=$this->baseInfo->role()->find_role($resultData["user_role"]);
		// $role=$this->baseInfo->role()->search_role($thisRole["role_upper"]);
		// $directorArray=$this->baseInfo->user()->show_director($resultData["user_department"],false);
		// $this->assign("groupArray",$group);
		// $this->assign("placeArray",$place);
		// $this->assign("roleArray",$role);
		// $this->assign("add",$add);
		
		// $this->assign("companyArray",$company);
		// $this->assign("departmentArray",$department);
		// $this->assign("directorArray",$directorArray);

		// $this->assign("rolesArray",$roles);
		echo $this->fetch("archiveinfo");
	}
	/**
	 * Undocumented function 从企业微信里获取用户列表
	 *
	 * @return void
	 */
	function fromWx(){
		$contacts=$this->weixinQy->user()->userList(1);
	
		if($contacts->errcode==0){
			foreach ($contacts->userlist as $wxUser) {
				$userInfoA=array();
				$userInfo=$this->user->nameTrans($wxUser->userid,3);
				$userInfoA["user_code"]=$wxUser->userid;
				$userInfoA["user_name"]=$wxUser->name;
				if($wxUser->department[0]>1){
					$groupId=$this->baseInfo->group()->wxIdTrans($wxUser->department[0]);
					if($groupId>0){
						$userInfoA["user_department"]=$groupId;
						$groupInfo=$this->baseInfo->group()->find_group($groupId);
						$departmentId=$groupInfo["group_department"];
					}else{
						$departmentId=$this->baseInfo->department()->wxIdTrans($wxUser->department[0]);
						$userInfoA["user_department"]=$departmentId;
					}
					if($wxUser->position!=""){
						$userInfoA["user_place"]=$this->baseInfo->place()->seekPlace(array("place_name"=>$wxUser->position,"place_department"=>$departmentId,"place_group"=>$groupId))["place_id"];
					}
					
				}
				
				$userInfoA["user_phone"]=$wxUser->mobile;
				$userInfoA["user_sex"]="男";
				if($wxUser->gender>1){
					$userInfoA["user_sex"]="女";
				}
				if($wxUser->status==4){
					$wxUser->status=0;
				}
				$userInfoA["user_avatar"]=$wxUser->avatar;
				$userInfoA["user_state"]=$wxUser->status;

				if($userInfo){
					//用户存在则修改
					$result=$this->user->set_user($userInfo["user_id"],$userInfoA);
				}else{
					$result=$this->user->addUser($userInfoA);
					$userInfoA["user_passwd"]=SHA1("Aa1234567");//设置默认密码
					//用户不存在则新增
				}
			}
		}
		echo true;
	}

}

