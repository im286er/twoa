<?php
namespace Common\Model;
use Think\Model;
class GroupModel extends Model {
	protected $trueTableName = 'oa_group'; 
	protected $fields = array('group_id', 'group_name', 'subgroup_id','subgroup_name','subgroup_group');
	// protected $fields = array('group_id', 'group_name', 'email','_pk'=>'group_id','_autoinc' => true);
	/**
	* 查询oa_group表的数据，默认查所有
	* @start limit的起始位置
	* @limit limit的条数
	* 当只有一个参数的时候默认查询条数
	*/
	function select_group($start="",$limit=""){
		if($start=="" && $limit==""){
			return $groupData=$this->select();
		}else if($start!="" && $limit==""){
			return $groupData=$this->limit("{$start}")->select();
		}else{
			return $groupData=$this->limit("{$start},{$limit}")->select();
		}
	}
	/**
	* 查询指定的group
	* @group_id 要查询的group id
	* @group_name 默认值为空，如果当此参数不为空的时候group_id 参数失效，即通过group name查group id
	*/
	function find_group($group_id,$group_name=""){
		if($group_name!=""){
			return $this->where(array("group_name"=>$group_name))->find();
		}else{
			return $this->where(array("group_id"=>$group_id))->find();
		}

	}
	/**
	* 查询指定的subgroup 分组
	* @group_id 要查询的group id
	* @subgroup_name 分组名，不同部门允许分组相同，但是同一个部门分组不能相同
	*/
	function find_subgroup($group_id,$subgroup_name){
		return $this->table("oa_subgroup")->where(array("subgroup_name"=>$subgroup_name,"subgroup_group"=>$group_id))->find();
	}
	/**
	* 更新指定group名
	* @group_id 要更新的group id
	* @group_name 新的group name 
	*/
	function set_group($group_id,$group_name){
		if($this->find_group(0,$group_name)==""){
			return $this->where(array("group_id"=>$group_id))->save(array("group_name"=>$group_name));
		}else{
			return "部门名已存在";
		}
		// 
	}

	/**
	* 新增group
	* @group_name 新增的group name 
	*/
	function add_group($group_name){
		if($this->find_group(0,$group_name)==""){
			return $this->add(array("group_name"=>$group_name));
		}else{
			return "部门名已存在";
		}
	}
	/**
	* 新增subgroup
	* @group_id 新增的group name 
	* @subgroup_name 新建分组的名称
	*/
	function add_subgroup($group_id,$subgroup_name){
		if($this->find_subgroup($group_id,$subgroup_name)==""){
			return $this->table("oa_subgroup")->add(array("subgroup_name"=>$subgroup_name,"subgroup_group"=>$group_id));
		}else{
			return "分组名已存在";
		}
	}
	/**
	* 删除group
	* @group_id 要删除的gorup id
	*/
	function del_group($group_id){
		return $this->where(array("group_id"=>$group_id))->delete();
	}
	/**
	* 根据group id查分组
	* @group_id 要查找的gorup id
	*/
	function select_subgroup($group_id){
		return $this->table($this->trueTableName." g")->join("oa_subgroup s on g.group_id=s.subgroup_group")->where("g.group_id='{$group_id}'")->select();
	}

	/**
	* 根据group id查职位
	* @group_id 要查找的gorup id
	*/
	function select_place($group_id){
		return $this->table($this->trueTableName." g")->join("oa_place p on g.group_id=p.place_group")->where("g.group_id='{$group_id}'")->select();
	}
}