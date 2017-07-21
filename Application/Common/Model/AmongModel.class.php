<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-21 16:00:59
 * @Last Modified by:   vition
 * @Last Modified time: 2017-07-21 17:24:06
 */
namespace Common\Model;
use Think\Model;

class AmongModel extends Model{
	protected $selfAuth;
	function _initialize(){
		$this->selfTableName=__CLASS__;
		$oa_rauth=M("oa_rauth a");
		if(null !==session("oa_user_username")){
			$this->selfAuth =json_decode($oa_rauth->field("rauth_table")->join("oa_user u")->where("u.user_username='".session("oa_user_username")."' AND u.user_role=a.rauth_role")->find()["rauth_table"],true);
		}else{
			$this->selfAuth=array();
		}
	}

	function has_auth($type){
		if(array_search($type, $this->selfAuth[$this->trueTableName])!==false){
			return true;
		}else{
			return false;
		}
	}
}