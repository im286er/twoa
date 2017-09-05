<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-11 16:04:09
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-09 15:57:43
 */
namespace Common\Model;
use Think\Model;
/**
 * 整合所有基本的信息管理
 */
class InfoModel extends Model{
	protected $trueTableName = 'oa_config';
	public $company;
	public $department;
	public $group;
	public $place;
	public $role;
	public $user;

	public $province;
	public $city;
	public $district;
	// protected $fields = array('config_id');	
	/**
	 * [company 公司管理模型]
	 * @return [object] [返回CompanyModel对象]
	 */
	function company(){
		if(!is_object($this->company)){
			$this->company=D("Company");
		}
		return $this->company;	
	}

	/**
	 * [department 部门管理模型]
	 * @return [object] [返回DepartmentModel]
	 */
	function department(){
		if(!is_object($this->department)){
			$this->department=D("Department");
		}
		return $this->department;
	}

	/**
	 * [group 分组管理模型]
	 * @return [object] [返回GroupModel]
	 */
	function group(){
		if(!is_object($this->group)){
			$this->group=D("Group");
		}
		return $this->group;
	}

	/**
	 * [place 职位管理模型]
	 * @return [object] [返回PlaceModel]
	 */
	function place(){
		if(!is_object($this->place)){
			$this->place=D("Place");
		}
		return $this->place;
	}

	/**
	 * [role 角色管理模型]
	 * @return [object] [返回RoleModel]
	 */
	function role(){
		if(!is_object($this->role)){
			$this->role=D("Role");
		}
		return $this->role;
	}

	/**
	 * [user 用户管理模型]
	 * @return [type] [返回UserModel]
	 */
	function user(){
		if(!is_object($this->user)){
			$this->user=D("User");
		}
		return $this->user;
	}

	/**
	 * getProvince function
	 * 默认情况下获取所有省份
	 * @param integer $id 大于0则获取省份的id
	 * @param boolean $multi true返回多，
	 * @return void
	 */
	function getProvince($id=0,$multi=true){
		if(!is_object($this->province)){
			$this->province=M("oa_region_province");
		}
		$mysql=$this->province;
		if($id>0){
			$mysql=$this->province->where(array("province_id"=>$id));
		}
		if($multi){
			return $mysql->select();
		}
		return $mysql->find();
	}

	/**
	 * getCity function 获取城市
	 *
	 * @param [type] $id 
	 * @param boolean $multi multi真的时候 id代表省份id，false的时候代表只获取城市的id
	 * @return void
	 */
	function getCity($id,$multi=true){
		if(!is_object($this->city)){
			$this->city=M("oa_region_city");
		}
		if($multi==true){
			return $this->city->where(array("city_proid"=>$id))->select();
		}else{
			return $this->city->where(array("city_id"=>$id))->find();
		}
	}
	
	/**
	 * getDistrict function 获取区
	 *
	 * @param [type] $id 
	 * @param boolean $multi multi真的时候 id代表城市id，false的时候代表只获取区的id
	 * @return void
	 */
	function getDistrict($id,$multi=true){
		if(!is_object($this->district)){
			$this->district=M("oa_region_district");
		}
		if($multi==true){
			return $this->district->where(array("district_cityid"=>$id))->select();
		}else{
			return $this->district->where(array("district_id"=>$id))->find();
		}
	}

}