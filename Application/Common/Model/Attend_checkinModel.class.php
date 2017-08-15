<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-07 18:39:02
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-10 12:07:15
 */
namespace Common\Model;
use Common\Model\AmongModel;
class Attend_checkinModel extends AmongModel{
	protected $trueTableName = 'oa_attend_checkin'; 
	protected $fields = array('acheckin_id', 'acheckin_code','acheckin_checkinway','acheckin_type','acheckin_timetype','acheckin_addtime','acheckin_checkintime','acheckin_location','acheckin_longlat','acheckin_picture',"acheckin_state","acheckin_tempstorage");

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
	function seekCheckin($user_code,$type,$date,$state=null){
		if(!$this->has_auth("select")) return false;
		if($state===null){
			return $this->where("date_format(acheckin_checkintime,'%Y-%m-%d')='{$date}' AND acheckin_code='{$user_code}' AND acheckin_type='{$type}'")->order("acheckin_checkintime")->select();
		}else{
			return $this->where("date_format(acheckin_checkintime,'%Y-%m-%d')='{$date}' AND acheckin_code='{$user_code}' AND acheckin_type='{$type}' AND acheckin_state ='{$state}'")->order("acheckin_checkintime")->select();
		}
		
	}

	/**
	 * [hasCheckin 判断指定打卡记录是否存在]
	 * @param  [type]  $acheckin_code     [人员code]
	 * @param  [type]  $acheckin_type     [打卡类型，1正常上班，2外勤，3加班]
	 * @param  [type]  $acheckin_timetype [打卡时间类型，1开始，2结束]
	 * @param  [type]  $date              [打卡发生时间，格式：2017-08-10]
	 * @return boolean                    [description]
	 */
	function hasCheckin($acheckin_code,$acheckin_type,$acheckin_timetype,$date){
		if(!$this->has_auth("select")) return false;
		return $this->field("acheckin_id")->where(array("acheckin_code"=>$acheckin_code,"acheckin_type"=>$acheckin_type,"acheckin_timetype"=>$acheckin_timetype))->where("date_format(acheckin_checkintime,'%Y-%m-%d')='{$date}'")->find()["acheckin_id"];
	}

	/**
	 * [checkin 打卡方法]
	 * @param  [type] $dataArray [打卡数据]
	 * @return [type]            [description]
	 */
	function checkin($dataArray){
		if(!$this->has_auth("insert")) return false;
		if($this->hasCheckin($dataArray["acheckin_code"],$dataArray["acheckin_type"],$dataArray["acheckin_timetype"],date("Y-m-d",strtotime($dataArray["acheckin_checkintime"])))==null){
			return $this->add($dataArray);
		}
		return false;
		
	}

	/**
	 * setCheckin function
	 *
	 * @param [type] $acheckin_id
	 * @param [type] $dataArray 
	 * @return void
	 */
	function setCheckin($acheckin_id,$dataArray){
		return $this->where(array("acheckin_id"=>$acheckin_id))->save($dataArray);
	}

}
