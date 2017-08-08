<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-07 18:39:02
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-08 11:18:59
 */
namespace Common\Model;
use Common\Model\AmongModel;
class Attend_checkinModel extends AmongModel{
	protected $trueTableName = 'oa_attend_checkin'; 
	protected $fields = array('acheckin_id', 'acheckin_code','acheckin_checkinway','acheckin_type','acheckin_timetype','acheckin_checkintime','acheckin_location','acheckin_longlat','acheckin_picture');

	function search_checkin($acheckin_code,$acheckin_id=null,$condition=array()){
		
		if(!$this->has_auth("select")){
			return null;
		}
		if($acheckin_id==null){
			// $acheckin=$this->where(array("acheckin_code"=>$acheckin_code));
		}else{
			// $acheckin=$this->where(array("acheckin_id"=>$acheckin_id));
		}
		if(!empty($condition)){
			// print_r($condition);
			return $this->where($condition)->select();
		}

		// return $acheckin->select();
	}
}
