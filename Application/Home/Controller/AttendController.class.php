<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-03 16:43:53
 * @Last Modified by:   369709991@qq.com
 * @Last Modified time: 2017-09-02 15:41:09
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

		// if($this->attendUser["auser_eachday"]>7.5){
		// 	$this->timeNode["AF"]="18:30:00";
		// }
		/*每天的时间节点*/
		
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
				// echo $this->getCheckinList($this->selfUser["user_code"]);
				// $checkinArray=$this->acheckin->search_checkin($this->selfUser["user_code"]);
				// $this->assign("checkinArray",$checkinArray);
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
	 * settleAttend function 计算一天里的考勤
	 *
	 * @param [type] $user_code
	 * @param [type] $date
	 * @return void
	 */
	function settleAttend($user_code,$date){
		//1，先获取指定日期的所有打卡记录
		$allCheckin=$this->acheckin->seekCheckin($user_code,null,$date);
		$checkins=array();
		//1,重新编排记录，三维数组，第一维key为类型，第二维key为开始1或结束2
		foreach ($allCheckin as $checkin){
			$checkins[$checkin["acheckin_type"]][$checkin["acheckin_timetype"]]=$checkin;
		}
		//2，获取指定日期的所有申请
		$allApply=$this->aapply->sameDate($user_code,$date);
		$applys=array();
		foreach ($allApply as $apply){
			$applys[$apply["aapply_type"]]=$apply;
		}
		//3，开始计算所有可能的考勤
		$forenoon=0;
		$afternoon=0;
		$dates=split("-", $date);/*分解成年月日*/

		/*计算正常上班的时间，上午，下午*/
		if(count($checkins[1])>1){
			$startTime=$this->loadStartTime($checkins[1][1]["acheckin_checkintime"]);
			$endTime=$this->loadEndTime($checkins[1][2]["acheckin_checkintime"]);
			$foreAfter=$this->getForeAfter($startTime,$endTime,$date,1);
			$tempRec[$dates[0]][$dates[1]][$dates[2]]=$foreAfter["rec"];
			$forenoon=$foreAfter["forenoon"];
			$afternoon=$foreAfter["afternoon"];
			// print_r($applys);
		}
		// echo $forenoon,"-",$afternoon;
		// echo "<br>";
		/*计算外勤的时间*/
		if(isset($applys[2])){
			/*判断外勤申请是否批准*/
			if($applys[2]["aapply_state"]>0 && $applys[2]["aapply_tempstorage"]!=""){
				$tempAttend=json_decode($applys[2]["aapply_tempstorage"],true);
				$foreTemp=$tempAttend[$dates[0]][$dates[1]][$dates[2]]["forenoon"]["worktime"];
				$afterTemp=$tempAttend[$dates[0]][$dates[1]][$dates[2]]["afternoon"]["worktime"];

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
			}
		}
		/*计算工作日加班（计算临时存储时跨天自动加天数） 做循环计算3、4、5、6*/
		for ($i=3; $i <7 ; $i++) { 
			if(isset($applys[$i])){
				if($applys[$i]["aapply_state"]>0 && $applys[$i]["aapply_tempstorage"]!=""){
					$tempAttend=json_decode($applys[$i]["aapply_tempstorage"],true);
					$foreTemp=$tempAttend[$dates[0]][$dates[1]][$dates[2]]["forenoon"]["worktime"];
					$afterTemp=$tempAttend[$dates[0]][$dates[1]][$dates[2]]["afternoon"]["worktime"];
	
					if($foreTemp>0){
						$forenoon+=$foreTemp;
					}
					if($afterTemp>0){
						$afternoon+=$afterTemp;
					}
				}
			}
		}
		
		/*计算补休、事假、病假时间*/
		for ($i=7; $i <10 ; $i++) { 
			if(isset($applys[$i])){
				/*判断补休申请是否批准*/
				if($applys[$i]["aapply_state"]>0 && $applys[$i]["aapply_tempstorage"]!=""){
					$tempAttend=json_decode($applys[$i]["aapply_tempstorage"],true);
					/*临时记录保存的是可以补休的小时，如果工时不够时间，那么保存的将是0*/
					$foreTemp=$tempAttend[$dates[0]][$dates[1]][$dates[2]]["forenoon"]["worktime"];
					$afterTemp=$tempAttend[$dates[0]][$dates[1]][$dates[2]]["afternoon"]["worktime"];
					$forenoon+=$foreTemp;
					$afternoon+=$afterTemp;
				}
			}
		}
		
		/*计算出差、产假、婚假，巡展时间*/
		for ($i=10; $i <14 ; $i++) { 
			if(isset($applys[$i])){
				$forenoon=3;
				$afternoon=$this->attendUser["auser_eachday"]-$forenoon;
			}
		}

		/*计算产检时间*/
		if(isset($applys[14])){
			if($applys[14]["aapply_state"]>0){
				switch ($applys[14]["aapply_inday"]) {
					case 1:
						$forenoon=3;
						$afternoon=$this->attendUser["auser_eachday"]-$forenoon;
						break;
					case 2:
						$forenoon=3;
						break;
					case 3:
						$afternoon=$this->attendUser["auser_eachday"]-3;
						break;
				}
			}
			
		}
		// echo $forenoon,"-",$afternoon;
		// echo "<br>";
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
		
		$this->settleAttend($this->selfUser["user_code"],"2017-09-14");
		// return;
		/*测试各种考勤审计*/
		// $test=$this->acheckin->isOverTime($this->selfUser["user_code"],"2017-08-18");
		// print_r($test);
		// return;
		// echo date("Y-m-d",strtotime("2017-8-14 +1 day"));// echo date("Y-m-d",strtotime("2017-8-14 +1 day"));
		
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
	 * 返回按钮 function
	 *
	 * @param [type] $type
	 * @param [type] $index
	 * @return void
	 */
	private function getButton($type,$index){
		$info=array(array(),array("上班","下班","fa-sun-o","fa-moon-o"),array("开始外勤","结束外勤","fa-sign-out","fa-circle-o-notch"),array("开始加班","结束加班","fa-clock-o","fa-hand-peace-o"));
		
		/*0:两个按钮都禁用,1:第一个按钮禁用，第二个按钮激活，2：第一个按钮激活，第二个按钮禁用*/
		$butInfo=array("<button class='btn disabled' data-type='{$type}' data-timetype='1'><i class='ace-icon fa {$info[$type][2]} align-top bigger-125'></i>{$info[$type][0]} </button><button  class='btn disabled' data-type='{$type}' data-timetype='2'><i class='ace-icon fa {$info[$type][3]} align-top bigger-125'></i>{$info[$type][1]}</button>","<button class='btn disabled' data-type='{$type}' data-timetype='1'><i class='ace-icon fa {$info[$type][2]} align-top bigger-125'></i>{$info[$type][0]}</button><button data-toggle='button' class='btn btn-success' data-type='{$type}' data-timetype='2'><i class='ace-icon fa {$info[$type][3]} align-top bigger-125'></i>{$info[$type][1]}</button>","<button data-toggle='button' class='btn btn-success' data-type='{$type}' data-timetype='1'><i class='ace-icon fa {$info[$type][2]} align-top bigger-125'></i>{$info[$type][0]}</button><button class='btn disabled' data-type='{$type}' data-timetype='2'><i class='ace-icon fa {$info[$type][3]} align-top bigger-125'></i>{$info[$type][1]}</button>");

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
		
		if($type==2){
			/*状态是外勤*/
			$aapplyData=$this->aapply->seekApply($user_code,$type,$date);
			if($aapplyData==null){
				return $this->getButton($type,0);
			}	
		}
		if($type>2){
			/*状态是加班*/
			$aapplyData=$this->aapply->seekApply($user_code,$type);	
			
			if($aapplyData==null){
				
				$aapplyData=$this->aapply->seekApply($user_code,4);
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
			}
			$checkinData=$this->acheckin->seekCheckin($user_code,$type,null,null,$aapplyData["aapply_id"]);
		}else{
			$checkinData=$this->acheckin->seekCheckin($user_code,$type,$date);
		}
	
		if(count($checkinData)>0){
			if(count($checkinData)>1){
				return $this->getButton($type,0);
			}else{
				return $this->getButton($type,1);
			}
		}else{
			$dates=split("-", $date);
			$thisDay=$this->arecord->isWeekday($dates[0],$dates[1],$dates[2]);
			/*判断是否工作日*/
			if($thisDay==false){
				return $this->getButton($type,0);
			}else{
				return $this->getButton($type,2);
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
		// $MonthRec["rec"]=$this->arecord->getMonthRec($this->selfUser["user_code"],$dates[0],$dates[1])[$dates[2]];
		$MonthRec["rec"]["forenoon"]["worktime"]= $forenoon;
		$MonthRec["rec"]["afternoon"]["worktime"]= $afternoon;
		$MonthRec["rec"]["forenoon"]["type"]= $type;
		$MonthRec["rec"]["afternoon"]["type"]= $type;
		$MonthRec["forenoon"]= $forenoon;
		$MonthRec["afternoon"]= $afternoon;
		return $MonthRec;
	}
	// private function predictSovertime($startTime){
	// 	$date=split(" ",$startTime);
	// 	if($startTime<$date[0]." ".$this->timeNode["MF"]){
	// 		$forenoon=time_reduce($startTime,$date[0]." ".$this->timeNode["MF"]);
	// 		$time=strtotime($startTime)+28800-5400;
	// 	}if($startTime>=$date[0]." ".$this->timeNode["MF"] && $startTime<$date[0]." ".$this->timeNode["AO"]){
	// 		$time=strtotime($this->timeNode["AO"])+28800;
	// 	}else{

	// 	}
	// 	return date("Y-m-d H:i:s",$time);
	// }

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
						if((time()> strtotime($applyArray["aapply_schedule"]." 18:00:00")) && I("remedy")=="false"){
							$this->ajaxReturn(array("status"=>"0","msg"=>"申请超时了"));

						}else{
							//**再加一个判断上班时间是否打卡了
							$checkin=$this->acheckin->hasCheckin($this->selfUser["user_code"],1,1,$applyArray["aapply_schedule"]);
							if($checkin==null){
								$this->ajaxReturn(array("status"=>"0","msg"=>"没有上班打卡记录不能申请加班！"));
							}else{
								$this->acheckin->checkin(array("acheckin_code"=>$this->selfUser["user_code"],"acheckin_checkinway"=>3,"acheckin_type"=>3,"acheckin_timetype"=>1,"acheckin_addtime"=>date("Y-m-d H:i:s",time()),"acheckin_checkintime"=>$applyArray["aapply_schedule"]." 18:30:00"));
							}
						}
					}
					
					
					if(I("type")==5 && (time()> strtotime($applyArray["aapply_schedule"]." 09:00:00")) && I("remedy")=="false"){
						$this->ajaxReturn(array("status"=>"0","msg"=>"申请超时了"));

					}

					// $this->aapply->addApply($applyArray);
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
			$result=$this->aapply->addApply($applyArray);
			if($result>0){
				$this->ajaxReturn(array("status"=>"1","msg"=>"申请提交成功！"));
			}else{
				$this->ajaxReturn(array("status"=>"0","msg"=>$result));
			}
			
		}
	}
}