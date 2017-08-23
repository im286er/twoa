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

	/**
	 * [blob_trans_file base64转文件]
	 * @param  [type] $blob [二进制文件]
	 * @param  [type] $name [文件名]
	 * @param  [type] $dir  [文件目录]
	 * @return [type]       [description]
	 */
	function blob_to_file($blob,$name,$dir){
		$root=__ROOT__ ;
		$url=preg_replace("/^\\".__ROOT__."/", ".", $dir);

		if(!is_dir($url)){
			mkdir($url);
		}

		//获取到一个data:image/jpeg;base64,数据
		$headData=explode(",",$blob);
		$headSB=explode(";",$headData[0]);
		$headS=explode("/",$headSB[0]);
		$suffix=$headS[1];
		$base64=base64_decode($headData[1]);
		$name.=".".$suffix;
		$fileName=$url."/".$name;
		$resource = fopen($fileName, 'w+');  
		fwrite($resource, $base64);  
		fclose($resource); 
		return $dir."/".$name;
	}
	
	/**
	 * 获取微信配置 function
	 *
	 * @return void
	 */
	function getWeixinConf(){
		$WxConf = fopen("WxConf.ini", 'r');
		return json_decode(fread($WxConf, filesize("WxConf.ini")),true);
	}

	/**
	 * time_reduce function 计算时间相减 $datetime2-$datetime1
	 *
	 * @param [type] $tdatetime1
	 * @param [type] $datetime2
	 * @return void
	 */
	function time_reduce($tdatetime1,$datetime2){
		if($datetime2>$tdatetime1){
			return round((strtotime($datetime2)-strtotime($tdatetime1))/3600,2);
		}
		return 0;
		
	}

	/**
	 * count_days function 计算两个时间之间的时间差天数，大减小
	 *
	 * @param [type] $datetime1
	 * @param [type] $datetime2
	 * @return void
	 */
	function count_days($datetime1,$datetime2){
		$stramp1=strtotime(split(" ",$datetime1)[0]);
		$stramp2=strtotime(split(" ",$datetime2)[0]);
		if($stramp1>$stramp2){
			$interval=$stramp1-$stramp2;
		}else{
			$interval=$stramp2-$stramp1;
		}
		return $interval/86400;
	}