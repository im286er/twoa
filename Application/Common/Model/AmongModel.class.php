<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-21 16:00:59
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-01 18:36:51
 */
namespace Common\Model;
use Think\Model;

class AmongModel extends Model{
	protected $selfAuth;

	/**
	 * [__construct 构造函数]
	 */
	function __construct(){
		parent::__construct();
		// $this->selfTableName=__CLASS__;
		$user=M("oa_user");
		$oa_rauth=M("oa_rauth");

		/**
		 * 获取每个用户的表权限
		 */
		if(null !==session("oa_user_code")){
			$selfUser=$user->where(array("user_code"=>session("oa_user_code")))->find();
			if($selfUser["user_role"]==0){
				$this->selfAuth =json_decode($oa_rauth->field("rauth_table")->where(array("rauth_role"=>$selfUser["user_roles"]))->find()["rauth_table"],true);
			}else{
				$this->selfAuth =json_decode($oa_rauth->field("rauth_table")->where(array("rauth_role"=>$selfUser["user_role"]))->find()["rauth_table"],true);
			}

		}else{
			$this->selfAuth=array();
		}
	}

	/**
	 * [has_auth 判断指定的数据表操作是否存在权限中]
	 * @param  [type]  $type [select,insert,update,delete]
	 * @return boolean       [description]
	 */
	function has_auth($type){
		if(!empty($this->selfAuth) && !empty($this->selfAuth[$this->trueTableName])){
			if(array_search($type, $this->selfAuth[$this->trueTableName])!==false){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
		
	}
}