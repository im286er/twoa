<?php
	//计算年龄
	function get_age($data){
		$bornYear=date("Y",strtotime($data));
		$nowYear=date("Y",time());
		return $nowYear-$bornYear;
	}
	//计算天数
	function get_day($data){
		$startYear=strtotime($data);
		$day=(time()-$startYear)/86400;

		return floor($day);
	}

	/**
	 * [date_format 日期转换格式]
	 * @param  [type] $dateStr [description]
	 * @return [type]          [description]
	 */
	function date2format($dateStr,$empty=false){

		if($dateStr=="0000-00-00"){
			if($empty==true){
				return "";
			}
			return date("Y-m-d");
		}else{
			return $dateStr;
		}
	}