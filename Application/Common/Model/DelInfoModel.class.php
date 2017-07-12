<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-07 10:16:00
 * @Last Modified by:   vition
 * @Last Modified time: 2017-07-07 17:05:14
 */
namespace Common\Model;
use Think\Model;
class InfoModel extends Model{
		protected $trueTableName = 'oa_config'; 
		protected $fields = array('group_id', 'group_name', 'subgroup_id','subgroup_name','subgroup_group','place_id','place_name','place_group','place_subgroup','place_manager','role_id','role_name','role_upper');
		/*配置方法*/

		/*公司方法*/
		function search_company($start=0,$limit=0){
			if($start==0 && $limit==0){
				return $this->table("oa_config")->where("config_class='company'")->limit($start.",".$limit)->select();
			}else{
				return $this->table("oa_config")->where("config_class='company'")->select();
			}
			
		}
		/*部门方法*/
		function search_group($start=0,$limit=0){
			if($start==0 && $limit==0){
				return $this->table("oa_group")->limit($start.",".$limit)->select();
			}else{
				return $this->table("oa_group")->select();
			}
		}
		/*分组方法*/
		function search_subgroup($group_id,$start=0,$limit=0){
			if($start==0 && $limit==0){
				return $this->table("oa_subgroup")->where("subgroup_group=".$group_id)->limit($start.",".$limit)->select();
			}else{
				return $this->table("oa_subgroup")->where("subgroup_group=".$group_id)->select();
			}
		}
		
		/*职位方法*/
		function search_place($place_group,$place_subgroup){
			return $this->table("oa_place")->where(array("place_group"=>$place_group,"place_subgroup"=>$place_subgroup))->select();
		}

		/*角色方法*/
		function search_role($role_upper=0){
			return $this->table("oa_role")->where(array("role_upper"=>$role_upper))->select();
		}
		function find_role($role_id){
			return $this->table("oa_role")->where(array("role_id"=>$role_id))->find();
		}
}