<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-09 18:31:13
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-11 09:41:27
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
		$result=$this->field("arecord_json")->where(array("arecord_code"=>"0","arecord_year"=>$arecord_year,"arecord_month"=>(int)$arecord_month))->find()["arecord_json"];
		return split(",", $result);

	}
	/**
	 * [isWeekday 判断指定日期是否工作日]
	 * @method   isWeekday
	 * @Author   vition
	 * @DateTime 2017-08-09
	 * @param    [type]     $arecord_year  [年]
	 * @param    [type]     $arecord_month [月]
	 * @param    [type]     $day           [日]
	 * @return   boolean                   [description]
	 */
	function isWeekday($arecord_year,$arecord_month,$day){
		if(!$this->has_auth("select")) return false;
		$month=$this->getWeekday($arecord_year,$arecord_month);
		if(array_search((int)$day, $month)!==false){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * [getMonthRec 获取指定月份的考勤记录，无返回false]
	 * @method   getMonthRec
	 * @Author   vition
	 * @DateTime 2017-08-09
	 * @param    [type]      $arecord_code  [人员编码]
	 * @param    [type]      $arecord_year  [年]
	 * @param    [type]      $arecord_month [月]
	 * @return   [type]                     [description]
	 */
	function getMonthRec($arecord_code,$arecord_year,$arecord_month,$day=0){
		if(!$this->has_auth("select")) return false;
		$arecord_json=$this->field("arecord_json")->where(array("arecord_code"=>$arecord_code,"arecord_year"=>$arecord_year,"arecord_month"=>(int)$arecord_month))->find()["arecord_json"];
		if($arecord_json==null){
			$monthRec= $this->createMonthRec($arecord_code,$arecord_year,$arecord_month);
		}else{
			$monthRec= json_decode($arecord_json,true);
		}
		if($day>0){
			return $monthRec[(int)$day];
		}else{
			return $monthRec;
		}
	}

	/**
	 * [createMonthRec 新建月份记录]
	 * @method   createMonthRec
	 * @Author   vition
	 * @DateTime 2017-08-09
	 * @param    [type]         $arecord_code  [人员编码]
	 * @param    [type]         $arecord_year  [年]
	 * @param    [type]         $arecord_month [月]
	 * @return   [type]                        [description]
	 */
	function createMonthRec($arecord_code,$arecord_year,$arecord_month){
		if(!$this->has_auth("insert")) return false;
		$dayNum=cal_days_in_month(CAL_GREGORIAN, $arecord_month, $arecord_year);
		$monthRec=array();
		for ($day=1; $day <= $dayNum ; $day++) { 
			$monthRec[(int)$day]=array("forenoon"=>array("type"=>"","worktime"=>""),"afternoon"=>array("type"=>"","worktime"=>""));
		}
		$this->add(array("arecord_code"=>$arecord_code,"arecord_year"=>$arecord_year,"arecord_month"=>(int)$arecord_month,"arecord_json"=>json_encode($monthRec),"arecord_remedy"=>"3"));
		return $this->getMonthRec($arecord_code,$arecord_year,$arecord_month);
	}
	/**
	 * setMonthRec 计算考勤时间
	 *
	 * @param [type] $arecord_code
	 * @param [type] $arecord_year
	 * @param [type] $arecord_month
	 * @param [type] $json
	 * @param [type] $count
	 * @return void
	 */
	function setMonthRec($arecord_code,$arecord_year,$arecord_month,$json,$count,$operation=1){
		if(!$this->has_auth("update")) return false;
		if($operation==1){
			$allCount=$this->findCount($arecord_code,$arecord_year,$arecord_month)+$count;
		}else{
			$allCount=$this->findCount($arecord_code,$arecord_year,$arecord_month)-$count;
		}

		return $this->where(array("arecord_code"=>$arecord_code,"arecord_year"=>$arecord_year,"arecord_month"=>(int)$arecord_month))->save(array("arecord_json"=>$json,"arecord_count"=>$allCount));

	}
	/**
	 * [findCount 查询当月工时统计]
	 * @param  [type] $arecord_code  [description]
	 * @param  [type]         $arecord_code  [人员编码]
	 * @param  [type]         $arecord_year  [年]
	 * @param  [type]         $arecord_month [月]
	 * @return [type]         [description]
	 */
	function findCount($arecord_code,$arecord_year,$arecord_month){
		if(!$this->has_auth("select")) return false;

		return $this->field("arecord_count")->where(array("arecord_code"=>$arecord_code,"arecord_year"=>$arecord_year,"arecord_month"=>(int)$arecord_month))->find()["arecord_count"];
	}

	function reduceCount($arecord_code,$arecord_year,$arecord_month,$reduce){
		if(!$this->has_auth("update")) return false;
		return $this->field("arecord_count")->where(array("arecord_code"=>$arecord_code,"arecord_year"=>$arecord_year,"arecord_month"=>(int)$arecord_month))->setDec("arecord_count",$reduce);
	}

	/**
	 * [findCount 查询当月后补次数]
	 * @param  [type] $arecord_code  [description]
	 * @param  [type]         $arecord_code  [人员编码]
	 * @param  [type]         $arecord_year  [年]
	 * @param  [type]         $arecord_month [月]
	 * @return [type]         [description]
	 */
	function findRemedy($arecord_code,$arecord_year,$arecord_month){
		if(!$this->has_auth("select")) return false;
		return $this->field("arecord_remedy")->where(array("arecord_code"=>$arecord_code,"arecord_year"=>$arecord_year,"arecord_month"=>(int)$arecord_month))->find()["arecord_remedy"];

	}

}