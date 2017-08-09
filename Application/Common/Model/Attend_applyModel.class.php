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
	function seekApply($user_code,$type,$date){
		return $this->where("aapply_code='{$user_code}' AND aapply_type='{$type}' AND aapply_schedule='{$date}'")->find();
	}
}
