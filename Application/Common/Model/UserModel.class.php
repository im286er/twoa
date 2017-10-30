<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-06 13:52:07
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-10 09:56:45
 */
namespace Common\Model;
use Common\Model\AmongModel;
class UserModel extends AmongModel{
	protected $trueTableName = 'oa_user';
	protected $tableNameAs = 'oa_user u';
	protected $fields=array("user_id","user_username","user_name","user_code","user_passwd","user_company","user_department","user_group","user_place","user_roles","user_role","user_director","user_phone","user_avatar","user_sex","user_born","user_lastlogin","user_entry","user_quit","user_login","user_state",);
	protected $fieldAs=array("DISTINCT(u.user_id)","u.user_username user_username","u.user_name user_name","u.user_code user_code","u.user_company user_company","u.user_department user_department","u.user_group user_group","u.user_place user_place","u.user_roles user_roles","u.user_role user_role","u.user_director user_director","u.user_phone user_phone","u.user_avatar user_avatar","u.user_born user_born","u.user_sex user_sex","u.user_lastlogin user_lastlogin","u.user_entry user_entry","u.user_login user_login","u.user_state user_state");

	protected $fieldStr="DISTINCT(u.user_id),u.user_username user_username,u.user_name user_name,u.user_code user_code,c.company_name user_company,d.department_name user_department,g.group_name user_group,p.place_name user_place,ur.role_name user_roles,r.role_name user_role,u2.user_name user_director,u.user_phone user_phone,u.user_avatar user_avatar,u.user_born user_born,u.user_sex user_sex,u.user_lastlogin user_lastlogin,u.user_entry user_entry,u.user_login user_login,CASE u.user_state WHEN 0 THEN '未激活' WHEN 1 THEN '在职' ELSE '离职' END user_status,u.user_state user_state,u.user_quit user_quit";
	protected $joins=array("left join oa_user u2 on u2.user_code=u.user_director ","left join oa_company c on u.user_company=c.company_id ","left join oa_department d on u.user_department=d.department_id","left join oa_place p on u.user_place=p.place_id","left join oa_group g on g.group_id= u.user_group","left join oa_role ur on u.user_roles=ur.role_id","left join oa_role r on u.user_role=r.role_id");
	/**
	 * [search_all description]
	 * @param  [type] $start     [description]
	 * @param  [type] $limit     [description]
	 * @param  array  $dataArray [description]
	 * @return [type]            [description]
	 */
	function search_all($start,$limit,$dataArray=array()){
		if(!$this->has_auth("select")) return false;

		$tableObject=$this->table($this->tableNameAs)->field($this->fieldStr)->join($this->joins)->limit($start.','.$limit);
		if (empty($dataArray)){
			return $tableObject->select();
		}else{
			$newDataArray=array();
			foreach ($dataArray as $key => $value) {
				$newDataArray["u.".$key]=$value;
			}
			return $tableObject->where($newDataArray)->select();
			// echo $this->getLastSql();
		}
	}	 
	/**
	 * [find_user 查找指定用户]
	 * @param  [type]  $user_id [用户id]
	 * @param  boolean $show    [是否联表显示部门等文本，false则默认查显示id]
	 * @return [type]           [description]
	 */
	function find_user($user_id,$show=false){
		if(!$this->has_auth("select")) return false;
		if($show==false){
			return $this->table($this->tableNameAs)->where(array("u.user_id"=>$user_id))->find();
			 // echo $this->getLastSql();
		}else{
			return $this->table($this->tableNameAs)->field($this->fieldStr)->join($this->joins)->where("u.user_id=".$user_id)->find();
		}
	}


	/**
	 * [get_new_code 获取最新的员工编码]
	 * @return [type] [description]
	 */
	function get_new_code(){
		if(!$this->has_auth("select")) return false;

		$userData=$this->table($this->tableNameAs)->field("u.user_code")->order("u.user_code DESC")->find();
		return $userData["user_code"]+1;
	}

	/**
	 * [get_manager 查找管理人员]
	 * @param  [type]  $dapartment [隶属的部门]
	 * @param  integer $group      [隶属的组别，默认0则直接查询部门]
	 * @return [type]              [description]
	 */
	function get_manager($dapartment=0,$group=0){
		if(!$this->has_auth("select")) return false;

		$resultUser=$this->table($this->tableNameAs)->field("u.user_name")->join("left join oa_place p on user_place=p.place_id where p.place_department={$dapartment} and p.place_group={$group} and p.place_manager=1")->find();
		if(isset($resultUser["user_name"])){
			return $resultUser["user_name"];
		}else{
			return "";
		}
	}
	function searchManager(){
		if(!$this->has_auth("select")) return false;
		return $this->field("user_code,user_name")->join("oa_place on place_id=oa_user.user_place")->where("oa_place.place_manager=1")->select();
	}
	/**
	 * [show_director 显示主要的上级人员]
	 * @param  [type]  $place_department [相关的部门id]
	 * @param  boolean $place_group      [是否查询分组，默认不差分组下的管理]
	 * @return [type]                    [description]
	 */
	function show_director($place_department=0,$place_group=true){
		if(!$this->has_auth("select")) return false;

		if($place_group==true){
			return $this->query("select user_name,user_code from oa_user where user_place in (select place_id from oa_place where (find_in_set({$place_department},place_extent)) or (place_manager=1 and place_department={$place_department} and place_group=0))");
		}else{
			return $this->query("select user_name,user_code from oa_user where user_place in (select place_id from oa_place where (find_in_set({$place_department},place_extent)) or (place_manager=1 and place_department={$place_department}))");
		}
		
	}

	/**
	 * [set_user 修改用户]
	 * @param [type] $user_id   [指定用户id]
	 * @param [type] $dataArray [更新的数据]
	 */
	function set_user($user_id,$dataArray){
		if(!$this->has_auth("update")) return false;

		return $this->where(array("user_id"=>$user_id))->data($dataArray)->save();
	}

	/**
	 * [set_state 修改状态]
	 * @param [type] $user_id    [description]
	 * @param [type] $user_state [description]
	 */
	function set_state($user_id,$user_state=1){
		if(!$this->has_auth("update")) return false;

		$dataArray=array("user_state"=>$user_state);
		if($user_state==1){
			$dataArray["user_entry"]=date("Y-m-d");
			$dataArray["user_quit"]="0000-00-00";
		}else if($user_state==0){
			$dataArray["user_entry"]="0000-00-00";
			$dataArray["user_quit"]="0000-00-00";
		}else{
			$dataArray["user_quit"]=date("Y-m-d");
		}
		return $this->where(array("user_id"=>$user_id))->save($dataArray);
	}

	/**
	 * [has_username 判断用户是否存在]
	 * @param  [type]  $user_username [用户名]
	 * @return boolean                [description]
	 */
	function has_username($user_username){
		if(!$this->has_auth("select")) return false;

		return $this->field("user_id")->where(array("user_username"=>$user_username))->find()["user_id"];

	}

	/**
	 * [nameTransform 通过编码获取用户姓名]
	 * @param  [type]  $value [药要转换的值]
	 * @param  integer $type  [转换的类型：1，username 转 name 和code；2，name 转 username 和 code（可能存在多个）；3 or other，code 转 username 和 name]
	 * @return [type]         [description]
	 */
	function nameTrans($value,$type=1){
		if(!$this->has_auth("select")) return false;

		if($type==1){
			return $this->field("user_id,user_name,user_code")->where(array("user_username"=>$value))->find();
		}else if($type==2){
			return $this->field("user_id,user_username,user_code")->where(array("user_name"=>$value))->select();
		}else{
			return $this->field("user_id,user_name,user_username")->where(array("user_code"=>$value))->find();
		}
	}
	/**
	 * Undocumented function 只搜索用户名和code
	 *
	 * @param [type] $condition
	 * @param [type] $start
	 * @param [type] $limit
	 * @return void
	 */
	function searchNameCode($condition,$start=0,$limit=0){
		if(!$this->has_auth("select")) return false;
		if($limit==0){
			return $this->field("user_name,user_code")->where($condition)->select();
		}
		return $this->field("user_name,user_code")->where($condition)->limit($start,$limit)->select();
	}
	/**
	 * Undocumented function 添加用户
	 *
	 * @param [type] $userInfo
	 * @return void
	 */
	function addUser($userInfo){
		if(!$this->has_auth("insert")) return false;
		return $this->add($userInfo);;
	}
}
