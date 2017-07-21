<?php
/**
 * 角色管理表
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-20 16:46:41
 * @Last Modified by:   vition
 * @Last Modified time: 2017-07-21 18:06:50
 */
namespace Common\Model;
use Common\Model\AmongModel;
class RauthModel extends AmongModel{
	protected $trueTableName = 'oa_rauth'; 
	protected $fields = array('rauth_id', 'rauth_role','rauth_auth','rauth_table');	

	/**
	 * [search_auth 批量查询角色权限]
	 * @param  string $start [开始]
	 * @param  string $limit [条数]
	 * @return [type]        [description]
	 */
	function search_auth($start="",$limit=""){
		if($this->has_auth("select")){
			if($start=="" && $limit==""){
				return $this->select();
			}else if($start!="" && $limit==""){
				return $this->limit("{$start}")->select();
			}else{
				return $this->limit("{$start},{$limit}")->select();
			}
		}
	}

	/**
	 * [find_auth 查找角色权限]
	 * @param  string  $elimArray [指定排除的功能，默认为空，]
	 * @param  boolean $module    [是否只获取当前模块的权限]
	 * @param  integer $user_role [指定的角色，默认为0获取当前用户权限]
	 * @return [type]             [description]
	 */
	function find_auth($elimArray="",$module=true,$user_role=0){
		if($this->has_auth("select")){
			if($user_role>0){
	    		$rauthData=json_decode($this->field("rauth_auth")->where("rauth_role='{$user_role}'")->find()["rauth_auth"],true);
	    	}else{
	    		if(null !==session("oa_user_username")){
	    			$rauthData=json_decode($this->table("oa_rauth a")->field("rauth_auth")->join("oa_user u")->where("u.user_username='".session("oa_user_username")."' AND u.user_role=a.rauth_role")->find()["rauth_auth"],true);
	    		}else{
	    			return false;
	    		}
	    		
	    	}

	    	if($module){
	    		$rAtuhArray=$rauthData[MODULE_NAME];
	    		if(!empty($elimArray)){
		    		foreach ($elimArray as $elim) {
		    			if(isset($rAtuhArray[$elim])){
		    				unset($rAtuhArray[$elim]);
		    			}
		    		}
		    	}
	    	}else{
				$rAtuhArray=$rauthData;
	    	}
	    	return $rAtuhArray;
		}
	}

	/**
	 * [is_auth 判断角色权限是否存在]
	 * @param  [type]  $rauth_role [description]
	 * @return boolean             [description]
	 */
	function is_auth($rauth_role){
		if($this->has_auth("select")){
			return $this->where(array("rauth_role"=>$rauth_role))->find();
		}
		
	}

	/**
	 * [add_auth 新增角色权限]
	 * @param [type] $rauth_role [角色id]
	 * @param [type] $rauth_auth [权限JSON]
	 */
	function add_auth($rauth_role,$rauth_auth){

		if($this->is_auth($rauth_role)==""){
			if($this->has_auth("insert")){
				return $this->add(array("rauth_role"=>$rauth_role,"rauth_auth"=>$rauth_auth));
			}
		}else{
			if($this->has_auth("update")){
				return $this->set_auth($rauth_role,$rauth_auth);
			}
			
		}
	}

	/**
	 * [set_auth 更新指定角色权限]
	 * @param [type] $rauth_role [指定角色id]
	 * @param [type] $rauth_auth [权限JSON]
	 */
	function set_auth($rauth_role,$rauth_auth){
		if($this->has_auth("update")){
			return $this->where(array('rauth_role' =>$rauth_role ))->save(array("rauth_auth"=>$rauth_auth));
		}
		
	}
	/**
	 * [set_table 更新数据表权限]
	 * @param [type] $rauth_role  [指定角色]
	 * @param [type] $rauth_table [权限json]
	 */
	function set_table($rauth_role,$rauth_table){
		if($this->has_auth("update")){
			return $this->where(array('rauth_role' =>$rauth_role ))->save(array("rauth_table"=>$rauth_table));
		}
		
	}

	/**
	 * [del_auth 删除指定角色权限]
	 * @param  [type] $rauth_role [指定角色id]
	 * @return [type]             [description]
	 */
	function del_auth($rauth_role){
		if($this->has_auth("delete")){
			return $this->where(array("rauth_role"=>$rauth_role))->delete();
		}
		
	}
	/**
	 * [find_table 查找指定数据表权限]
	 * @param  [type] $user_role [指定角色id]
	 * @return [type]            [description]
	 */
	function find_table($user_role=0){
		// echo $this->has_auth("select");	
		if($this->has_auth("select")){
			if($user_role>0){
				return json_decode($this->field("rauth_table")->where("rauth_role='{$user_role}'")->find()["rauth_table"],true);
			}else{
				if(null !==session("oa_user_username")){
	    			return json_decode($this->table("oa_rauth a")->field("rauth_table")->join("oa_user u")->where("u.user_username='".session("oa_user_username")."' AND u.user_role=a.rauth_role")->find()["rauth_table"],true);
	    		}else{
	    			return false;
	    		}
			}
		}
		
		
	}

	/**
	 * [add_table 添加修改指定角色数据表权限]
	 * @param [type] $rauth_role  [指定角色id]
	 * @param [type] $rauth_table [权限json]
	 */
	function add_table($rauth_role,$rauth_table){
		echo $this->has_auth("insert");
		if($this->is_auth($rauth_role)==""){

			if($this->has_auth("insert")){
				return $this->add(array("rauth_role"=>$rauth_role,"rauth_table"=>$rauth_table));
			}
			
		}else{
			if($this->has_auth("update")){
				return $this->set_table($rauth_role,$rauth_table);
			}
		}
	}
}
