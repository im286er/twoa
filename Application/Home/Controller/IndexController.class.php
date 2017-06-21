<?php
namespace Home\Controller;
use Common\Controller\AmangController;
class IndexController extends AmangController {
	//获取员工编码
	function getcode(){
		if(IS_POST){
			$user=M("oa_user");
			$userData=$user->where($_POST)->find();
			// print_r($userData);
			if(!empty($userData["user_id"])){
				if($userData["user_state"]!=1){
					session("reg_code",$_POST["user_code"]);
					echo $userData["user_name"];
				}
			}
		}
	}
	//验证手机
	function checkphone(){
		if(IS_POST && session("reg_code")){
			if($_POST["type"]=="checkphone"){
				$user=M("oa_user");
				$userData=$user->field("user_id")->where($_POST["data"])->find();
				if(empty($userData)){
					/*手机号码可以用*/
					echo "success";
				}else{
					/*手机号码已存在*/
					echo "error";
				}
			}else if($_POST["type"]=="getmsg"){
				echo "send msg";
			}else if($_POST["type"]=="checkmsg"){

			}
		}
	}
	//对注册页的信息进行初始化
	function getreg(){
		if(IS_POST && session("reg_code")){
			$config=M("oa_config");
			$companyData=$config->field("config_key,config_value")->where("config_class='company'")->select();
			$this->assign("user_companys",$companyData);

			$group=M("oa_group g");
			$groupData=$group->field("group_id,group_name")->select();
			$this->assign("user_groups",$groupData);

			$subgroup=M("oa_subgroup");
			$subgroupData=$subgroup->field("subgroup_id,subgroup_name")->select();
			$this->assign("user_subgroups",$subgroupData);

			$place=M("oa_place");
			$placeData=$place->field("place_id,place_name")->select();
			$this->assign("user_place",$placeData);

			echo $this->fetch("reg");
		}
	}

}