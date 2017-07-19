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
	 * [date_format description]
	 * @param  [type] $dateStr [description]
	 * @return [type]          [description]
	 */
	function date2format($dateStr){
		if($dateStr=="0000-00-00"){
			return date("Y-m-d");
		}else{
			return $dateStr;
		}
	}