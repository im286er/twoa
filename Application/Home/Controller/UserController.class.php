<?php
/*用户功能{list|用户列表,create|新建用户,edit|编辑用户}*/
namespace Home\Controller;
use Common\Controller\AmangController;
class UserController extends AmangController {
	//重组gethtml方法
	public function gethtml(){
		if (I("html")=='list'){
			$user=M("oa_user u");
			$userData=$user->field("user_name,user_code,c.config_value user_company,g.group_name user_group,p.place_name user_place,r.role_name user_role,user_higher,user_phone,user_avatar,user_born,user_sex,user_lastlogin,user_entry,user_login,user_state")->where("u.user_company=c.config_key AND u.user_group=g.group_id AND u.user_place=p.place_id AND u.user_role=r.role_id AND c.config_class='company'")->join("oa_config c,oa_group g,oa_place p,oa_role r")->select();
			$this->assign("userlist",$userData);
		}else if(I("html")=='create'){
			$user=M("oa_user u");
			$userData=$user->field("user_code")->order("user_code DESC")->find();
			$this->assign("user_code",$userData["user_code"]+1);
			$config=M("oa_config");
			$companyData=$config->field("config_key,config_value")->where("config_class='company'")->select();
			$this->assign("user_companys",$companyData);

			$group=M("oa_group g");
			$groupData=$group->field("group_id,group_name")->select();
			$this->assign("user_groups",$groupData);

			$place=M("oa_place");
			$placeData=$place->field("place_id,place_name")->select();
			$this->assign("user_place",$placeData);

			$role=M("oa_role");
			$roleData=$role->field("role_id,role_name")->order("role_id ASC")->select();
			$this->assign("user_roles",$roleData);

		}else if(I("html")=='edit'){

		}
		parent::gethtml();
	}
	//新建用户
	public function create(){
		if(IS_POST){
			$user=M("oa_user");
			$userData=$_POST;
			$userData["user_passwd"]=sha1("Aa1234567");//初始化密码
			if($userData["user_sex"]=="男"){
				$userData["user_avatar"]="/assets/avatars/man.png";
			}else{
				$userData["user_avatar"]="/assets/avatars/lady.png";
			}	
			$userData["user_quit"]="0000-00-00";
			$result=$user->add($userData);
			var_dump($result);
		}
		
	} 
}