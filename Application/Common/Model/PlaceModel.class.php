<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-11 15:01:41
 * @Last Modified by:   vition
 * @Last Modified time: 2017-07-24 12:57:15
 */
namespace Common\Model;
use Common\Model\AmongModel;

class PlaceModel extends AmongModel{
	protected $trueTableName = 'oa_place'; 
	protected $fields = array('place_id', 'place_name','place_department','place_group','place_manager','place_extent','place_role');

	/**
	 * [查询职位]
	 * @param  [type]  $place_department [职位隶属的部门]
	 * @param  integer $place_group      [职位隶属的分组]
	 * @param  [type]  $start [起始值]
	 * @param  [type]  $limit [限制条数]
	 * @return [type]        [上述两个参数都为空的时候，默认查询所有；$start存在而$limit为空的时候，查询条数；两个参数都存在则查询指定起始和限制条数]
	 */
	function search_place($place_department,$place_group=0,$start="",$limit=""){
		if(!$this->has_auth("select")) return false;

		$place=$this->where(array("place_department"=>$place_department,"place_group"=>$place_group));
		if($start=="" && $limit==""){
			return $placeData=$place->select();
		}else if($start!="" && $limit==""){
			return $placeData=$place->limit("{$start}")->select();
		}else{
			return $placeData=$place->limit("{$start},{$limit}")->select();
		}
	}

	/**
	 * [find_place 查找职位]
	 * @param  [type] $place_id [职位id]
	 * @return [type]           []
	 */
	function find_place($place_id){
		if(!$this->has_auth("select")) return false;

		return $this->where(array("place_id"=>$place_id))->find();
	}

	/**
	 * Undocumented function 通过条件查找到指定一条的职位信息
	 *
	 * @param [type] $conditionArray
	 * @return void
	 */
	function seekPlace($conditionArray){
		if(!$this->has_auth("select")) return false;
		return $this->where($conditionArray)->find();
	}

	/**
	 * [is_place 判断职位]
	 * @param  [type]  $place_department [职位隶属部门]
	 * @param  [type]  $place_name       [职位名称]
	 * @param  [type]  $place_manager    [职位是否管理]
	 * @param  integer $place_group      [职位分组]
	 * @return boolean                   [description]
	 */
	function is_place($place_department,$place_name,$place_manager,$place_group=0){
		if(!$this->has_auth("select")) return false;

		return $this->where(array("place_department"=>$place_department,"place_name"=>$place_name,"place_group"=>$place_group,"place_manager"=>$place_manager))->find();
	}


	/**
	 * [set_place 修改职位]
	 * @param [type]  $place_id         [职位id]
	 * @param [type]  $place_department [职位隶属部门]
	 * @param [type]  $place_name       [职位名字]
	 * @param [type]  $place_manager    [职位是否管理]
	 * @param integer $place_group      [职位分组]
	 */
	function set_place($place_id,$place_department,$place_name,$place_manager,$place_group=0){
		if(!$this->has_auth("update")) return false;

		$isResult=$this->is_place($place_department,$place_name,$place_manager,$place_group);
		if($isResult==""){
			return $this->where(array("place_id"=>$place_id))->save(array("place_name"=>$place_name,"place_department"=>$place_department,"place_group"=>$place_group,"place_manager"=>$place_manager));

		}else{
			return "该职位在部门/分组中已存在";
		}
	}	

	/**
	 * [add_place 新增职位]
	 * @param [type]  $place_department [职位隶属部门]
	 * @param [type]  $place_name       [职位名字]
	 * @param [type]  $place_manager    [职位是否管理]
	 * @param integer $place_group      [职位分组]
	 */
	function add_place($place_department,$place_name,$place_manager,$place_group=0){
		if(!$this->has_auth("insert")) return false;

		if($this->is_place($place_department,$place_name,$place_manager,$place_group)==""){
			return $this->add(array("place_name"=>$place_name,"place_department"=>$place_department,"place_group"=>$place_group,"place_manager"=>$place_manager));
		}else{
			return "该职位在部门/分组中已存在";
		}
	}

	/**
	 * 删除职位
	 * @param  [type] $place_id [指定的职位id]
	 * @return [type]           [description]
	 */
	function del_place($place_id){
		if(!$this->has_auth("delete")) return false;

		return $this->where(array("place_id"=>$place_id))->delete();
	}
	/**
	 * [get_leader 获取属于管理层的职位]
	 * @return [type] [description]
	 */
	function get_leader(){
		if(!$this->has_auth("select")) return false;

		return $this->join("left join oa_department d on d.department_id=place_department")->where("d.department_leader>0 and place_manager>0")->select();
	}

	/**
	 * [add_extent 增加extent]
	 * @param [type] $place_id      [指定的place id]
	 * @param [type] $eplace_extent [需要新增的extent]
	 */
	function add_extent($place_id,$eplace_extent){
		if(!$this->has_auth("update")) return false;

		$map['place_extent'] = array("exp","concat ('{$eplace_extent}',place_extent)");
		$this->where(array("place_id"=>$place_id))->save($map);
	}

	/**
	 * [reduce_extent 减小extent]
	 * @param  [type] $place_id     [指定的place id]
	 * @param  [type] $place_extent [减少后的extent]
	 * @return [type]               [none]
	 */
	function reduce_extent($place_id,$place_extent){
		if(!$this->has_auth("update")) return false;

		$this->where(array("place_id"=>$place_id))->save(array("place_extent"=>$place_extent));
	}

	/**
	 * [set_place_role 修改默认角色]
	 * @param [type] $place_id   [指定place id]
	 * @param [type] $place_role [修改的role id]
	 */
	function set_place_role($place_id,$place_role){
		if(!$this->has_auth("update")) return false;

		$this->where(array("place_id"=>$place_id))->save(array("place_role"=>$place_role));
	}
}
