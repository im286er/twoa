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