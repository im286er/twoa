<?php
/**
 * 部门管理
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-11 12:15:19
 * @Last Modified by:   vition
 * @Last Modified time: 2017-07-13 15:53:32
 */
namespace Common\Model;
use Think\Model;
class DepartmentModel extends Model{
	protected $trueTableName = 'oa_department'; 
	protected $fields = array('department_id', 'department_name','department_leader');

	/**
	 * 查询部门
	 * @param  [type] $department_leader [是否为管理层，默认0非]
	 * @param  [type] $start [起始值]
	 * @param  [type] $limit [限制条数]
	 * @return [type]        [上述两个参数都为空的时候，默认查询所有；$start存在而$limit为空的时候，查询条数；两个参数都存在则查询指定起始和限制条数]
	 */
	function search_department($department_leader=0,$start="",$limit=""){
		if($start=="" && $limit==""){
			return $departmentData=$this->select();
		}else if($start!="" && $limit==""){
			return $departmentData=$this->limit("{$start}")->select();
		}else{
			return $departmentData=$this->limit("{$start},{$limit}")->select();
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
		return $this->where(array("department_name"=>$department_name,"department_leader"=>$department_leader))->find();
	}
	/**
	 * 修改部门
	 * @param [type] $department_id   [指定的部门id]
	 * @param [type] $department_name [修改后的部门名]
	 * @param [type] $department_name [是否为管理层，默认非]
	 */
	function set_department($department_id,$department_name,$department_leader=0){
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
		return $this->where(array("department_id"=>$department_id))->delete();
	}
}