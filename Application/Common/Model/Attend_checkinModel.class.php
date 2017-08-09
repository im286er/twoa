<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-07 18:39:02
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-10 00:17:06
 */
namespace Common\Model;
use Common\Model\AmongModel;
class Attend_checkinModel extends AmongModel{
	protected $trueTableName = 'oa_attend_checkin'; 
	protected $fields = array('acheckin_id', 'acheckin_code','acheckin_checkinway','acheckin_type','acheckin_timetype','acheckin_addtime','acheckin_checkintime','acheckin_location','acheckin_longlat','acheckin_picture');

	function search_checkin($acheckin_code,$acheckin_id=null,$condition=array()){
		if(!$this->has_auth("select")) return false;
		if($acheckin_id==null){
			// $acheckin=$this->where(array("acheckin_code"=>$acheckin_code));
		}else{
			// $acheckin=$this->where(array("acheckin_id"=>$acheckin_id));
		}
		if(!empty($condition)){
			// print_r($condition);
			return $this->where($condition)->select();
		}

		// return $acheckin->select();
	}

	/**
	 * [seekCheckin 寻找打卡记录]
	 * @param  [type] $user_code [人员code]
	 * @param  [type] $type      [类型，]
	 * @param  [type] $date      [日期，格式如：2017-08-09]
	 * @return [type]            [description]
	 */
	function seekCheckin($user_code,$type,$date){
		if(!$this->has_auth("select")) return false;
		return $this->where("date_format(acheckin_checkintime,'%Y-%m-%d')='{$date}' AND acheckin_code='{$user_code}' AND acheckin_type='{$type}'")->select()[];
	}


	function hasCheckin($acheckin_code,$acheckin_checkinway,$acheckin_type,$acheckin_timetype,$date){
		if(!$this->has_auth("select")) return false;
		return $this->filed("acheckin_id")->where(array("acheckin_code"=>$acheckin_code,"acheckin_checkinway"=>$acheckin_checkinway,"acheckin_type"=>$acheckin_type,"acheckin_timetype"=>$acheckin_timetype))->where("date_format(acheckin_checkintime,'%Y-%m-%d')='{$date}'")->find()["acheckin_id"];
	}
	function checkin($dataArray){
		if(!$this->has_auth("insert")) return false;
		if($this->hasCheckin($dataArray["acheckin_code"],$dataArray["acheckin_checkinway"],$dataArray["acheckin_type"],$dataArray["acheckin_timetype"],date("Y-m-d",strtotime($dataArray["acheckin_checkintime"])))==null){
			$this->add($dataArray);
		}
		
	}

}