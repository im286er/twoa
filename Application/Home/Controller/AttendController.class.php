<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-03 16:43:53
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-10 15:01:00
 */

/*{"control":"Attend","name":"考勤管理","icon":"fa fa-calendar","menus":[{"name":"考勤配置","icon":"fa fa-gear","menus":"config"},{"name":"考勤申请","icon":"fa fa-list-alt","menus":"apply"},{"name":"申请管理","icon":"fa fa-pencil-square","menus":"archives"},{"name":"打卡","icon":"fa fa-square","menus":"checkin"}]}*/
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
		
		$this->timeNode=array("MO"=>"09:00:00","MF"=>"12:00:00","AO"=>"13:30:00","STA"=>"00:00:00","AF"=>"18:00:00","END"=>"23:59:59");

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

		
		/*测试各种考勤审计*/
		// $test=$this->acheckin->isOverTime($this->selfUser["user_code"],"2017-08-18");
		// print_r($test);
		// return;
		// echo date("Y-m-d",strtotime("2017-8-14 +1 day"));
		
		// $this->settleCheckin($this->selfUser["user_code"],3,date("Y-m-d",strtotime("2017-8-21")));
		// return ;
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

		$dates=split("-", $date);/*分解成年月日*/
		$this->MonthRec=$this->arecord->getMonthRec($this->selfUser["user_code"],$dates[0],$dates[1]);
		/*判断哪些类型不需要涉及日期*/
		if(in_array($type,array("3"))){
			$checkinData=$this->acheckin->seekCheckin($user_code,$type,null);
			$overTime=$this->acheckin->isOverTime($user_code,$date);
			$checkinData=$this->acheckin->applySeekCheckin($overTime[0]);

		}else{
			$checkinData=$this->acheckin->seekCheckin($user_code,$type,$date);
		}
		if(count($checkinData)==2 && $checkinData[0]["acheckin_timetype"]==1 && $checkinData[1]["acheckin_timetype"]==2){

			/**
			 * 当打卡记录=2，第一条记录是开始，第二条记录是结束的时候执行下列结算，不同打卡类型计算不同
			 */
			$startTime= $this->loadStartTime($checkinData[0]["acheckin_checkintime"]);
			$endTime= $this->loadEndTime($checkinData[1]["acheckin_checkintime"]);
		
			switch ($type) {
				/**
				 * 正常上班班
				 */
				case '1':
						$foreAfter=$this->getForeAfter($startTime,$endTime,$date,$type);
						$tempRec[$dates[0]][$dates[1]][$dates[2]]=$foreAfter["rec"];
						$forenoon=$foreAfter["forenoon"];
						$afternoon=$foreAfter["afternoon"];

						/*需要加一个对外勤进行判断，直接获取现有的时间，判断，
							如果上午：外勤时间>=2，那么上午直接为3
									 else 外勤时间外勤时间+正常时间>=3，那么上午直接3
									 else 上午时间  外勤时间+正常时间
							如果下午：外勤时间>=4，那么下午直接为5
									 else 外勤时间+正常时间>5,那么下午直接5
									 else 下午时间 外勤时间+正常时间
						*/
						$outIsApply=$this->aapply->isApply($user_code,2,$date);//外勤申请
						/*判断外勤申请是否存在，是否审批*/
						$forenoonOld=0;
						$afternoonOld=0;
						if($outIsApply){
							$type=2;
							$outCheckinData=$this->acheckin->seekCheckin($user_code,2,$date);//取外勤打卡记录
							

							if($outCheckinData[1]["acheckin_tempstorage"]!=""){

								/*打卡的计时必须存在*/
								$tempstorage=json_decode($outCheckinData[1]["acheckin_tempstorage"],true);
								$forenoonOld=$tempstorage[$dates[0]][$dates[1]][$dates[2]]["forenoon"]["worktime"];
								$afternoonOld=$tempstorage[$dates[0]][$dates[1]][$dates[2]]["afternoon"]["worktime"];
							}
							// else{
							// 	$dayRec=$this->arecord->getMonthRec($this->selfUser["user_code"],$dates[0],$dates[1],$dates[2]);

							// 	$forenoonOld=$dayRec["forenoon"]["worktime"];
							// 	$afternoonOld=$dayRec["afternoon"]["worktime"];
							// }
								/*计算上午的时间*/
								if($forenoonOld>0){

									if($forenoonOld>=2 || ($forenoon+$forenoonOld)>=3){
										$forenoon=3;
									}else{
										$forenoon=$forenoon+$forenoonOld;
									}
								}
								/*计算下午的时间*/
								if($afternoonOld>0){
									if($afternoonOld>=($this->attendUser["auser_eachday"]-$forenoon-1) || ($afternoon+$afternoonOld)>=($this->attendUser["auser_eachday"]-$forenoon)){
										$afternoon=$this->attendUser["auser_eachday"]-$forenoon;
									}else{
										$afternoon=$afternoon+$afternoonOld;
									}
								}
						}
						if($outCheckinData[1]["acheckin_state"]>0){
							$this->arecord->reduceCount($this->selfUser["user_code"],$dates[0],$dates[1],($forenoonOld+$afternoonOld));
						}
						/*存在加班start*/

						$overIsApply=$this->acheckin->isOverTime($this->selfUser["user_code"],$date);//加班申请
						
						if($overIsApply){

							$overCheckinData=$this->acheckin->seekCheckin($user_code,3,$date,1);//取外勤打卡记录
							if($overCheckinData[1]["acheckin_tempstorage"]!=""){
								$type=3;
								$tempstorage=json_decode($overCheckinData[1]["acheckin_tempstorage"],true);
								foreach ($tempstorage as $day => $value) {
									if($day==$dates[2]){
										$forenoon=$value["forenoon"]["worktime"];
										$afternoon=$value["afternoon"]["worktime"];
									}else{

									}
								}
							}
						}
						/*存在加班end*/

						$dayTime=$forenoon+$afternoon;
						/*开始修改json数据*/
						$this->MonthRec[$dates[2]]=array("forenoon"=>array("type"=>$type,"worktime"=>$forenoon),"afternoon"=>array("type"=>$type,"worktime"=>$afternoon));

						$this->updateMonthRec($user_code,$dates,$type,$checkinData,$dayTime);
									
					break;
					/*外勤*/
				case "2":
					
					$isApply=$this->aapply->isApply($user_code,$type,$date);
					$tempRec=array();

					$foreAfter=$this->getForeAfter($startTime,$endTime,$date,$type);
					$tempRec[$dates[0]][$dates[1]][$dates[2]]=$foreAfter["rec"];
					$forenoon=$foreAfter["forenoon"];
					$afternoon=$foreAfter["afternoon"];
					$dayTime=$forenoon+$afternoon;
					
					/*统一数据更新*/
					
					if($isApply){
						$this->tempStorage($checkinData,$tempRec,1);
						/*判断正常的上下班时间是否已计算，涉及，打卡记录是否为1，取指定日期的上下午记录
							已计算：恢复记录，减掉时间，打卡记录状态恢复0，
								   重新运行	settleCheckin,
							未计算：直接修改记录
						*/
						
						$baseCheckinData=$this->acheckin->seekCheckin($user_code,1,$date,1);
						if(!empty($baseCheckinData)){
							echo "guolai";
							$dayRec=$this->arecord->getMonthRec($this->selfUser["user_code"],$dates[0],$dates[1],$dates[2]);
	
							$this->MonthRec[$dates[2]]=array("forenoon"=>array("type"=>"","worktime"=>""),"afternoon"=>array("type"=>"","worktime"=>""));

							$dayTime=$dayRec["forenoon"]["worktime"]+$dayRec["afternoon"]["worktime"];

							if($this-> updateMonthRec($user_code,$dates,1,$baseCheckinData,$dayTime,0)){

								$this->settleCheckin($user_code,1,$date);
							}

						}else{
							$this->updateMonthRec($user_code,$dates,$type,$checkinData,$dayTime);
						}
					}else{
						$this->tempStorage($checkinData,$tempRec);
					}
					break;
				case "3":
					// echo "上午".$forenoon;
					// echo "下午".$afternoon;
					$isApply=$this->acheckin->isOverTime($user_code,$date);

					$interval=count_days($startTime,$endTime);

					$startDate=split("-",split(" ",$startTime)[0]);
					$endDate=split("-",split(" ",$endTime)[0]);
					// $thisDay=$this->arecord->isWeekday($dates[0],$dates[1],$dates[2]);
					$tempRec=array();
					
					// return ;
					if($interval>0){
						/*超过当天*/
						echo "跨天的了";
						/*计算第一天的时间*/
						$foreAfter=$this->getForeAfter($startTime,split(" ",$startTime)[0]." ".$this->timeNode["END"],split(" ",$startTime)[0],$type);
						$tempRec[$startDate[0]][$startDate[1]][$startDate[2]]=$foreAfter["rec"];
						
						/*计算最后一天的时间*/
						
						
						$foreAfter=$this->getForeAfter(split(" ",$endTime)[0]." ".$this->timeNode["STA"],$endTime,split(" ",$endTime)[0],$type);
						$tempRec[$endDate[0]][$endDate[1]][$endDate[2]]=$foreAfter["rec"];

							// $tempRec[$dates[0]][$dates[1]][$dates[2]]=$this->MonthRec[$dates[2]];
						// $dayTime=$afternoon;
						if($interval>1){
							for ($i=1; $i <$interval ; $i++) {
								$tempDate=date("Y-m-d",strtotime(split(" ",$startTime)[0]." +{$i} day"));
								$tempDates=split("-",$tempDate);

								$foreAfter=$this->getForeAfter($tempDate." ".$this->timeNode["STA"],$tempDate." ".$this->timeNode["END"],$tempDate,$type);
								$tempRec[$tempDates[0]][$tempDates[1]][$tempDates[2]]=$foreAfter["rec"];
							}
							// $this->MonthRec=$this->arecord->getMonthRec($this->selfUser["user_code"],$dates[0],$dates[0]);
							
						}else{
							
						}
						// echo count($tempRec);
						$year=key($tempRec);
						echo count($tempRec[$year]);

					}else{
						// if($startTime<=$this)
			
						$foreAfter=$this->getForeAfter($startTime,$endTime,$date,$type);
						$tempRec[$dates[0]][$dates[1]][$dates[2]]=$foreAfter["rec"];
						$forenoon=$foreAfter["forenoon"];
						$afternoon=$foreAfter["afternoon"];
						$dayTime=$forenoon+$afternoon;

						
						
						// $this-> updateMonthRec($user_code,$dates,1,$baseCheckinData,$dayTime,0)
						/*当天*/
					}
					

					if($isApply){
						// $this->tempStorage($checkinData,$tempRec);
						$baseCheckinData=$this->acheckin->seekCheckin($user_code,1,$date,1);

							$dayRec=$this->arecord->getMonthRec($this->selfUser["user_code"],$dates[0],$dates[1],$dates[2]);
							// $this->MonthRec[$dates[2]]=array("forenoon"=>array("type"=>"","worktime"=>""),"afternoon"=>array("type"=>"","worktime"=>""));

							$dayTime=$dayRec["forenoon"]["worktime"]+$dayRec["afternoon"]["worktime"];

							if($this-> updateMonthRec($user_code,$dates,1,$baseCheckinData,$dayTime,0)){
								foreach ($tempRec as  $year =>$yearData) {
									$month =key($yearData);
									foreach ($yearData[$month] as $day => $dayData) {
										$this->settleCheckin($user_code,1,date("Y-m-d",strtotime($year."-".$month."-".$day)));
									}

								}
								
							}

						// $this->updateMonthRec($user_code,$dates,$type,$checkinData,$afternoon);
					}else{
						$this->tempStorage($checkinData,$tempRec);
					}
				/**
				 * 加班
				 */

					break;
				default :
					# code...
					break;
			}
		}else{
			// echo "没有记录啊";
		}
	}

	/**
	 * 对早上时间进行判断 function
	 *
	 * @param [type] $startTime
	 * @return void
	 */
	private function loadStartTime($startTime){
		$date=split(" ",$startTime);
		$MO=$date[0]." ".$this->timeNode["MO"];
		$MF=$date[0]." ".$this->timeNode["MF"];
		$AO=$date[0]." ".$this->timeNode["AO"];
		$AF=$date[0]." ".$this->timeNode["AF"];
		/*这里需要增加一个判断是否加早班*/
		if($startTime<$MO){
			return $MO;
		}else{
			if($startTime>$MF && $startTime<$AO){
				return $AO;
			}
		}
		return $startTime;
	}
	/**
	 * loadEndTime function 对结束时间做判断
	 *
	 * @param [type] $endTime
	 * @return void
	 */
	private function loadEndTime($endTime){
		if($endTime>$MF && $endTime<$AO){
			return $MF;
		}
		return $endTime;
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
	private function updateMonthRec($user_code,$dates,$type,$checkinData,$dayTime,$acheckin_state=1,$customData=array()){
		/*启动事物*/
		$emptyRec=array("forenoon"=>array("type"=>"","worktime"=>""),"afternoon"=>array("type"=>"","worktime"=>""));
		$this->acheckin->startTrans();
		/*修改打卡记录的状态，防止打卡记录重复使用*/
		foreach ($checkinData as $checkins) {
			$this->acheckin->setCheckin($checkins["acheckin_id"],array("acheckin_state"=>$acheckin_state));
		}
		// $this->arecord->startTrans();
		/*开始修改月数据*/
		if(empty($customData)){
			$setMonthResult=$this->arecord->setMonthRec($user_code,$dates[0],$dates[1],json_encode($this->MonthRec),$dayTime,$acheckin_state);
		}else{
			if(count($customData)>1){
				/*表示跨年*/
				foreach ($customData as $year=>$yearData) {
					$month=key($yearData);

					$tempMonthRec=$this->$this->arecord->getMonthRec($this->selfUser["user_code"],$year,$month);
					foreach ($yearData[$month] as $day => $dayData) {
						$tempMonthRec[$day]=$acheckin_state==1?$dayData:$emptyRec;
					}
					
					$setMonthResult=$this->arecord->setMonthRec($user_code,$year,$month,json_encode($tempMonthRec),$dayTime,$acheckin_state);
				}
			}else{
				/*当年*/
				$year=key($customData);
				if(count($customData[$year])>1){
					/*跨月*/
					foreach ($customData[$year] as $month => $monthData) {
						$day=key($monthData[$day]);
						$tempMonthRec=$this->$this->arecord->getMonthRec($this->selfUser["user_code"],$year,$month);

						foreach ($monthData as $day => $dayData) {
							$tempMonthRec[$day]=$acheckin_state==1?$dayData:$emptyRec;
						}

						$setMonthResult=$this->arecord->setMonthRec($user_code,$year,$month,json_encode($tempMonthRec),$dayTime,$acheckin_state);
					}
				}else{
					/*当月*/
					$year=key($customData);
					$month=key($customData[$year]);
					$tempMonthRec=$this->$this->arecord->getMonthRec($this->selfUser["user_code"],$year,$month);
					foreach ($customData[$year][$month] as $day => $dayData){
						$tempMonthRec[$day]=$acheckin_state==1?$dayData:$emptyRec;
					}
					$setMonthResult=$this->arecord->setMonthRec($user_code,$year,$month,json_encode($tempMonthRec),$dayTime,$acheckin_state);
				}
			}
		}
		

		/*判断是否执行成功，是，提交事务，否回滚*/
		if($setMonthResult>0){
			$this->acheckin->commit();
			return true;
		}else{
			$this->acheckin->rollback();
			return false;
		}
	}

	/**
	 * tempStorage function 当申请未审批时，计算的时间储存到临时字段里
	 *
	 * @param [type] $checkinData 打卡的两条信息
	 * @param [type] $storageArray
	 * @return void
	 */
	function tempStorage($checkinData,$storageArray,$state=0){
		foreach ($checkinData as $checkins) {
			$this->acheckin->setCheckin($checkins["acheckin_id"],array("acheckin_state"=>$state,"acheckin_tempstorage"=>json_encode($storageArray)));
		}
	}
	
	/**
	 * getForeAfter function 更新指定日期的最新记录（临时），返回数组
	 *
	 * @param [type] $startTime
	 * @param [type] $endTime
	 * @param [type] $date
	 * @return void
	 */
	private function getForeAfter($startTime,$endTime,$date,$type){
		$dates=split("-",$date);
		if($startTime<$date." ".$this->timeNode["AO"] && $endTime<$date." ".$this->timeNode["AO"]){
			// echo "A";
			$forenoon=time_reduce($startTime,$endTime);
		}elseif ($startTime>$date." ".$this->timeNode["MF"] && $endTime>$date." ".$this->timeNode["MF"]) {
			// echo "B";
			$afternoon=time_reduce($startTime,$endTime);
			# code...
		}else {
			$forenoon=time_reduce($startTime,$date." ".$this->timeNode["MF"]);
			$afternoon=time_reduce($date." ".$this->timeNode["AO"],$endTime);
		}
		$MonthRec["rec"]=$this->arecord->getMonthRec($this->selfUser["user_code"],$dates[0],$dates[1])[$dates[2]];
		$MonthRec["rec"]["forenoon"]["worktime"]+= $forenoon;
		$MonthRec["rec"]["afternoon"]["worktime"]+= $afternoon;
		$MonthRec["rec"]["forenoon"]["type"]= $type;
		$MonthRec["rec"]["afternoon"]["type"]= $type;
		$MonthRec["forenoon"]= $forenoon;
		$MonthRec["afternoon"]= $afternoon;
		return $MonthRec;
	}

	/**
	 * 以下是申请功能
	 */

	/**
	 * 根据不同的申请类型返回对应的html页面 function
	 *
	 * @return void
	 */
	function getApplyHtml(){
		$nowDate=$this->getNowTime(1);
		$nowDates=split("-",$nowDate);
		if(IS_AJAX){
			$this->assign("nowtime",$nowDate);
			$managerArray=$this->baseInfo->user()->searchManager();
			
			$this->assign("remedy",$this->arecord->findRemedy($this->selfUser["user_code"],$nowDates[0],$nowDates[1]));
			$this->assign("managerArray",$managerArray);
			$this->ajaxReturn(array("html"=>$this->fetch("attend/apply/".I("html"))));
		}
	}
	
	/**
	 * 提交申请 submitApply
	 *
	 * @return void
	 */
	function submitApply(){
		if(IS_AJAX){
			$applyArray=I("data");
			$applyArray["aapply_code"]=$this->selfUser["user_code"];
			$applyArray["aapply_addtime"]=$this->getNowTime(3);
			if($applyArray["aapply_approve"]==null){
				$applyArray["aapply_approve"]=$this->selfUser["user_director"];
			}
			print_r($applyArray);
			$dates=split("-", $applyArray["aapply_schedule"]);
			$this->MonthRec=$this->arecord->getMonthRec($this->selfUser["user_code"],$dates[0],$dates[1]);
			$isWeekday=$this->arecord->isWeekday($dates[0],$dates[1],$dates[2]);

			if($isWeekday==true){
				if(I("type")==4){
					echo "非节假日不能申请";
					return;
				}
			}else{
				if(I("type")==3 || I("type")==7 || I("type")==8 || I("type")==9 || I("type")==10 || I("type")==11 || I("type")==12){
					echo "节假日不能申请";
					return;
				}
			}

			if($applyArray["aapply_inday"]>0 && $applyArray["aapply_inday"]<3 && (time()> strtotime($applyArray["aapply_schedule"]." 09:00:00")) && I("remedy")=="false"){
				echo "上午超时了";
				return false;
			}else if($applyArray["aapply_inday"]==3 && I("type")!=3 && time()> strtotime($applyArray["aapply_schedule"]." 13:30:00")){
				echo "下午超时了";
				return false;

			}
			return;
			switch (I("type")) {
				
				case 3: case 4: case 5: case 6:/*3，工作日加班，4，节假日加班，5，上午加班，6，在家加班*/
				/*				 
				 *上午加班不允许：上午补休，上午外勤，上午事假，上午病假，出差，婚假，产假
				 *普通加班不允许：下午和全天补休，下午和全天补休，下午和全天事假，下午和全天病假，出差，婚假，产假
				 *在家加班不允许：出差
				*/

					if(I("type")==3 && (time()> strtotime($applyArray["aapply_schedule"]." 18:00:00")) && I("remedy")=="false"){
						echo "超时了";
						return false;
					}
					
					
					if(I("type")==5 && (time()> strtotime($applyArray["aapply_schedule"]." 09:00:00")) && I("remedy")=="false"){
						echo "超时了";
						return false;
					}
					return;
					$this->aapply->addApply($applyArray);
					break;
				case 7:/*补休*/
					$this->aapply->addApply($applyArray);
				/**
				 * 上午补休不允许：上午加班，上午外勤，上午事假，上午病假，出差，婚假，产假
				 * 下午补休不允许：普通加班，下午外勤，下午事假，下午病假，出差，婚假，产假
				 */
				break;
				case 2:/*外勤*/
				/**
				 * 同上
				 */
					$this->aapply->addApply($applyArray);
				break;
				case 8:/*事假*/
					$this->aapply->addApply($applyArray);
				break;
				case 9:/*病假*/
					$this->aapply->addApply($applyArray);
				break;
				case 10:/*出差*/
				/**
				 *出差不允许申请其他 
				 */
					$this->aapply->addApply($applyArray);
				break;
				case 11:/*婚假*/
				/**
				 *婚假不允许申请其他 
				 */
					$this->aapply->addApply($applyArray);
				break;
				case 12:/*产假*/
				/**
				 *产假不允许申请其他 
				 */
					$this->aapply->addApply($applyArray);
				break;
				default:
					# code...
					break;
			}
			
		}
	}

	/**
	 * 获取当前时间,可以给前端调用，防止前端修改时间
	 *
	 * @param integer $type 1,2017-08-24 2,14:22:12 3,2017-08-24 14:22:12
	 * @return void
	 */
	function getNowTime($type=0){
		$types=$type;
		if($type==0){
			$types=I("timetype");
		}
		
		switch ($types) {
			case 1: default:
				$nowTime= date("Y-m-d",time());
				break;
			case 2:
				$nowTime= date("H:i:s",time());
				break;
			case 3:
				$nowTime= date("Y-m-d H:i:s",time());
				break;
		}

		if($type==0){
			echo $nowTime;
		}else{
			return $nowTime;
		}
	}
}