<?php
namespace Common\Model;
use Think\Model;
class CompanyModel extends Model {
	protected $trueTableName = 'oa_config'; 
	protected $fields = array('config_id', 'config_class','config_key','config_value','_pk'=>'config_id','_autoinc' => true);
	protected $class=array();

	/**
	*初始化值
	*/
	function _initialize(){
		$this->class[0]['config_class']='company';
		$this->where($this->class);
	}
	/**
	* 查询oa_config表中company的数据，默认查所有
	* @start limit的起始位置
	* @limit limit的条数
	* 当只有一个参数的时候默认查询条数
	*/
	function select_company($start="",$limit=""){
		
		// $this->class["config_class"]="company";
		if($start=="" && $limit==""){
			return $companyData=$this->select();
			 // return $this->getLastSql();
		}else if($start!="" && $limit==""){
			return $companyData=$this->limit("{$start}")->select();
		}else{
			return $companyData=$this->limit("{$start},{$limit}")->select();
		}
		
	}
	/**
	* 查询指定的company
	* @company_key 要查询的config_key
	* @company_name 默认值为空，如果当此参数不为空的时候company_key 参数失效，即通过company_name查company_key
	*/
	function find_company($company_key,$company_name=""){
		if($company_name!=""){
			$condition['config_value']=$company_name;
			return $this->where($condition)->find();
			// return $this->getLastSql();
		}else{
			$condition['config_key']=$company_key;
			return $this->where($condition)->find();
			// return $this->getLastSql();
		}

	}
	/**
	* 更新指定company名
	* @company_key 要更新的company key
	* @company_name 新的company name
	*/
	function set_company($company_key,$company_name){
		if($this->find_company(0,$company_name)==""){
			return $this->where(array("config_key"=>$company_key))->save(array("config_value"=>$company_name));
		}else{
			return "公司名已存在";
		}
		// 
	}

	/**
	* 新增company
	* @company_name 新增的company name 
	*/
	function add_company($company_name){

		if($this->find_company(0,$company_name)==""){
			// return $this->new_key();
			return $this->add(array("config_class"=>"company","config_key"=>$this->new_key(),"config_value"=>$company_name));
		}else{
			return "公司名已存在";
		}
	}
	/**
	* 生成新的key
	*/
	protected function new_key(){
		return $this->field("config_key")->order("config_key DESC")->find()["config_key"]+1;
	}
	/**
	* 删除company
	* @company_key 要删除的company_key
	*/
	function del_company($company_key){
		return $this->where(array("config_key"=>$company_key))->delete();
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