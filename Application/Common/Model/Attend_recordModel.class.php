<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-09 18:31:13
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-09 18:59:10
 */
namespace Common\Model;
use Common\Model\AmongModel;
class Attend_recordModel extends AmongModel{
	protected $trueTableName = 'oa_attend_record'; 
	protected $fields = array('arecord_id', 'arecord_code','arecord_year','arecord_month','arecord_json','arecord_count','arecord_remedy');

	/**
	 * [getWeekday 获取工作日]
	 * @param  [type] $arecord_year  [年]
	 * @param  [type] $arecord_month [月]
	 * @return [type]                [description]
	 */
	function getWeekday($arecord_year,$arecord_month){
		if(!$this->has_auth("select")) return false;
		$result=$this->field("arecord_json")->where(array("arecord_code"=>"0","arecord_year"=>$arecord_year,"arecord_month"=>$arecord_month))->find()["arecord_json"];
		return split(",", $result);

	}

	function isWeekday($arecord_year,$arecord_month,$day){
		if(!$this->has_auth("select")) return false;
		$month=$this->getWeekday($arecord_year,$arecord_month);
		if(array_search($day, $month)!==false){
			return true;
		}else{
			return false;
		}
		
	}

}