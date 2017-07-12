<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-06 13:52:07
 * @Last Modified by:   vition
 * @Last Modified time: 2017-07-11 17:43:31
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
		$tableObject=$this->field("DISTINCT(user_id),user_name,user_code,c.config_value user_company,d.department_name user_department,g.group_name user_group,p.place_name user_place,r.role_name user_role,user_director,user_phone,user_avatar,user_born,user_sex,user_lastlogin,user_entry,user_login,CASE user_state WHEN 0 THEN '未激活' WHEN 1 THEN '在职' ELSE '离职' END user_state")->join(array("left join oa_company c on u.user_company=c.oa_company ","left join oa_department d on u.user_department=d.department_id","left join oa_place p on u.user_place=p.place_id","left join oa_group g on g.group_id= u.user_group","left join oa_role r on u.user_role=r.role_id"))->limit($start.','.$limit);
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
}
