<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-07 18:24:40
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-10 12:47:42
 */
namespace Common\Model;
use Common\Model\AmongModel;
class Attend_userModel extends AmongModel{
	protected $trueTableName = 'oa_attend_user'; 
	protected $fields = array('auser_id', 'auser_code','auser_eachday','auser_worktime','auser_annual');

	/**
	 * [find_auser 通过code查找用户考勤相关信息]
	 * @param  [type] $auser_code [用户code]
	 * @return [type]             [description]
	 */
	function find_auser($auser_code){
		if(!$this->has_auth("select")) return false;
		return $this->where(array("auser_code"=>$auser_code))->find();
	}
}
