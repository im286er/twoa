<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-03 16:43:53
 * @Last Modified by:   vition
 * @Last Modified time: 2017-09-26 23:27:22
 */

/*{"control":"Attend","name":"考勤管理","icon":"fa fa-calendar","menus":[{"name":"考勤配置","icon":"fa fa-gear","menus":"config"},{"name":"考勤申请","icon":"fa fa-list-alt","menus":"apply"},{"name":"申请管理","icon":"fa fa-pencil-square","menus":"applycontrol"},{"name":"打卡","icon":"fa fa-square","menus":"checkin"}]}*/
namespace Home\Controller;
use Common\Controller\AmongController;
class AttendController extends AmongController {
	public $MonthRec;
	public $timeNode;
	public $attendUser;
	//
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
		
		$this->timeNode=array("MO"=>"09:00:59","MF"=>"12:00:00","AO"=>"13:30:59","STA"=>"00:00:00","AF"=>"18:30:00","END"=>"23:59:59");

		$this->MonthRec=$this->arecord->getMonthRec($this->selfUser["user_code"],date("Y"),date("m"));
		
	}
	/**
	 * [gethtml 重写gethtml方法]
	 * @return [type] []
	 */
	public function gethtml($html=null){
		if($html==null){
			$html=I("html");
		}
        switch ($html) {
			case "applycontrol":

                $this->assign("checkinListHtml",$this->getCheckinList($this->selfUser["user_code"]));
				$this->assign("applyListHtml",$this->getApplyList($this->selfUser["user_code"]));
				$this->assign("approveListHtml",$this->getApproveList($this->selfUser["user_code"]));
				$this->assign("applicantArray",$this->aapply->getApplicant($this->selfUser["user_code"]));
				$this->assign("attendtypeArray",$this->config->search_all(array("config_class"=>"aapply_type")));
                break;
		}
        parent::gethtml($html);
	}
	/*申请管理相关开始*/
	/**
	 * getCheckinList 获取打卡记录
	 *
	 * @param integer $usercode 
	 * @param integer $p
	 * @return void
	 */
	function getCheckinList($usercode=0,$p=1){
		$condition=array();
		if($usercode==0){
			$user_code=$this->selfUser["user_code"];
			$p=$_POST["p"];
			if(I("data")["time"]!=""){
				$condition["acheckin_checkintime"]=array("EXP",">=date_sub(now(),interval ".I("data")["time"].")");
			}
		}else{
			$user_code=$usercode;
			$_POST["p"]=$p;
		}
		$limit=10;
		$count=$this->acheckin->where(array("acheckin_code"=>$user_code))->where($condition)->count();
		if($p>ceil($count/$limit)){
			$_POST["p"]=1;
		}

		$Page=new \Think\Page($count,$limit);
		$pageShow=$Page->show();

		$checkinArray=$this->acheckin->search_checkin($this->selfUser["user_code"],$condition,$Page->firstRow,$Page->listRows);
		$this->assign("checkinArray",$checkinArray);
		$return=array("html"=>$this->fetch("attend/applycontrol/checkin_list"),"pages"=>$pageShow);
		if($usercode==0){
			$this->ajaxReturn($return);
		}
		return $return;
	}

	/**
	 * getApplyList function 获取申请记录
	 *
	 * @param integer $usercode
	 * @param integer $p
	 * @return void
	 */
	function getApplyList($usercode=0,$p=1){
		$condition=array();
		$state=array('<span class="label label-info show-apply" data-toggle="modal" data-target="#applyModal">未审批</span>','<span class="label label-success arrowed show-apply" data-toggle="modal" data-target="#applyModal">已审批</span>','<span class="label label-danger arrowed-in show-apply" data-toggle="modal" data-target="#applyModal">拒绝</span>','<span class="label label-warning arrowed-in arrowed-in-right show-apply" data-toggle="modal" data-target="#applyModal">审核中</span>','<span class="label label-inverse arrowed-in-right show-apply" data-toggle="modal" data-target="#applyModal">删除</span>');
		if($usercode==0){
			$user_code=$this->selfUser["user_code"];
			$p=$_POST["p"];
			if(I("data")["time"]!=""){
				$condition["aapply_schedule"]=array("EXP",">=date_sub(now(),interval ".I("data")["time"].")");
			}
			foreach (I("data") as $key => $value) {
				if($key!="time"){
					$condition[$key]=array("eq",$value);
				}
			}
		}else{
			$user_code=$usercode;
			$_POST["p"]=$p;
		}
		$limit=10;
		$count=$this->aapply->where(array("aapply_code"=>$user_code))->where($condition)->count();
		if($p>ceil($count/$limit)){
			$_POST["p"]=1;
		}

		$Page=new \Think\Page($count,$limit);
		$pageShow=$Page->show();

		$aapplyArray=$this->aapply->searchApply($this->selfUser["user_code"],$condition,$Page->firstRow,$Page->listRows);
		// echo $this->aapply->getLastSql();
		$this->assign("aapplyArray",$aapplyArray);
		$this->assign("state",$state);
		$return=array("html"=>$this->fetch("attend/applycontrol/apply_list"),"pages"=>$pageShow);
		if($usercode==0){
			$this->ajaxReturn($return);
		}
		return $return;
	}

	/**
	 * getApproveList function 获取审批记录
	 *
	 * @param integer $usercode
	 * @param integer $p
	 * @return void
	 */
	function getApproveList($usercode=0,$p=1){
		$condition=array();
		$state=array('<button class="btn btn-xs btn-inverse show-apply" data-toggle="modal" data-target="#applyModal"><i class="ace-icon fa fa-square-o
		bigger-110"></i>&nbsp;未审批&nbsp;<i class="ace-icon fa fa-hand-o-up icon-on-right"></i></button>','<button class="btn btn-xs btn-success show-apply" data-toggle="modal" data-target="#applyModal"><i class="ace-icon fa fa-check-square bigger-110"></i>&nbsp;审批&nbsp;<i class="ace-icon fa fa-hand-o-up icon-on-right"></i></button>','<button class="btn btn-xs btn-danger show-apply" data-toggle="modal" data-target="#applyModal"><i class="ace-icon fa fa-times-rectangle bigger-110"></i>&nbsp;拒绝&nbsp;<i class="ace-icon fa fa-hand-o-up icon-on-right"></i></button>','<button class="btn btn-xs btn-warning show-apply" data-toggle="modal" data-target="#applyModal"><i class="ace-icon fa fa-hourglass-half bigger-110"></i>&nbsp;审批中&nbsp;<i class="ace-icon fa fa-hand-o-up icon-on-right"></i></button>','<button class="btn btn-xs btn-inverse show-apply" data-toggle="modal" data-target="#applyModal"><i class="ace-icon fa fa-window-close-o bigger-110"></i>&nbsp;删除&nbsp;<i class="ace-icon fa fa-hand-o-up icon-on-right"></i></button>');
		if($usercode==0){
			$user_code=$this->selfUser["user_code"];
			$p=$_POST["p"];
			if(I("data")["time"]!=""){
				$condition["aapply_schedule"]=array("EXP",">=date_sub(now(),interval ".I("data")["time"].")");
			}
			foreach (I("data") as $key => $value) {
				if($key!="time"){
					if($key=="aapply_code"){
						$condition[$key]=array("in",$value);
					}else{
						$condition[$key]=array("eq",$value);
					}
				}
			}
		}else{
			$user_code=$usercode;
			$_POST["p"]=$p;
		}
		$limit=10;
		$count=$this->aapply->where("aapply_approve LIKE '%".$user_code."%'")->where($condition)->count();
		if($p>ceil($count/$limit)){
			$_POST["p"]=1;
		}

		$Page=new \Think\Page($count,$limit);
		$pageShow=$Page->show();

		$approveArray=$this->aapply->searchApply($this->selfUser["user_code"],$condition,$Page->firstRow,$Page->listRows,true);
		// echo $this->aapply->getLastSql();
		$this->assign("approveArray",$approveArray);
		$this->assign("state",$state);
		$return=array("html"=>$this->fetch("attend/applycontrol/approve_list"),"pages"=>$pageShow);
		if($usercode==0){
			$this->ajaxReturn($return);
		}
		return $return;
	}

	/**
	 * getApplyInfo function 获取申请信息
	 *
	 * @param integer $applyId
	 * @return void
	 */
	function getApplyInfo($applyId=0){

		$state=array('<span class="label label-inverse show-apply" >未审批</span>','<span class="label label-success arrowed show-apply" >已审批</span>','<span class="label label-danger arrowed-in show-apply" >拒绝</span>','<span class="label label-warning arrowed-in arrowed-in-right show-apply">审核中</span>','<span class="label label-inverse arrowed-in-right show-apply">删除</span>');
		// echo I("active");
		if(I("active")=="approve-html-div"){
			$conHtml=array('<button class="btn btn-sm btn-success state-con" data-state="1"><i class="ace-icon fa fa-check-square"></i>通过</button><button class="btn btn-sm btn-danger state-con" data-state="2"><i class="ace-icon fa fa-times-rectangle"></i>拒绝</button>','','','');
			$this->assign("readonly","");
		}else{
			$conHtml=array('<button class="btn btn-sm btn-danger state-con" data-state="4"><i class="ace-icon fa fa-times"></i>删除</button><button class="btn btn-sm btn-primary"><i class="ace-icon fa fa-pencil-square-o"></i>编辑</button>','','','<button class="btn btn-sm btn-danger state-con" data-state="4"><i class="ace-icon fa fa-times"></i>删除</button>');
			$this->assign("readonly","readonly");
		}
		

		if($applyId==0){
			$aapply_id=I("applyid");
		}else{
			$aapply_id=$applyId;
		}
		$applyInfo=$this->aapply->getAppy($aapply_id);
		if(!array_key_exists($this->selfUser["user_code"],json_decode($applyInfo["aapply_operation"],true)) && $applyInfo["aapply_state"]==3){
			$this->assign("conHtml",$conHtml[0]);
		}else{
			$this->assign("conHtml",$conHtml[$applyInfo["aapply_state"]]);
		}

		$this->assign("applyInfo",$applyInfo);
		$this->assign("applyState",$state[$applyInfo["aapply_state"]]);
		$return=array("html"=>$this->fetch("attend/applycontrol/applyinfo"));
		if($usercode==0){
			$this->ajaxReturn($return);
		}
		return $return;
	}

	/**
	 * setApplyState function 修改申请状态
	 *
	 * @param integer $id
	 * @param integer $state
	 * @return void
	 */
	function setApplyState($id=0,$state=0,$remark=""){
		if($id==0){
			$aapply_id=I("aapply_id");
			$aapply_state=I("aapply_state");
			$aapply_remark=I("aapply_remark");
		}else{
			$aapply_id=$id;
			$aapply_state=$state;
			$aapply_remark=$remark;
		}
		$applyInfo=$this->aapply->field("aapply_operation,aapply_approve")->getAppy($aapply_id);
		$aapply_operation=json_decode($applyInfo["aapply_operation"],true);
		$aapply_approve=json_decode($applyInfo["aapply_approve"],true);

		$data=array();
		$data["aapply_state"]=$aapply_state;
		if(count($aapply_approve)>1){
			if($aapply_operation==null || (count($aapply_operation)+1)<count($aapply_approve)){
				if($aapply_state!=2){
					$data["aapply_state"]=3;
				}
				
			}
		}

		$aapply_operation[$this->selfUser["user_code"]]=array($aapply_state,time());
		$data["aapply_operation"]=json_encode($aapply_operation);


		$result=$this->aapply->setApply($aapply_id,$data);
		$msg="审批失败";
		if($result>0){
			$msg="审批成功";
			$this->Wxqy->secret($this->WxConf["assistant"]["corpsecret"]);//更改成企业小助手的secret
			switch ($data["aapply_state"]) {
				case 1:
					$mesArray=array("touser"=>$applyInfo["aapply_code"],"msgtype"=>"text","agentid"=>"0","text"=>array("content"=>"你申请 {$applyInfo['aapply_schedule']} 的 {$applyInfo['aapply_types']} 已被 ".$this->selfUser['user_name']." 批准"));
					break;
				case 2:
					$mesArray=array("touser"=>$applyInfo["aapply_code"],"msgtype"=>"text","agentid"=>"0","text"=>array("content"=>"很遗憾，你申请 {$applyInfo['aapply_schedule']} 的 {$applyInfo['aapply_types']} 已被 ".$this->selfUser['user_name']." 拒绝"));
					break;
				case 3:
					$mesArray=array("touser"=>$applyInfo["aapply_code"],"msgtype"=>"text","agentid"=>"0","text"=>array("content"=>"你申请 {$applyInfo['aapply_schedule']} 的 {$applyInfo['aapply_types']} 已被 ".$this->selfUser['user_name']." 批准，请等待其他人审核"));
					break;
				default:
					return;
					break;
			}
			$this->Wxqy->message()->send($mesArray);//通过微信发送通知
		}

		if($id==0){
			$this->ajaxReturn(array("status"=>$result,"msg"=>$msg));
		}
		return json_encode(array("status"=>$result,"msg"=>$msg));
	}
	/*申请管理相关结束*/

	/**
	 * [checkin 打卡页面]
	 * @return [type] [description]
	 */
	public function checkin(){
		$date=date("Y-m-d",time());
		if(IS_AJAX){
			$date=date("Y-m-d",strtotime(I("thisDay")));
		}

		$this->settleApply();
		
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
	 * 返回按钮 function
	 *
	 * @param [type] $type
	 * @param [type] $index
	 * @return void
	 */
	private function getButton($type,$index,$applyid=0){
		$info=array(array(),array("上班","下班","fa-sun-o","fa-moon-o"),array("开始外勤","结束外勤","fa-sign-out","fa-circle-o-notch"),array("开始加班","结束加班","fa-clock-o","fa-hand-peace-o"));
		
		/*0:两个按钮都禁用,1:第一个按钮禁用，第二个按钮激活，2：第一个按钮激活，第二个按钮禁用*/
		$butInfo=array("<button class='btn disabled' data-type='{$type}' data-applyid='{$applyid}' data-timetype='1'><i class='ace-icon fa {$info[$type][2]} align-top bigger-125'></i>{$info[$type][0]} </button><button  class='btn disabled' data-type='{$type}' data-applyid='{$applyid}' data-timetype='2'><i class='ace-icon fa {$info[$type][3]} align-top bigger-125'></i>{$info[$type][1]}</button>","<button class='btn disabled' data-type='{$type}' data-applyid='{$applyid}' data-timetype='1'><i class='ace-icon fa {$info[$type][2]} align-top bigger-125'></i>{$info[$type][0]}</button><button data-toggle='button' class='btn btn-success' data-type='{$type}' data-applyid='{$applyid}' data-timetype='2'><i class='ace-icon fa {$info[$type][3]} align-top bigger-125'></i>{$info[$type][1]}</button>","<button data-toggle='button' class='btn btn-success' data-type='{$type}' data-applyid='{$applyid}' data-timetype='1'><i class='ace-icon fa {$info[$type][2]} align-top bigger-125'></i>{$info[$type][0]}</button><button class='btn disabled' data-type='{$type}' data-applyid='{$applyid}' data-timetype='2'><i class='ace-icon fa {$info[$type][3]} align-top bigger-125'></i>{$info[$type][1]}</button>");

		return $butInfo[$index];
	}
	/**
	 * [checkinType 返回不同状态的按钮]
	 * @param  [type] $user_code [用户code]
	 * @param  [type] $type      [类型：1上下班，2外勤，3加班]
	 * @return [type]            [description]
	 */
	private function checkinType($user_code,$type,$date){
		/*这里加上判断外勤是否属于全天，如果是，正常上下班按钮禁止*/
		$applyid=0;
		if($type==2){
			/*状态是外勤*/
			$aapplyData=$this->aapply->seekApply($user_code,$type,$date);
			if($aapplyData==null){
				return $this->getButton($type,0);
			}else{
				$applyid=$aapplyData["aapply_id"];
			}
		}
		if($type>2){
			/*状态是加班*/
			$aapplyData=$this->aapply->seekApply($user_code,$type,$aapplyData["aapply_id"]);	
			
			if($aapplyData==null){
				
				$aapplyData=$this->aapply->seekApply($user_code,4,$aapplyData["aapply_id"]);
				if($aapplyData4==null){
					return $this->getButton($type,0);
				}else{
					$type=4;
					if((time()-strtotime($aapplyData["aapply_schedule"]))>86400){
						return $this->getButton($type,0);
					}
				}
			}else if((time()-strtotime($aapplyData["aapply_schedule"]." 23:59:59"))>86400){
				return $this->getButton($type,0);
			}else{
				$applyid=$aapplyData["aapply_id"];
			}
			$checkinData=$this->acheckin->seekCheckin($user_code,$type,null,null,$aapplyData["aapply_id"]);
		}else{
			$checkinData=$this->acheckin->seekCheckin($user_code,$type,$date);
		}
	
		if(count($checkinData)>0){
			if(count($checkinData)>1){
				return $this->getButton($type,0);
			}else{
				return $this->getButton($type,1,$applyid);
			}
		}else{
			$dates=split("-", $date);
			$thisDay=$this->arecord->isWeekday($dates[0],$dates[1],$dates[2]);
			/*判断是否工作日*/
			if($thisDay==false){
				return $this->getButton($type,0);
			}else{
				return $this->getButton($type,2,$applyid);
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
			echo json_encode(array("status"=>"1","msg"=>$Position));
		}else{
			echo json_encode(array("status"=>"0","msg"=>"抱歉！坐标不在公司范围，请使用拍照"));
		}
		
	}

	/**
	 * [submitCheckin 提交打打卡信息]
	 * @return [type] [description]
	 */
	function submitCheckin(){
		if(IS_AJAX){
		// print_r($_POST);
			$checkinData=$_POST["data"];
			$checkinData["acheckin_addtime"]=date("Y-m-d H:i:s",time());
			/**
			 * 1,定位打卡，2拍照打卡
			 */
			$checkinResult=array("status"=>"0","msg"=>"打卡失败，请联系管理员");
			switch ($_POST["data"]["acheckin_checkinway"]) {

				case "1":
					# code...
					$checkinData["acheckin_checkintime"]=date("Y-m-d H:i:s",time());

					if($checkinData["acheckin_type"]>2 && $checkinData["acheckin_timetype"]==2){
						$checkinData=$this->acheckin->seekCheckin($checkinData["acheckin_code"],$checkinData["acheckin_type"]);
						if(count($checkinData)>1){
							$dates=split("-",date("Y-m-d",time()));
							$isWeekday=$this->arecord->isWeekday($dates[0],$dates[1],$dates[2]);
							if($isWeekday==true){
								if($checkinData[0]["acheckin_applyid"]!=$checkinData[1]["acheckin_applyid"]){
									$this->ajaxReturn(array("status"=>"0","msg"=>"你的工作日加班超时，请使用监控拍照"));
								}
							}
						}
					}
					$result=$this->acheckin->checkin($checkinData);
					if($result>0){
						$checkinResult=array("status"=>"1","msg"=>$result);
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
					
					if($downloadResult!=false){
						$pictureName=$checkinData["acheckin_code"]."-".date("Y-m-d[His]",time()).".".$downloadResult["type"];
						$picture=fopen("Public/images/upload/checkin/".$pictureName, "w+");
						$result= fwrite($picture, $downloadResult["content"]);
						fclose($picture);
						if($result>0){
							$checkinData["acheckin_picture"]=$pictureName;
							$result=$this->acheckin->add($checkinData);
							if($result>0){
								$checkinResult= array("status"=>"1","msg"=>$result);
							}
						}
					}
					# code...
					break;
			}
			
			if($checkinData["acheckin_type"]==2 && $checkinData["acheckin_timetype"]==2 && $checkinResult["status"]==1){
				// dump(I("knockoff"));
				/*这里是外勤结束计算*/
				if(I("knockoff")!=null){
					/*添加一条下班记录*/
					$checkinData["acheckin_type"]=1;
					$checkinData["acheckin_applyid"]=0;
					$this->acheckin->add($checkinData);
					//
				}
				// $this->settleAttend($this->selfUser["user_code"],"2017-09-14");
			}else{
				if($checkinData["acheckin_timetype"]==2 && $checkinResult["status"]==1){
					//计算上下班时间，
				}

			}
			$this->ajaxReturn($checkinResult);
			
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
	 * getForeAfter function 更新指定日期的最新记录（临时），返回数组
	 *
	 * @param [type] $startTime
	 * @param [type] $endTime
	 * @param [type] $date
	 * @return void
	 */
	private function getForeAfter($startTime,$endTime,$date,$type){
		$startTime=$this->loadStartTime($startTime);//对开始时间进行初始化
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
		// $MonthRec["rec"]=$this->arecord->getMonthRec($this->selfUser["user_code"],$dates[0],$dates[1])[$dates[2]];
		$MonthRec["rec"]["forenoon"]["worktime"]= $forenoon;
		$MonthRec["rec"]["afternoon"]["worktime"]= $afternoon;
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
			$this->assign("projectArray",R("Project/searchProjectName"));
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

			$aapply_approve=array($this->selfUser["user_director"]);
			if(is_array($applyArray["aapply_approve"])){
				$applyArray["aapply_approve"]=json_encode(array_unique(array_merge($aapply_approve,$applyArray["aapply_approve"])));
			}else{
				$applyArray["aapply_approve"]=json_encode($aapply_approve);
			}

			// print_r($applyArray);
			
			$dates=split("-", $applyArray["aapply_schedule"]);
			$this->MonthRec=$this->arecord->getMonthRec($this->selfUser["user_code"],$dates[0],$dates[1]);
			$isWeekday=$this->arecord->isWeekday($dates[0],$dates[1],$dates[2]);

			if($isWeekday==true){
				if(I("type")==4){
					$this->ajaxReturn(array("status"=>"0","msg"=>"非节假日不能申请"));
				}
			}else{
				if(I("type")==3 || I("type")==7 || I("type")==8 || I("type")==9 || I("type")==10 || I("type")==11 || I("type")==12){
					$this->ajaxReturn(array("status"=>"0","msg"=>"节假日不能申请"));
				}
			}

			if($applyArray["aapply_inday"]>0 && $applyArray["aapply_inday"]<3 && (time()> strtotime($applyArray["aapply_schedule"]." 09:00:00")) && I("remedy")=="false"){
				$this->ajaxReturn(array("status"=>"0","msg"=>"上午超时了"));

			}else if($applyArray["aapply_inday"]==3 && I("type")!=3 && time()> strtotime($applyArray["aapply_schedule"]." 13:30:00")){
				$this->ajaxReturn(array("status"=>"0","msg"=>"下午超时了"));


			}
			// return;
			switch (I("type")) {
				case 3: case 4: case 5: case 6:/*3，工作日加班，4，节假日加班，5，上午加班，6，在家加班*/
				/*				 
				 *上午加班不允许：上午补休，上午外勤，上午事假，上午病假，出差，婚假，产假
				 *普通加班不允许：下午和全天补休，下午和全天补休，下午和全天事假，下午和全天病假，出差，婚假，产假
				 *在家加班不允许：出差
				*/

					if(I("type")==3){
						if((time()> strtotime($applyArray["aapply_schedule"]." 18:30:00")) && I("remedy")=="false"){
							$this->ajaxReturn(array("status"=>"0","msg"=>"申请超时了"));

						}else{
							//**再加一个判断上班时间是否打卡了
							$checkin=$this->acheckin->hasCheckin($this->selfUser["user_code"],1,1,$applyArray["aapply_schedule"]);
							if($checkin==null){
								
							}else{
								$checkinData=$this->acheckin->seekCheckin($this->selfUser["user_code"],3);
								if(count($checkinData)>2){
									if($checkinData[0]["acheckin_applyid"]!=$checkinData[1]["acheckin_applyid"]){
										$this->ajaxReturn(array("status"=>"0","msg"=>"你上次申请的加班记录还没结束哦！"));
									}
								}else{
									$checkinArray=array("acheckin_code"=>$this->selfUser["user_code"],"acheckin_checkinway"=>3,"acheckin_type"=>3,"acheckin_timetype"=>1,"acheckin_addtime"=>date("Y-m-d H:i:s",time()),"acheckin_checkintime"=>$applyArray["aapply_schedule"]." 18:30:00");
								}
							}
						}
					}
					return;
					
					if(I("type")==5 && (time()> strtotime($applyArray["aapply_schedule"]." 09:00:00")) && I("remedy")=="false"){
						$this->ajaxReturn(array("status"=>"0","msg"=>"申请超时了"));

					}
					$this->aapply->startTrans();
					$result=$this->aapply->addApply($applyArray);
					if(isset($checkinArray)){
						$checkinArray["acheckin_applyid"]=$result;
						$result=$this->acheckin->checkin($checkinArray);
						if($result>0){
							$this->aapply->commit();
						}else{
							$this->aapply->rollback();
						}
					}else{
						$this->aapply->commit();
					}
					// $this->aapply->addApply($applyArray);
					break;
				case 7:/*补休*/
					if($applyArray["aapply_inday"]==1){
						//aapply_schedule<date_sub("2017-09-20",interval 7 day) and aapply_type=7 and aapply_inday=1 and aapply_days>1
						$condition=array('_string'=>'aapply_schedule>date_sub("'.$applyArray["aapply_schedule"].'",interval 7 day)','aapply_inday'=>array("EQ","1"),'aapply_type'=>array('EQ','7'));
						$hasResult=$this->aapply->searchApply($this->selfUser["user_code"],$condition);
						// echo $this->aapply->getLastSql();
						// print_r($hasResult);
						if(!empty($hasResult)){
							if($applyArray["aapply_days"]>1){
								$this->ajaxReturn(array("status"=>"0","msg"=>"你7天内已存在一次相同的补休！"));
							}else{
								if(count($hasResult)>=2){
									$this->ajaxReturn(array("status"=>"0","msg"=>"你7天内已存在两次相同的补休！"));
								}
							}
						}
					}
					$result=$this->aapply->addApply($applyArray);
				/**
				 * 上午补休不允许：上午加班，上午外勤，上午事假，上午病假，出差，婚假，产假
				 * 下午补休不允许：普通加班，下午外勤，下午事假，下午病假，出差，婚假，产假
				 */
				break;
				case 2:/*外勤*/
				/**
				 * 同上
				 */
					$result=$this->aapply->addApply($applyArray);
				break;
				case 8:/*事假*/
					$result=$this->aapply->addApply($applyArray);
				break;
				case 9:/*病假*/
					$result=$this->aapply->addApply($applyArray);
				break;
				case 10:/*出差*/
				/**
				 *出差不允许申请其他 
				 */
					$result=$this->aapply->addApply($applyArray);
				break;
				case 11:/*婚假*/
				/**
				 *婚假不允许申请其他 
				 */
					$result=$this->aapply->addApply($applyArray);
				break;
				case 12:/*产假*/
				/**
				 *产假不允许申请其他 
				 */
					$result=$this->aapply->addApply($applyArray);
				break;
				default:
					# code...
					break;
			}
			
			if($result>0){
				$this->ajaxReturn(array("status"=>"1","msg"=>"申请提交成功！"));
			}else{
				$this->ajaxReturn(array("status"=>"0","msg"=>$result));
			}
			
		}
	}

	/**
	 * Undocumented function 处理未计算的申请
	 *
	 * @return void
	 */
	private function settleApply(){
		$unSettleApply=$this->aapply->searchApply($aapply_code,array("aapply_state"=>1,"aapply_settle"=>0));
		foreach ($unSettleApply as $apply) {
			$this->setAttend($apply);
		}
	}

	/**
	 * setAttend function 审计考勤
	 *
	 * @param [type] $applyInfo 要审计的申请信息
	 * @return void
	 */
	function setAttend($applyInfo){
		if($applyInfo["aapply_days"]==0){
			$days=1;
		}else{
			$days=$applyInfo["aapply_days"];
		}
		
		for ($i=0; $i <$days; $i++) {
			// echo $i."开始";
			// echo $applyInfo["aapply_type"].'-'.$i.'\n';
			$thisDate=date('Y-m-d',strtotime('+'.$i.' day',strtotime($applyInfo["aapply_schedule"])));
			list($year,$month,$date)=split("-",$thisDate);
			
			//获取考勤记录的数据
			$monthRec=$this->arecord->getMonthRec($this->selfUser["user_code"],$year,$month);
		
			//初始化每天数据结构
			$baseRec=array("forenoon"=>array("worktime"=>0,"type"=>0),"afternoon"=>array("worktime"=>0,"type"=>0));
			if(is_array($monthRec[$date])){
				$baseRec=$monthRec[$date];
			}
			$forenoon=$baseRec["forenoon"]["worktime"];
			$afternoon=$baseRec["afternoon"]["worktime"];
			$foreType=$baseRec["forenoon"]["type"];
			$afterType=$baseRec["afternoon"]["type"];
	
			$type=$applyInfo["aapply_type"];
			switch ($type) {
				case '2'://外勤
					if($applyInfo["aapply_tempstorage"]!=""){
						$tempAttend=json_decode($applyInfo["aapply_tempstorage"],true);
					}else{
						return false;
					}
					$foreTemp=$tempAttend[$year][$month][$date]["forenoon"]["worktime"];
					$afterTemp=$tempAttend[$year][$month][$date]["afternoon"]["worktime"];
					if($foreTemp>0){
						$foreType=$type;
					}
					if($afterTemp>0){
						$afterType=$type;
					}
	
					if($foreTemp>=2 || ($foreTemp+$forenoon)>=3){
						$forenoon=3;
					}else{
						$forenoon+=$foreTemp;
					}
					if($afterTemp>=4 || ($afterTemp+$afternoon)>=5){
						$afternoon=5;
					}else{
						$afternoon+=$afterTemp;
					}
					# code...
					break;
				case "3"://工作日加班
					if($applyInfo["aapply_tempstorage"]!=""){
						$tempAttend=json_decode($applyInfo["aapply_tempstorage"],true);
					}else{
						return false;
					}
					
					$afterTemp=$tempAttend[$year][$month][$date]["afternoon"]["worktime"];
					
					if($afterTemp>0){
						$afterType=$type;
						$afternoon+=$afterTemp;
					}
					break;
				case "4"://工作日加班
					if($applyInfo["aapply_tempstorage"]!=""){
						$tempAttend=json_decode($applyInfo["aapply_tempstorage"],true);
					}else{
						return false;
					}
					$foreTemp=$tempAttend[$year][$month][$date]["forenoon"]["worktime"];
					$afterTemp=$tempAttend[$year][$month][$date]["afternoon"]["worktime"];
					if($foreTemp>0){
						$foreType=$type;
						$forenoon=$foreTemp;
					}
					if($afterTemp>0){
						$afterType=$type;
						$afternoon=$afterTemp;
					}
					break;
				case "5"://上午加班
					$checkinInfo=$this->acheckin->findCheckin($applyInfo["aapply_code"],array("acheckin_type"=>array("eq",1),"acheckin_timetype"=>array("eq",1),"date_format(acheckin_checkintime,'%Y-%m-%d')"=>array("eq",$applyInfo["aapply_schedule"])));
					$theTime=split(" ",$checkinInfo["acheckin_checkintime"]);
					$theMO=$theTime[0]." 09:00:00";
					if($checkinInfo["acheckin_checkintime"]<$theMO){
						$forenoon+="\n".(strtotime($theMO)-strtotime($checkinInfo["acheckin_checkintime"]))/3600;
						$foreType=$type;
					}
					break;
				
				case "6"://在家加班
					$afternoon+=$applyInfo["aapply_hours"];
					$afterType=$type;
					break;
				
				case "7"://补休，补休要去查找时间总和，
					
					if($this->attendUser["auser_worktime"]>0){
						$foreType=$type;
	
						if($this->attendUser["auser_worktime"]>=(3-$forenoon)){
							$forenoon=3;
							$this->attendUser["auser_worktime"]-=3;
						}else{
							$forenoon+=$this->attendUser["auser_worktime"];
							$this->attendUser["auser_worktime"]=0;
						}
					}
					if($this->attendUser["auser_worktime"]>0){
						$afterType=$type;
						if($this->attendUser["auser_worktime"]>=($this->attendUser["auser_eachday"]-3-$afternoon)){
							$afternoon=$this->attendUser["auser_eachday"]-3;
							$this->attendUser["auser_worktime"]-=$this->attendUser["auser_eachday"]-3;
						}else{
							$afternoon+=$this->attendUser["auser_worktime"];
							$this->attendUser["auser_worktime"]=0;
						}
					}
	
					break;
				
				case "8": case "9"://事假，病假
					if($forenoon<3){
						$foreType=$type;
					}
					if($afternoon<($this->attendUser["auser_eachday"]-3)){
						$afterType=$type;
					}
					
					break;
				case "10": case "11": case "12": case "13"://出差、婚假、产假、巡展
					$forenoon=3;
					$afternoon=5;
					$foreType=$type;
					$afterType=$type;
					break;
				case "14"://婚检
					if($applyInfo["aapply_inday"]==1){
						$forenoon=3;
						$afternoon=$this->attendUser["auser_eachday"]-3;
						$foreType=$type;
						$afterType=$type;
					}else if($applyInfo["aapply_inday"]==2){
						$forenoon=3;
						$foreType=$type;
					}else if($applyInfo["aapply_inday"]==3){
						$afternoon=$this->attendUser["auser_eachday"]-3;
						$afterType=$type;
					}
					break;
				default:
					# code...
					break;
			}
	
			$theDate["forenoon"]["worktime"]=$forenoon;
			$theDate["afternoon"]["worktime"]=$afternoon;
			$theDate["forenoon"]["type"]=$foreType;
			$theDate["afternoon"]["type"]=$afterType;
			$monthRec[$date]=$theDate;
			// print_r($theDate);
			$count=0;
			foreach ($monthRec as $eachDate) {
				$count+=$eachDate["forenoon"]["worktime"]+$eachDate["afternoon"]["worktime"];
			}
			$this->arecord->startTrans();
			$recordResult=$this->arecord->setMonthRec($applyInfo["aapply_code"],$year,$month,array("arecord_json"=>json_encode($monthRec),"arecord_count"=>$count));
			
			if($recordResult>0){
				$applyResult=$this->aapply->setApply($applyInfo["aapply_id"],array("aapply_settle"=>1));
				if($applyResult>0){
					$this->arecord->commit();
				}else{
					$this->arecord->rollback();
				}
			}
		}
	}
}