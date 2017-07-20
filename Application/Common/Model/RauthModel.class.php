<?php
/**
 * 角色管理表
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-20 16:46:41
 * @Last Modified by:   vition
 * @Last Modified time: 2017-07-20 18:06:24
 */
namespace Common\Model;
use Think\Model;
class RauthModel extends Model{
	protected $trueTableName = 'oa_rauth'; 
	protected $fields = array('rauth_id', 'rauth_role','rauth_auth');	

	/**
	 * [search_auth 批量查询角色权限]
	 * @param  string $start [开始]
	 * @param  string $limit [条数]
	 * @return [type]        [description]
	 */
	function search_auth($start="",$limit=""){
		if($start=="" && $limit==""){
			return $this->select();
		}else if($start!="" && $limit==""){
			return $this->limit("{$start}")->select();
		}else{
			return $this->limit("{$start},{$limit}")->select();
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
    	if($user_role>0){
    		$rauthData=json_decode($this->field("rauth_auth")->where("rauth_role='{$user_role}'")->find()["rauth_auth"],true);
    	}else{
    		$rauthData=json_decode($this->table("oa_rauth a")->field("rauth_auth")->join("oa_user u")->where("u.user_username='".session("oa_user_username")."' AND u.user_role=a.rauth_role")->find()["rauth_auth"],true);
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

	/**
	 * [is_auth 判断角色权限是否存在]
	 * @param  [type]  $rauth_role [description]
	 * @return boolean             [description]
	 */
	function is_auth($rauth_role){
		return $this->where(array("rauth_role"=>$rauth_role))->find();
	}

	/**
	 * [add_auth 新增角色权限]
	 * @param [type] $rauth_role [角色id]
	 * @param [type] $rauth_auth [权限JSON]
	 */
	function add_auth($rauth_role,$rauth_auth){
		if($this->is_auth($rauth_role)==""){
			return $this->add(array("rauth_role"=>$rauth_role,"rauth_auth"=>$rauth_auth));
		}else{
			return $this->set_auth($rauth_role,$rauth_auth);
		}
	}

	/**
	 * [set_auth 更新指定角色权限]
	 * @param [type] $rauth_role [指定角色id]
	 * @param [type] $rauth_auth [权限JSON]
	 */
	function set_auth($rauth_role,$rauth_auth){
		return $this->where(array('rauth_role' =>$rauth_role ))->save(array("rauth_auth"=>$rauth_auth));
	}

	/**
	 * [del_auth 删除指定角色权限]
	 * @param  [type] $rauth_role [指定角色id]
	 * @return [type]             [description]
	 */
	function del_auth($rauth_role){
		return $this->where(array("rauth_role"=>$rauth_role))->delete();
	}
}
