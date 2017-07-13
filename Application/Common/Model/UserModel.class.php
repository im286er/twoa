<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-06 13:52:07
 * @Last Modified by:   vition
 * @Last Modified time: 2017-07-13 12:13:09
 */
namespace Common\Model;
use Think\Model;
class UserModel extends Model{
	protected $trueTableName = 'oa_user u';
	/**
	 * [search_all description]
	 * @param  [type] $start     [description]
	 * @param  [type] $limit     [description]
	 * @param  array  $dataArray [description]
	 * @return [type]            [description]
	 */
	function search_all($start,$limit,$dataArray=array()){
		$tableObject=$this->field("DISTINCT(user_id),user_name,user_code,c.company_name user_company,d.department_name user_department,g.group_name user_group,p.place_name user_place,r.role_name user_role,user_director,user_phone,user_avatar,user_born,user_sex,user_lastlogin,user_entry,user_login,CASE user_state WHEN 0 THEN '未激活' WHEN 1 THEN '在职' ELSE '离职' END user_status,user_state")->join(array("left join oa_company c on u.user_company=c.company_id ","left join oa_department d on u.user_department=d.department_id","left join oa_place p on u.user_place=p.place_id","left join oa_group g on g.group_id= u.user_group","left join oa_role r on u.user_role=r.role_id"))->limit($start.','.$limit);
		if (empty($dataArray)){
			return $tableObject->select();
		}else{
			return $tableObject->where($dataArray)->select();
			return $this->getLastSql();

		}
	}	 
	function find_user($user_id){
		return $this->where("user_id=".$user_id)->find();
	}

	/**
	 * [get_new_code 获取最新的员工编码]
	 * @return [type] [description]
	 */
	function get_new_code(){
		$userData=$this->field("user_code")->order("user_code DESC")->find();
		return $userData["user_code"]+1;
	}

	/**
	 * [get_manager 查找管理人员]
	 * @param  [type]  $dapartment [隶属的部门]
	 * @param  integer $group      [隶属的组别，默认0则直接查询部门]
	 * @return [type]              [description]
	 */
	function get_manager($dapartment,$group=0){
		$resultUser=$this->field("user_name")->join("left join oa_place p on user_place=p.place_id where p.place_department={$dapartment} and p.place_group={$group} and p.place_manager=1")->find();
		if(isset($resultUser["user_name"])){
			return $resultUser["user_name"];
		}else{
			return "";
		}
	}
}
