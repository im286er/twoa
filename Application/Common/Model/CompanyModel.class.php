<?php
/**
 * 公司管理模型
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-06-29 18:48:50
 * @Last Modified by:   vition
 * @Last Modified time: 2017-07-25 11:56:10
 */
namespace Common\Model;
use Common\Model\AmongModel;
class CompanyModel extends AmongModel {
	protected $trueTableName = 'oa_company'; 
	protected $fields = array('company_id', 'company_name');

	/**
	*初始化值
	*/
	function _initialize(){
		// $this->table("oa_company");	
	}
	/**
	 * [查询公司名列表]
	 * @param  string $start [起始值]
	 * @param  string $limit [限制条数]
	 * @return [type]        [上述两个参数都为空的时候，默认查询所有；$start存在而$limit为空的时候，查询条数；两个参数都存在则查询指定起始和限制条数]
	 */
	function search_company($start="",$limit=""){
		if(!$this->has_auth("select")) return false;
		if($start=="" && $limit==""){
			return $companyData=$this->select();
		}else if($start!="" && $limit==""){
			return $companyData=$this->limit("{$start}")->select();
		}else{
			return $companyData=$this->limit("{$start},{$limit}")->select();
		}
		return $this->getLastSql();	

		
	}
	/**
	 * [查找指定的公司名]
	 * @param  [type] $company_id   [公司id]
	 * @param  string $company_name [公司名，如果这个参数存在，那么第一个参数失效，意思为通过公司名查id]
	 * @return [type]               [只返回一条记录]
	 */
	function find_company($company_id,$company_name=""){
		if(!$this->has_auth("select")) return false;

		if($company_name!=""){
			$condition['company_name']=$company_name;
			return $this->where($condition)->find();
		}else{
			$condition['company_id']=$company_id;
			return $this->where($condition)->find();
		}

	}
	/**
	 * 修改公司名
	 * @param [type] $company_id   [指定的公司id]
	 * @param [type] $company_name [修改后的公司名]
	 */
	function set_company($company_id,$company_name){
		if(!$this->has_auth("update")) return false;

		if($this->find_company(0,$company_name)==""){
			return $this->where(array("company_id"=>$company_id))->save(array("company_name"=>$company_name));
		}else{
			return "公司名已存在";
		}
	}

	/**
	 * 新增公司名
	 * @param [type] $company_name 新增的名字
	 */
	function add_company($company_name){
		if(!$this->has_auth("insert")) return false;

		if($this->find_company(0,$company_name)==""){
			return $this->add(array("company_name"=>$company_name));
		}else{
			return "公司名已存在";
		}
	}

	/**
	 * 删除指定的公司
	 * @param  [type] $company_id [指定的公司名]
	 * @return [type]             
	 */
	function del_company($company_id){
		if(!$this->has_auth("delete")) return false;

		return $this->where(array("company_id"=>$company_id))->delete();
	}
}