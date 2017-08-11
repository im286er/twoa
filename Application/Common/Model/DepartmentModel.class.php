<?php
/**
 * 部门管理
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-11 12:15:19
 * @Last Modified by:   vition
 * @Last Modified time: 2017-07-24 12:56:44
 */
namespace Common\Model;
use Common\Model\AmongModel;
class DepartmentModel extends AmongModel{
	protected $trueTableName = 'oa_department'; 
	protected $fields = array('department_id', 'department_name','department_leader');

	/**
	 * 查询部门
	 * @param  [type] $department_leader [是否为管理层，默认0非]
	 * @param  [type] $start [起始值]
	 * @param  [type] $limit [限制条数]
	 * @return [type]        [上述两个参数都为空的时候，默认查询所有；$start存在而$limit为空的时候，查询条数；两个参数都存在则查询指定起始和限制条数]
	 */
	function search_department($department_leader=0,$start=0,$limit=0,$condition=""){
		if(!$this->has_auth("select")) return false;

		if(is_array($condition)){
			$this->where($condition);
		}
		if($start==0 && $limit==0){
			return $this->select();
		}else if($start>0 && $limit==0){
			return $this->limit("{$start}")->select();
		}else{
			return $this->limit("{$start},{$limit}")->select();
		}	
	}

	/**
	 * [查找部门]
	 * @param  [int] $department_id   部门id
	 * @param  string $department_name 部门名字
	 * @param  [int] $department_leader 大于0的时候判断为管理层
	 * @return [type]                  [当第二个参数不为空的时候第一个参数失效]
	 */
	function find_department($department_id,$department_name="",$department_leader=0){
		if(!$this->has_auth("select")) return false;

		if($department_name!=""){
			$condition['department_name']=$department_name;
		}else if($department_leader>0){
			$condition['department_leader']=$department_leader;
		}else{
			$condition['department_id']=$department_id;
		}
		return $this->where($condition)->find();
	}
	/**
	 * [is_department 判断部门]
	 * @param  [type]  $department_name   [要判断的部门名]
	 * @param  integer $department_leader [是否属于管理层]
	 * @return boolean                    [description]
	 */
	function is_department($department_name,$department_leader=0){
		if(!$this->has_auth("select")) return false;

		return $this->where(array("department_name"=>$department_name,"department_leader"=>$department_leader))->find();
	}
	/**
	 * 修改部门
	 * @param [type] $department_id   [指定的部门id]
	 * @param [type] $department_name [修改后的部门名]
	 * @param [type] $department_name [是否为管理层，默认非]
	 */
	function set_department($department_id,$department_name,$department_leader=0){
		if(!$this->has_auth("update")) return false;

		$reaultArray=$this->is_department($department_name,$department_leader);
		if(empty($reaultArray)){
			return $this->where(array("department_id"=>$department_id))->save(array("department_name"=>$department_name,"department_leader"=>$department_leader));
		}else{
			return "部门名已存在";
		}
	}

	/**
	 * 新增部门
	 * @param [type] $department_name 新增的部门名
	 */
	function add_department($department_name,$department_leader=0){
		if(!$this->has_auth("insert")) return false;

		$reaultArray=$this->is_department($department_name,$department_leader);
		
		if(empty($reaultArray)){
			return $this->add(array("department_name"=>$department_name,"department_leader"=>$department_leader));
		}else{
			return "部门名已存在";
		}
	}

	/**
	 * 删除指定的部门
	 * @param  [type] $department_id [指定的公司名]
	 * @return [type]             
	 */
	function del_department($department_id){
		if(!$this->has_auth("delete")) return false;

		return $this->where(array("department_id"=>$department_id))->delete();
	}

}