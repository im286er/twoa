<?php
namespace Home\Controller;
use Common\Controller\AmongController;
class IndexController extends AmongController {
	
	//获取员工姓名
	function getcode(){
		if(IS_POST){
			if($this->selfUser["user_code"]!=1){
				echo $this->selfUser["user_name"];
			}
		}
	}
	//验证手机
	function checkphone(){
		if(IS_POST && session("is_register")){
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
				echo $captcha;
				/*这里涉及到账号配置，暂时保密*/	
				// $sms->setOption('','','','')->send($_POST["data"]["user_phone"],"您的手机号码正在注册集团会员信息，注册验证码是{$captcha}，如非本人操作请及时通知管理员。");
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
		
		if(IS_POST && session("is_register")){

			$this->assign("userIdData",$this->selfUser["user_id"]);

			$companyData=$this->baseInfo->company()->search_company();
			$this->assign("companyDataArray",$companyData);

			$departmentData=$this->baseInfo->department()->search_department();
			$this->assign("departmentDataArray",$departmentData);

			$placeData=$this->baseInfo->place()->search_place();
			$this->assign("user_place",$placeData);

			$roleData=$this->baseInfo->role()->search_role();
			$this->assign("role_group",$roleData);

			$allRole=$this->baseInfo->role()->search_role(0,true);
			$this->assign("allRoleArray",$allRole);
			$this->assign("todayTime",time());


			echo $this->fetch("reg");
		}
	}


}
