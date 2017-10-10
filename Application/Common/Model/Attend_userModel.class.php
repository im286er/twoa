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
	 * Undocumented function 搜索用户数据
	 *
	 * @param [type] $condition
	 * @param [type] $start
	 * @param [type] $limit
	 * @return void
	 */
	function searchAuser($condition,$start,$limit){
		if(!$this->has_auth("select")) return false;
		return $this->table("oa_user u")->field("user_name,c.company_name user_company,user_code,d.department_name user_department,g.group_name user_group,auser_eachday,auser_worktime,auser_annual")->join("left join oa_company c on u.user_company=c.company_id")->join("left join oa_department d on u.user_department=d.department_id")->join("left join oa_group g on g.group_id= u.user_group")->join("left join oa_attend_user au on u.user_code=au.auser_code")->where($condition)->limit($start.','.$limit)->select();

	}

	/**
	 * [find_auser 通过code查找用户考勤相关信息]
	 * @param  [type] $auser_code [用户code]
	 * @return [type]             [description]
	 */
	function find_auser($auser_code){
		if(!$this->has_auth("select")) return false;
		return $this->where(array("auser_code"=>$auser_code))->find();
	}
	/**
	 * Undocumented function 修改用户考勤基本数据
	 *
	 * @param [type] $userData
	 * @return void
	 */
	function setAuser($userData){
		if(!$this->has_auth("insert")) return false;
		$result=$this->find_auser($userData["auser_code"]);
		if($result==null){
			return $this->add($userData);
		}else{
			return $this->where(array("auser_code"=>$userData["auser_code"]))->save($userData);
		}
	}
}
