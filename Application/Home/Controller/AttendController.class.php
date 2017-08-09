<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-03 16:43:53
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-09 13:01:52
 */

/*{"control":"Attend","name":"考勤管理","icon":"fa fa-calendar","menus":[{"name":"考勤配置","icon":"fa fa-gear","menus":"config"},{"name":"考勤申请","icon":"fa fa-list-alt","menus":"userlist"},{"name":"申请管理","icon":"fa fa-pencil-square","menus":"archives"},{"name":"打卡","icon":"fa fa-square","menus":"arch"}]}*/
namespace Home\Controller;
use Common\Controller\AmongController;
class AttendController extends AmongController {
	//重组gethtml方法
	function __construct(){
		parent::__construct();
		$this->baseInfo=D("Info");
		$this->user=D("User");
		$this->acheckin=D("Attend_checkin");
		$this->config=D("Config");
		// parent::_initialize();
	}
	/**
	 * [checkin 打卡页面]
	 * @return [type] [description]
	 */
	public function checkin(){
		$date=date("Y-m-d",time());
		if(IS_AJAX){
			$date=date("Y-m-d",strtotime(I("thisDay")));
		}

		$normalCheckin=$this->checkinType(session("oa_user_code"),1,$date);
		$outCheckin=$this->checkinType(session("oa_user_code"),2,$date);
		$overtimeCheckin=$this->checkinType(session("oa_user_code"),3,$date);
		if(IS_AJAX){
			// echo $this->acheckin->getLastSql();
			$this->ajaxReturn(array("normalCheckin"=>$normalCheckin,"outCheckin"=>$outCheckin,"overtimeCheckin"=>$overtimeCheckin));
		}
		
		$this->assign("normalCheckin",$normalCheckin);
		$this->assign("outCheckin",$outCheckin);
		$this->assign("overtimeCheckin",$overtimeCheckin);
		$this->assign("user_code",$this->selfUser["user_code"]);
		$this->assign("user_name",$this->selfUser["user_name"]);
		$this->assign("SignPackage",$this->Wxqy->jssdk()->GetSignPackage());
		$this->display("checkin");
	}

	/**
	 * [checkinType 返回不同状态的按钮]
	 * @param  [type] $user_code [用户code]
	 * @param  [type] $type      [类型：1上下班，2外勤，3加班]
	 * @return [type]            [description]
	 */
	private function checkinType($user_code,$type,$date){
		$info=array(array(),array("上班","下班","fa-sun-o","fa-moon-o"),array("开始外勤","结束外勤","fa-sign-out","fa-circle-o-notch"),array("开始加班","结束加班","fa-clock-o","fa-hand-peace-o"));

		$butInfo=array("<button class='btn disabled' data-type='{$type}' data-timetype='1'><i class='ace-icon fa {$info[$type][2]} align-top bigger-125'></i>{$info[$type][0]} </button><button  class='btn disabled' data-type='{$type}' data-timetype='2'><i class='ace-icon fa {$info[$type][3]} align-top bigger-125'></i>{$info[$type][1]}</button>","<button class='btn disabled' data-type='{$type}' data-timetype='1'><i class='ace-icon fa {$info[$type][2]} align-top bigger-125'></i>{$info[$type][0]}</button><button data-toggle='button' class='btn btn-success' data-type='{$type}' data-timetype='2'><i class='ace-icon fa {$info[$type][3]} align-top bigger-125'></i>{$info[$type][1]}</button>","<button data-toggle='button' class='btn btn-success' data-type='{$type}' data-timetype='1'><i class='ace-icon fa {$info[$type][2]} align-top bigger-125'></i>{$info[$type][0]}</button><button class='btn disabled' data-type='{$type}' data-timetype='2'><i class='ace-icon fa {$info[$type][3]} align-top bigger-125'></i>{$info[$type][1]}</button>");

		if($type>=2){
			$aapplyTable=M("oa_attend_apply");
			$aapplyData=$aapplyTable->where("aapply_code='{$user_code}' AND aapply_type='{$type}' AND aapply_schedule='{$date}'")->find();
			if(!$aapplyData["aapply_id"]){
				return $butInfo[0]; 
			}
		}
		
		$checkinData=$this->acheckin->field("acheckin_checkinway,acheckin_type,acheckin_timetype")->where("date_format(acheckin_checkintime,'%Y-%m-%d')='{$date}' AND acheckin_code='{$user_code}' AND acheckin_type='{$type}'")->select();
		if(count($checkinData)>0){
			if(count($checkinData)>1){
				return $butInfo[0];
			}else{
				return $butInfo[1];
			}
		}else{
			return $butInfo[2];
		}

	}

	/**
	 * [getLocation 根据经纬度获取地址]
	 * @param  [type] $latitude  [latitude]
	 * @param  [type] $longitude [longitude]
	 * @param  [type] $range 	 [默认不做范围限制]
	 * @return [type]            [description]
	 */
	public function getPosition($latitude=0,$longitude=0,$range=false){
		$positions=array(array("minLat"=>23.1313,"maxLat"=>23.1319,"minLong"=>113.274,"maxLong"=>113.2755));
		if($latitude==0 || $longitude==0){
			$latitude=I("latitude");
			$longitude=I("longitude");
		}

		$getLocation=true;
		if($range==true){
			$getLocation=false;
			foreach ($position as $position) {
				if($latitude>$position["minLat"] && $latitude< $position["maxLat"] && $longitude >$position["minLong"] && $longitude < $position["maxLong"]){
					$getLocation=true;
					break;
				}
			}
		}
		
		if($getLocation==true){
			$xmlstr=file_get_contents("http://apis.map.qq.com/ws/geocoder/v1/?location={$latitude},{$longitude}&key=V6EBZ-4EN35-7OHIH-QDJTA-KNYBO-IHFFN");
			$objPosition=json_decode($xmlstr);
			$Position=$objPosition->result->address;
			echo json_encode(array("success"=>"1","msg"=>$Position));
		}else{
			echo json_encode(array("success"=>"0","msg"=>"抱歉！坐标不在公司范围，请使用拍照"));
		}
		
	}


	function submit_checkin(){
		if(IS_AJAX){
		// print_r($_POST);
			$checkinData=$_POST["data"];
			$checkinData["acheckin_addtime"]=date("Y-m-d H:i:s",time());
			switch ($_POST["data"]["acheckin_checkinway"]) {
				case "1":
					# code...
					$checkinData["acheckin_checkintime"]=date("Y-m-d H:i:s",time());
					$result=$this->acheckin->add($checkinData);
					if($result>0){
						$this->ajaxReturn(array("success"=>"1","msg"=>$result));
					}else{
						$this->ajaxReturn(array("success"=>"0","msg"=>"打卡失败，请联系管理员"));
					}
					
					break;
				case "2":
					// print_r($checkinData);
					if(!is_dir("Public/images/upload/checkin/")){
							mkdir("Public/images/upload/checkin/");
						}

					$result=$this->Wxqy->download($checkinData["acheckin_picture"]);
					$return=array("success"=>"0","msg"=>"打卡失败，请联系管理员");
					if($result!=false){
						$pictureName=$checkinData["acheckin_code"]."-".date("Y-m-d[His]",time()).".".$result["type"];
						$picture=fopen("Public/images/upload/checkin/".$pictureName, "w+");
						$result= fwrite($picture, $result["content"]);
						fclose($picture);
						if($result>0){
							$checkinData["acheckin_picture"]=$pictureName;
							$result=$this->acheckin->add($checkinData);
							if($result>0){
								$return= array("success"=>"1","msg"=>$result);
							}
						}
					}
					$this->ajaxReturn(array("success"=>"0","msg"=>"打卡失败，请联系管理员"));
					# code...
					break;
				default:
					# code...
					break;
			}
		}
	}
	

}
