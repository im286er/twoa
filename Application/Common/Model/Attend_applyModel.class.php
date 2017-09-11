<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-07 18:24:40
 * @Last Modified by:   369709991@qq.com
 * @Last Modified time: 2017-09-08 23:27:22
 */
namespace Common\Model;
use Common\Model\AmongModel;
class Attend_applyModel extends AmongModel{
	protected $trueTableName = 'oa_attend_apply'; 
	protected $fields = array("aapply_id","aapply_code","aapply_type","aapply_inday","aapply_addtime","aapply_schedule","aapply_days","aapply_hours","aapply_reason",'aapply_project','aapply_proof',"aapply_approve","aapply_state","aapply_operation","aapply_remark");

	function searchApply($aapply_code,$condition=array(),$start=0,$limit=0,$approve=false){
		if(!$this->has_auth("select")) return false;
		$this->table("oa_attend_apply ap")->field(array_merge($this->fields,array("ci.config_value aapply_indays","ct.config_value aapply_types","u.user_name aapply_username")))->join("left join oa_user u on u.user_code=ap.aapply_code")->join("left join oa_config ct on ct.config_class='aapply_type' AND ct.config_key=ap.aapply_type")->join("left join oa_config ci on ci.config_class='aapply_inday' AND ci.config_key=ap.aapply_inday")->order("aapply_schedule DESC");
		if($limit>0){
			$this->limit($start.','.$limit);
		}
		if($aapply_code>0){
			if($approve==true){
				$this->where("aapply_approve LIKE '%".$aapply_code."%'");
			}else{
				$this->where(array("aapply_code"=>$aapply_code));
			}
			
		}
		if(!empty($condition)){
			// print_r($condition);
			return $this->where($condition)->select();
		}
		return $this->select();
	}
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
	 * getAppy function 根据id取申请信息
	 *
	 * @param [type] $aapply_id
	 * @return void
	 */
	function getAppy($aapply_id){
		if(!$this->has_auth("select")) return false;
		$this->table($this->trueTableName." ap")->field(array_merge($this->fields,array("u.user_name aapply_codes","ct.config_value aapply_types","ci.config_value aapply_indays")))->join("left join oa_user u on u.user_code=ap.aapply_code")->join("left join oa_config ct on ct.config_class='aapply_type' AND ct.config_key=ap.aapply_type")->join("left join oa_config ci on ci.config_class='aapply_inday' AND ci.config_key=ap.aapply_inday");
		return $this->where(array("aapply_id"=>$aapply_id))->find();
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

	/**
	 * hasApply function 判断指定日期是否存在相同的申请
	 *
	 * @param [type] $user_code 人员编码
	 * @param [type] $aapply_type 申请类型
	 * @param [type] $aapply_inday 当天的时间类型，上午，下午，全天
	 * @param [type] $aapply_schedule 预计时间
	 * @return boolean
	 */
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
		$sameApply=$this->sameDate($user_code,$aapply_schedule,$aapply_inday);
		if($apply!=null){
			return true;
		}
		return false;
	}

	/**
	 * sameDate function 判断同一天内是否存在其他申请，避免冲突
	 *
	 * @param [type] $user_code
	 * @param [type] $aapply_inday
	 * @param [type] $aapply_schedule
	 * @return void
	 */
	function sameDate($user_code,$aapply_schedule,$aapply_inday=0){
		if(!$this->has_auth("select")) return true;

		$resultArray=$this->where("(aapply_schedule>='{$aapply_schedule}' AND '{$aapply_schedule}'<date_sub(aapply_schedule,interval -aapply_days day) and aapply_days>0) or (aapply_schedule='{$aapply_schedule}') AND aapply_inday='{$aapply_inday}'")->select();
		if($resultArray!=null){
			return true;
		}
		return false;
	}

	/**
	 * getApplicant function 获取申请人
	 *
	 * @param [type] $user_code
	 * @param [type] $condition
	 * @return void
	 */
	function getApplicant($user_code,$condition=null){
		if(!$this->has_auth("select")) return true;
		$this->table($this->trueTableName." a")->field("aapply_code, u.user_name aapply_codes")->join("left join oa_user u on u.user_code=a.aapply_code")->group("aapply_code");
		$this->where("aapply_approve LIKE '%".$user_code."%'");
		if($condition!=null){
			$this->where($condition);
		}
		return $this->select();
	}

	/**
	 * setApply function
	 *
	 * @param [type] $aapply_id 要修改的id
	 * @param [type] $dataArray 更新的数据
	 * @return void
	 */
	function setApply($aapply_id,$dataArray){
		if(!$this->has_auth("update")) return true;
		return $this->where(array("aapply_id"=>$aapply_id))->save($dataArray);
	}
}
