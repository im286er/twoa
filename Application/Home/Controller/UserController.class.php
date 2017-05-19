<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends AmangController {
	//新建用户
	// public function create(){
	// 	$this->display("create");
	// }
	// //用户列表
	// public function lists(){
	// 	$this->display("list");
	// }
	//重组gethtml方法
	public function gethtml(){
		if (I("html")=='list'){
			$user=M("oa_user u");
			$userData=$user->field("user_name,user_code,c.config_value user_company,g.config_value user_group,p.config_value user_place,r.config_value user_role,user_higher,user_phone,user_avatar,user_born,user_sex,user_lastlogin,user_entry,user_login,user_state")->where("u.user_company=c.config_key AND u.user_group=g.config_key AND u.user_place=p.config_key AND u.user_role=r.config_key AND c.config_class='company' AND g.config_class='group' AND p.config_class='place' AND r.config_class='role'")->join("oa_config c,oa_config g,oa_config p,oa_config r")->select();
			$this->assign("userlist",$userData);
		}else if(I("html")=='create'){
			$user=M("oa_user u");
			$userData=$user->field("user_code")->order("user_code DESC")->find();
			$this->assign("user_code",$userData["user_code"]+1);
			$config=M("oa_config");
			$companyData=$config->field("config_key,config_value")->where("config_class='company'")->select();
			$this->assign("user_companys",$companyData);
			$groupData=$config->field("config_key,config_value")->where("config_class='group'")->select();
			$this->assign("user_groups",$groupData);
			$roleData=$config->field("config_key,config_value")->where("config_class='role'")->select();
			$this->assign("user_roles",$roleData);

		}else if(I("html")=='edit'){

		}
		parent::gethtml();
	}
	//post data
	public function create(){
		if(IS_POST){
			
		}
		print_r($_POST);
	} 
}