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
	