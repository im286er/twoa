<?php
namespace Home\Controller;
use Think\Controller;
class AmangController extends Controller {
	public function _initialize(){
		$oa_login=session("oa_islogin");
		if(empty($oa_login)){
			$url=U("index/index");
			echo "<script>top.location.href='$url'</script>";exit;
		}else{
			$user=M("oa_user u");
			$userData=$user->field("user_name,user_code,c.config_value user_company,g.config_value user_group,p.config_value user_place,r.config_value user_role,user_higher,user_phone,user_avatar,user_born,user_sex,user_lastlogin,user_entry,user_login,user_state")->where("user_name='".session("oa_user_name")."' AND  u.user_company=c.config_key AND u.user_group=g.config_key AND u.user_place=p.config_key AND u.user_role=r.config_key AND c.config_class='company' AND g.config_class='group' AND p.config_class='place' AND r.config_class='role'")->join("oa_config c,oa_config g,oa_config p,oa_config r")->find();
			$userData["user_age"]=get_age($userData["user_born"]);
			$userData["user_joinDay"]=get_day($userData["user_entry"]);
			$this->assign("user",$userData);
		}
	}

}