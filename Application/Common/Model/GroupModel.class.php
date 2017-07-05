<?php
namespace Common\Model;
use Think\Model;
class GroupModel extends Model {
	protected $trueTableName = 'oa_group'; 
	protected $fields = array('group_id', 'group_name', 'subgroup_id','subgroup_name','subgroup_group','place_id','place_name','place_group','place_subgroup','place_manager','role_id','role_name','role_upper');
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
	function find_subgroup($subgroup_id){
		return $this->table("oa_subgroup")->where(array("subgroup_id"=>$subgroup_id))->find();
	}

	function is_subgroup($group_id,$subgroup_name){
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

	function set_subgroup($subgroup_id,$subgroup_group,$subgroup_name){
		$subInfo=$this->is_subgroup($subgroup_group,$subgroup_name);
		if($subInfo==""){
			return $this->table("oa_subgroup")->where(array("subgroup_id"=>$subgroup_id))->save(array("subgroup_name"=>$subgroup_name,"subgroup_group"=>$subgroup_group));
		}else{
			return "分组名已存在";
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
		if($this->is_subgroup($group_id,$subgroup_name)==""){
			return $this->table("oa_subgroup")->add(array("subgroup_name"=>$subgroup_name,"subgroup_group"=>$group_id));
		}else{
			return "分组名已存在";
		}
	}
	/**
	* 新增place
	* @group_id 新增的group name 
	* @subgroup_name 新建分组的名称
	*/
	function add_place($group_id,$place_name,$manager,$subgroup_id=0){
		//is_place($group_id,$place_name,$subgroup_id=0)
		if($this->is_place($group_id,$place_name,$manager,$subgroup_id)==""){
			return $this->table("oa_place")->add(array("place_name"=>$place_name,"place_group"=>$group_id,"place_subgroup"=>$subgroup_id,"place_manager"=>$manager));
			// return $this->getLastSql();
		}else{
			return "该职位在部门/分组中已存在";
		}
	}
	function set_place($place_id,$group_id,$place_name,$manager,$subgroup_id=0){
		//is_place($group_id,$place_name,$subgroup_id=0)
		$isResult=$this->is_place($group_id,$place_name,$manager,$subgroup_id);
		// return $isResult ;
		if($isResult==""){
			// return $isResult ;
			return $this->table("oa_place")->where(array("place_id"=>$place_id))->save(array("place_name"=>$place_name,"place_group"=>$group_id,"place_subgroup"=>$subgroup_id,"place_manager"=>$manager));

			// return $this->table("oa_place")->add(array("place_name"=>$place_name,"place_group"=>$group_id,"place_subgroup"=>$subgroup_id,"place_manager"=>$manager));
			// return $this->getLastSql();
		}else{
			return "该职位在部门/分组中已存在";
		}
	}
	/**
	* 删除group
	* @group_id 要删除的gorup id
	*/
	function del_group($group_id){
		return $this->where(array("group_id"=>$group_id))->delete();
	}

	function del_subgroup($subgroup_id){
		return $this->table("oa_subgroup")->where(array("subgroup_id"=>$subgroup_id))->delete();
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
	* @subgroup_id 如果>0表示该职位属于子分组下的
	*/
	function select_place($group_id,$subgroup_id=0){
		// if($subgroup_id>0){
			 // $this->table($this->trueTableName." g")->join("oa_place p on g.group_id=p.place_group")->join("oa_subgroup s on s.subgroup_id=p.place_subgroup")->where("g.group_id='{$group_id}' AND s.subgroup_id='{$subgroup_id}'")->select();
		return $this->table("oa_place")->where(array("place_group"=>$group_id,"place_subgroup"=>$subgroup_id))->select();
		// }else{
			// return $this->table($this->trueTableName." g")->join("oa_place p on g.group_id=p.place_group")->where("g.group_id='{$group_id}'")->select();
		// }
				echo $this->getLastSql();
	}

	function del_place($place_id){
		return $this->table("oa_place")->where(array("place_id"=>$place_id))->delete();
	}
	/**
	* 根据group id和subgroup id 和place name判断职位是否存在
	* @group_id 要查找的gorup id
	* @place_name 要查找的用户名
	* @subgroup_id 如果>0表示该职位属于子分组下的
	*/
	function is_place($group_id,$place_name,$subgroup_manager,$subgroup_id=0){
		return $this->table("oa_place")->where(array("place_group"=>$group_id,"place_name"=>$place_name,"place_subgroup"=>$subgroup_id,"place_manager"=>$subgroup_manager))->find();
		
		// return $this->getLastSql();
	}
	/**
	* 根据place id 查找place相关信息
	* @group_id 要查找的gorup id
	*/
	function find_place($place_id){
		return $this->table("oa_place")->where(array("place_id"=>$place_id))->find();
	}
	/**
	* 查找角色
	* @role_upper 要查的一级组id
	*/
	function select_role($role_upper=0){
		return $this->table("oa_role")->where(array("role_upper"=>$role_upper))->select();
	}
	/**
	* 判断角色是否存在
	* @role_name 要查的角色名
	* @role_upper 要查的一级组id
	*/
	function is_role($role_name,$role_upper=0){
		return $this->table("oa_role")->where(array("role_name"=>$role_name,"role_upper"=>$role_upper))->find();
	}
	/**
	* 新增角色
	* @role_name 角色名
	* @role_upper 角色上级
	*/
	function add_role($role_name,$role_upper=0){
		if($this->is_role($role_name,$role_upper)==""){
			return $this->table("oa_role")->add(array("role_name"=>$role_name,"role_upper"=>$role_upper));
		}else{
			return "角色名已存在"; 
		}
	}
	function set_role($role_id,$role_name,$role_upper=0){
		$resultData=$this->is_role($role_name,$role_upper);
		if($resultData==""){
			return $this->table("oa_role")->where(array('role_id' =>$role_id ))->save(array("role_name"=>$role_name,"role_upper"=>$role_upper));
		}else{
			return "角色名已存在"; 
		}
	}
	function del_role($role_id){
		return $this->table("oa_role")->where(array("role_id"=>$role_id))->delete();
	}

}