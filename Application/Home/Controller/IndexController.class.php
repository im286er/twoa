<?php
namespace Home\Controller;
use Common\Controller\AmongController;
class IndexController extends AmongController {
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
				vendor('Sms.Sms');//引入Sms
				$sms = new \Sms("E讯通");
				$captcha=rand(111111,999999);
				session("captcha",sha1($captcha));
				session("captchaExpire",time()+600);
				/*这里涉及到账号配置，暂时保密*/	
				$sms->setOption('','','','')->send($_POST["data"]["user_phone"],"您的手机号码正在注册集团会员信息，注册验证码是{$captcha}，如非本人操作请及时通知管理员。");
			}else if($_POST["type"]=="checkmsg"){
				if(time()>=session("captchaExpire")){
					echo "timeout";
				}else{
					if(sha1($_POST["data"]["captcha"])==session("captcha")){
						echo "success";
					}else{
						echo "error";
					}
				}
				

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
