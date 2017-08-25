<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-07 18:24:40
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-09 15:46:41
 */
namespace Common\Model;
use Common\Model\AmongModel;
class Attend_applyModel extends AmongModel{
	protected $trueTableName = 'oa_attend_apply'; 
	protected $fields = array("aapply_id","aapply_code","aapply_type","aapply_inday","aapply_addtime","aapply_schedule","aapply_days","aapply_hours","aapply_reason","aapply_approve","aapply_state","aapply_operation","aapply_remark");

	/**
	 * [seekApply 查找申请记录]
	 * @param  [type] $user_code [关联的人员]
	 * @param  [type] $type      [考勤类型，]
	 * @param  [type] $date      [日期，格式如：2017-08-09]
	 * @return [type]            [description]
	 */
	function seekApply($user_code,$type,$date,$inday=0){
		if(!$this->has_auth("select")) return false;
		if($inday!=0){
			$indaySql=" AND aapply_inday='{$inday}'";
		}else{
			$indaySql="";
		}
		return $this->where("aapply_code='{$user_code}' AND aapply_type='{$type}' AND aapply_schedule='{$date}'".$indaySql)->find();
	}
	
	/**
	 * isApply 判断申请记录是否审批
	 *
	 * @param [type] $user_code
	 * @param [type] $type
	 * @param [type] $date
	 * @return boolean
	 */
	function isApply($user_code,$type,$date){
		if(!$this->has_auth("select")) return false;
		$result=$this->where(array("aapply_code="=>$user_code,"aapply_schedule"=>$date,"aapply_type"=>$type,"aapply_state"=>"1",))->find();
		if($result===null){
			return false;
		}
		return true;
	}

	/**
	 * addApply 添加申请
	 *
	 * @param [type] $dataArray 数据 数组
	 * @return void
	 */
	function addApply($dataArray){
		if(!$this->has_auth("insert")) return false;
		// $this->add($dataArray);
		if(!$dataArray["aapply_inday"]){
			$dataArray["aapply_inday"]=0;
		}
		$result=$this->hasApply($dataArray["aapply_code"],$dataArray["aapply_type"],$dataArray["aapply_inday"],$dataArray["aapply_schedule"]);
		var_dump($result);
		// print_r($dataArray);
		if(!$result){
			$this->add($dataArray);
		}

	}


	function hasApply($user_code,$aapply_type,$aapply_inday,$aapply_schedule){
		if(!$this->has_auth("select")) return true;
		$apply=$this->seekApply($user_code,$aapply_type,$aapply_schedule,$aapply_inday);
		if($apply!=null){
			return true;
		}else{
			if($aapply_inday>0){
				$applya=$this->seekApply($user_code,$aapply_type,$aapply_schedule);
				if($applya!=null){
					if($applya["aapply_inday"]==1 || $aapply_inday==1){
						return true;
					}
				}
			}
		}
		return false;
	}
}
