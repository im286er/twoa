<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-11 15:37:56
 * @Last Modified by:   vition
 * @Last Modified time: 2017-07-20 16:55:12
 */
namespace Common\Model;
use Think\Model;

/**
 * 角色管理模型
 */
class RoleModel extends Model{
	protected $trueTableName = 'oa_role'; 
	protected $fields = array('role_id', 'role_name','role_upper');


	/**
	 * [search_role 查询角色]
	 * @param  integer $role_upper [角色分组id]
	 * @param  [type] $start [起始值]
	 * @param  [type] $limit [限制条数]
	 * @return [type]        [上述两个参数都为空的时候，默认查询所有；$start存在而$limit为空的时候，查询条数；两个参数都存在则查询指定起始和限制条数]
	 */
	function search_role($role_upper=0,$start="",$limit=""){
		$role=$this->where(array("role_upper"=>$role_upper));
		if($start=="" && $limit==""){
			return $role->select();
		}else if($start!="" && $limit==""){
			return $role->limit("{$start}")->select();
		}else{
			return $role->limit("{$start},{$limit}")->select();
		}
	}


	/**
	 * [find_role 查找角色]
	 * @param  [type] $place_id [职位id]
	 * @return [type]           []
	 */
	function find_role($role_id){
		return $this->where(array("role_id"=>$role_id))->find();
	}

	/**
	 * [is_role 判断角色]
	 * @param  [type]  $role_name  [角色名字]
	 * @param  integer $role_upper [角色分组id]
	 * @return boolean             []
	 */
	function is_role($role_name,$role_upper=0){
		return $this->where(array("role_name"=>$role_name,"role_upper"=>$role_upper))->find();
	}

	/**
	 * [set_role 修改角色]
	 * @param [type]  $role_id    [角色id]
	 * @param [type]  $role_name  [角色名字]
	 * @param integer $role_upper [角色分组]
	 */
	function set_role($role_id,$role_name,$role_upper=0){
		$resultData=$this->is_role($role_name,$role_upper);
		if($resultData==""){
			return $this->where(array('role_id' =>$role_id ))->save(array("role_name"=>$role_name,"role_upper"=>$role_upper));
		}else{
			return "角色名已存在"; 
		}
	}

	/**
	 * [add_role 新增角色]
	 * @param [type]  $role_name  [角色名称]
	 * @param integer $role_upper [角色分组id]
	 */
	function add_role($role_name,$role_upper=0){
		if($this->is_role($role_name,$role_upper)==""){
			return $this->add(array("role_name"=>$role_name,"role_upper"=>$role_upper));
		}else{
			return "角色名已存在"; 
		}
	}

	/**
	 * 删除角色
	 * @param  [type] $role_id [指定的橘色id]
	 * @return [type]           [description]
	 */
	function del_role($role_id){
		return $this->where(array("role_id"=>$role_id))->delete();
	}
}