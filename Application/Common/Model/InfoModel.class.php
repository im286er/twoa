<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-11 16:04:09
 * @Last Modified by:   vition
 * @Last Modified time: 2017-07-13 12:07:27
 */
namespace Common\Model;
use Think\Model;
/**
 * 整合所有基本的信息管理
 */
class InfoModel extends Model{
	protected $trueTableName = 'oa_config';
	// protected $fields = array('config_id');	
	/**
	 * [company 公司管理模型]
	 * @return [object] [返回CompanyModel对象]
	 */
	function company(){
		$company=D("Company");
		return $company;
	}

	/**
	 * [department 部门管理模型]
	 * @return [object] [返回DepartmentModel]
	 */
	function department(){
		return D("Department");
	}

	/**
	 * [group 分组管理模型]
	 * @return [object] [返回GroupModel]
	 */
	function group(){
		return D("Group");
	}

	/**
	 * [place 职位管理模型]
	 * @return [object] [返回PlaceModel]
	 */
	function place(){
		return D("Place");
	}

	/**
	 * [role 角色管理模型]
	 * @return [object] [返回RoleModel]
	 */
	function role(){
		return D("Role");
	}

	/**
	 * [user 用户管理模型]
	 * @return [type] [返回UserModel]
	 */
	function user(){
		return D("User");
	}


}