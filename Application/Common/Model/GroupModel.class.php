<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-11 14:14:24
 * @Last Modified by:   vition
 * @Last Modified time: 2017-07-24 12:56:57
 */
namespace Common\Model;
use Common\Model\AmongModel;
class GroupModel extends AmongModel{
	protected $trueTableName = 'oa_group'; 
	protected $fields = array('group_id', 'group_name','group_department');

	/**
	 * 查询分组
	 * @param  [type] $start [起始值]
	 * @param  [type] $limit [限制条数]
	 * @return [type]        [上述两个参数都为空的时候，默认查询所有；$start存在而$limit为空的时候，查询条数；两个参数都存在则查询指定起始和限制条数]
	 */
	function search_group($group_department=0,$start="",$limit=""){
		if(!$this->has_auth("select")) return false;

		$group=$this->where(array("group_department"=>$group_department));
		if($start=="" && $limit==""){
			return $groupData=$group->select();
		}else if($start!="" && $limit==""){
			return $groupData=$group->limit("{$start}")->select();
		}else{
			return $groupData=$group->limit("{$start},{$limit}")->select();
		}
	}

	/**
	 * 查找分组
	 * @param  [type] $group_id [description]
	 * @return [type]           [description]
	 */
	function find_group($group_id){
		if(!$this->has_auth("select")) return false;

		return $this->where(array("group_id"=>$group_id))->find();
	}

	/**
	 * 判断分组
	 * @param  [type]  $group_department [分组指定的部门]
	 * @param  [type]  $group_name       [分组的名称]
	 * @return boolean                   [description]
	 */
	function is_group($group_department,$group_name){
		if(!$this->has_auth("select")) return false;

		return $this->where(array("group_name"=>$group_name,"group_department"=>$group_department))->find();
	}
	/**
	 * 修改分组
	 * @param [type] $group_id         [分组id]
	 * @param [type] $group_name      [分组名字]
	 * @param [type] $group_department [分组的指定部门]
	 */
	function set_group($group_id,$group_name,$group_department){
		if(!$this->has_auth("update")) return false;

		$groupInfo=$this->is_group($group_department,$group_name);
		if($groupInfo==""){
			return $this->where(array("group_id"=>$group_id))->save(array("group_name"=>$group_name,"group_department"=>$group_department));
		}else{
			return "分组名已存在";
		}
	}
	/**
	 * 新增分组
	 * @param [type] $group_department [分组指定的部门]
	 * @param [type] $group_name       [分组的名称]
	 */
	function add_group($group_department,$group_name){
		if(!$this->has_auth("insert")) return false;

		if($this->is_group($group_department,$group_name)==""){
			return $this->add(array("group_name"=>$group_name,"group_department"=>$group_department));
		}else{
			return "分组名已存在";
		}
	}

	/**
	 * 删除分组
	 * @param  [type] $group_id [指定的分组id]
	 * @return [type]           [description]
	 */
	function del_group($group_id){
		if(!$this->has_auth("delete")) return false;

		return $this->where(array("group_id"=>$group_id))->delete();
	}
}