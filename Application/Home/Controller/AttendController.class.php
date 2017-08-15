<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-03 16:43:53
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-10 15:01:00
 */

/*{"control":"Attend","name":"考勤管理","icon":"fa fa-calendar","menus":[{"name":"考勤配置","icon":"fa fa-gear","menus":"config"},{"name":"考勤申请","icon":"fa fa-list-alt","menus":"userlist"},{"name":"申请管理","icon":"fa fa-pencil-square","menus":"archives"},{"name":"打卡","icon":"fa fa-square","menus":"checkin"}]}*/
namespace Home\Controller;
use Common\Controller\AmongController;
class AttendController extends AmongController {
	public $MonthRec;
	public $timeNode;
	public $attendUser;
	//重组gethtml方法
	function __construct(){
		parent::__construct();
		$this->baseInfo=D("Info");
		$this->user=D("User");
		$this->auser=D("Attend_user");
		$this->acheckin=D("Attend_checkin");
		$this->aapply=D("Attend_apply");
		$this->arecord=D("Attend_record");
		$this->config=D("Config");
		$this->attendUser=$this->auser->find_auser($this->selfUser["user_code"]);
		
		$this->timeNode=array("MO"=>"09:00:00","MF"=>"12:00:00","AO"=>"13:00:00","AF"=>"18:00:00");

		if($this->attendUser["auser_eachday"]>7.5){
			$this->timeNode["AF"]="18:30:00";
		}
		/*每天的时间节点*/
		
		$this->MonthRec=$this->arecord->getMonthRec($this->selfUser["user_code"],date("Y"),date("m"));
		
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

		

		$this->settleCheckin($this->selfUser["user_code"],1,date("Y-m-d",strtotime("2017-8-14")));

		$normalCheckin=$this->checkinType($this->selfUser["user_code"],1,$date);
		$outCheckin=$this->checkinType($this->selfUser["user_code"],2,$date);
		$overtimeCheckin=$this->checkinType($this->selfUser["user_code"],3,$date);
		if(IS_AJAX){
			/*获取不同按钮状态*/
			$this->ajaxReturn(array("normalCheckin"=>$normalCheckin,"outCheckin"=>$outCheckin,"overtimeCheckin"=>$overtimeCheckin));
		}
		
		$this->assign("normalCheckin",$normalCheckin);
		$this->assign("outCheckin",$outCheckin);
		$this->assign("overtimeCheckin",$overtimeCheckin);
		$this->assign("user_code",$this->selfUser["user_code"]);
		$this->assign("user_name",$this->selfUser["user_name"]);
		$this->assign("SignPackage",$this->Wxqy->jssdk()->GetSignPackage());
		$this->gethtml("checkin");

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
		/*这里加上判断外勤是否属于全天，如果是，正常上下班按钮禁止*/
		

		if($type>=2){
			/*状态是外勤或者加班*/
			$aapplyData=$this->aapply->seekApply($user_code,$type,$date);	

			if(!$aapplyData["aapply_id"]){
				return $butInfo[0]; 
			}
		}
		
		$checkinData=$this->acheckin->seekCheckin($user_code,$type,$date);
		if(count($checkinData)>0){
			if(count($checkinData)>1){
				return $butInfo[0];
			}else{
				return $butInfo[1];
			}
		}else{
			$dates=split("-", $date);
			$thisDay=$this->arecord->isWeekday($dates[0],$dates[1],$dates[2]);
			/*判断是否工作日*/
			if($thisDay==false){
				return $butInfo[0];
			}else{
				return $butInfo[2];
			}
			
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
		if($latitude==0 && $longitude==0){
			$latitude=I("latitude");
			$longitude=I("longitude");
			$range=I("range");
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

	/**
	 * [submit_checkin 提交打打卡信息]
	 * @return [type] [description]
	 */
	function submit_checkin(){
		if(IS_AJAX){
		// print_r($_POST);
			$checkinData=$_POST["data"];
			$checkinData["acheckin_addtime"]=date("Y-m-d H:i:s",time());
			/**
			 * 1,定位打卡，2拍照打卡
			 */
			switch ($_POST["data"]["acheckin_checkinway"]) {

				case "1":
					# code...
					$checkinData["acheckin_checkintime"]=date("Y-m-d H:i:s",time());

					$result=$this->acheckin->checkin($checkinData);
					if($result>0){
						$this->ajaxReturn(array("success"=>"1","msg"=>$result));
					}else{
						$this->ajaxReturn(array("success"=>"0","msg"=>"打卡失败，请联系管理员"));
					}
					
					break;
				case "2":
					if(!is_dir("Public/images/upload/checkin/")){
							mkdir("Public/images/upload/checkin/");
						}
					/**
					 * [download 通过微信接口下载临时图片]
					 */
					$downloadResult=$this->Wxqy->download($checkinData["acheckin_picture"]);
					$return=array("success"=>"0","msg"=>"打卡失败，请联系管理员");
					if($downloadResult!=false){
						$pictureName=$checkinData["acheckin_code"]."-".date("Y-m-d[His]",time()).".".$downloadResult["type"];
						$picture=fopen("Public/images/upload/checkin/".$pictureName, "w+");
						$result= fwrite($picture, $downloadResult["content"]);
						fclose($picture);
						if($result>0){
							$checkinData["acheckin_picture"]=$pictureName;
							$result=$this->acheckin->add($checkinData);
							if($result>0){
								$return= array("success"=>"1","msg"=>$result);
							}
						}
					}
					$this->ajaxReturn($return);
					# code...
					break;
				default:
					# code...
					break;
			}
		}
	}
	/**
	 * settleCheckin 计算考勤
	 *
	 * @param [type] $user_code
	 * @param [type] $type
	 * @param [type] $date
	 * @return void
	 */
	function settleCheckin($user_code,$type,$date){
		
		// $checkinData=$this->acheckin->seekCheckin($user_code,$type,$date);
		$dates=split("-", $date);/*分解成年月日*/
		$checkinData=$this->acheckin->seekCheckin($user_code,$type,$date,0);
		
		if(count($checkinData)==2 && $checkinData[0]["acheckin_timetype"]==1 && $checkinData[1]["acheckin_timetype"]==2){
			/**
			 * 当打卡记录=2，第一条记录是开始，第二条记录是结束的时候执行下列结算，不同打卡类型计算不同
			 */
			switch ($type) {
				/**
				 * 正常加班
				 */
				case '1':
						$forenoon=time_reduce($this->morningTime($checkinData[0]["acheckin_checkintime"]),$date." ".$this->timeNode["MF"]);
						$afternoon=time_reduce($date." ".$this->timeNode["AO"],$checkinData[1]["acheckin_checkintime"]);

						print_r($this->MonthRec[(int)$dates[2]]);

						return;
						/*需要加一个对外勤进行判断，直接获取现有的时间，判断，
							如果上午：外勤时间>=2，那么上午直接为3
									 else 外勤时间外勤时间+正常时间>=3，那么上午直接3
									 else 上午时间  外勤时间+正常时间
							如果下午：外勤时间>=4，那么下午直接为5
									 else 外勤时间+正常时间>5,那么下午直接5
									 else 下午时间 外勤时间+正常时间
						*/
						if(($forenoon+$afternoon)>($this->attendUser["auser_eachday"]+0.5)){
							$afternoon=($this->attendUser["auser_eachday"]+0.5)-$forenoon;
						}

						$dayTime=$forenoon+$afternoon;
						/*开始修改json数据*/
						$this->MonthRec[(int)$dates[2]]=array("forenoon"=>array("type"=>$type,"worktime"=>$forenoon),"afternoon"=>array("type"=>$type,"worktime"=>$afternoon));
						$this->updateMonthRec($user_code,$dates,$type,$dataArray,$checkinData,$dayTime);
									
					break;
					/*外勤*/
				case "2":
					$startTime= $checkinData[0]["acheckin_checkintime"];
					$endTime= $checkinData[1]["acheckin_checkintime"];
					/*外勤开始时间小于13:00 且 外勤结束时间小于13:00 ，表示上午外勤*/
					if($startTime<$date." ".$this->timeNode["AO"] && $endTime<$date." ".$this->timeNode["AO"]){


						$forenoon=time_reduce($this->morningTime($startTime),$endTime);
						$this->MonthRec[(int)$dates[2]]["forenoon"]["type"]=$type;
						$this->MonthRec[(int)$dates[2]]["forenoon"]["worktime"]=$forenoon;
						// print_r($this->MonthRec[(int)$dates[2]]);
						$this->updateMonthRec($user_code,$dates,$type,$checkinData,$forenoon);

					/*外勤开始时间大于12:00 且 外勤结束时间大于12:00 ，表示下午外勤*/
					}else if($startTime>$date." ".$this->timeNode["MF"] && $endTime>$date." ".$this->timeNode["MF"]){

						$afternoon=time_reduce($startTime,$endTime);;
						$this->MonthRec[(int)$dates[2]]["afternoon"]["type"]=$type;
						$this->MonthRec[(int)$dates[2]]["afternoon"]["worktime"]=$afternoon;
						$this->updateMonthRec($user_code,$dates,$type,$checkinData,$afternoon);

					/*剩下的表示全天外勤了*/
					}else{
						$forenoon=time_reduce($this->morningTime($startTime),$date." ".$this->timeNode["MF"]);
						$afternoon=time_reduce($date." ".$this->timeNode["AO"],$endTime);
						$this->MonthRec[(int)$dates[2]]=array("forenoon"=>array("type"=>$type,"worktime"=>$forenoon),"afternoon"=>array("type"=>$type,"worktime"=>$afternoon));
						$dayTime=$forenoon+$afternoon;
						$this->updateMonthRec($user_code,$dates,$type,$checkinData,$dayTime);
					}
					break;
				case "3":
				/**
				 * 加班
				 */

					break;
				default :
					# code...
					break;
			}
		}
	}

	/**
	 * 对早上时间进行判断 function
	 *
	 * @param [type] $startTime
	 * @return void
	 */
	private function morningTime($startTime){
		$date=split(" ",$startTime);
		$MO=$date[0]." ".$this->timeNode["MO"];
		/*这里需要增加一个判断是否加早班*/
		if($startTime<$MO){
			return $MO;
		}else{
			return $startTime;
		}
	}
	/**
	 * updateMonthRec function
	 *
	 * @param [str] $dates 日期   	  '2017-08-15'
	 * @param [int] $type 更新的类型，   1正常上下班，2外勤，3加班
	 * @param [array] $checkinData		打卡记录两条的数据
	 * @param [float] $dayTime			累积的时间
	 * @return void
	 */
	private function updateMonthRec($user_code,$dates,$type,$checkinData,$dayTime){
		
		/*启动事物*/
		$this->acheckin->startTrans();
		/*修改打卡记录的状态，防止打卡记录重复使用*/
		foreach ($checkinData as $checkins) {
			$this->acheckin->setCheckin($checkins["acheckin_id"],array("acheckin_state"=>"1"));
		}
		// $this->arecord->startTrans();
		/*开始修改月数据*/
		$setMonthResult=$this->arecord->setMonthRec($user_code,$dates[0],$dates[1],json_encode($this->MonthRec),$dayTime);
		/*判断是否执行成功，是，提交事务，否回滚*/
		if($setMonthResult>0){
			$this->acheckin->commit();
		}else{
			$this->acheckin->rollback();
		}
	}
	

}
