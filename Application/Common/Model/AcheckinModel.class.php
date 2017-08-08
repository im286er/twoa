<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-07 18:39:02
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-07 18:59:08
 */
namespace Common\Model;
use Common\Model\AmongModel;
class AcheckinModel extends AmongModel{
	protected $trueTableName = 'oa_attend_checkin'; 
	protected $fields = array('acheckin_id', 'acheckin_code','acheckin_checkinway','acheckin_type','acheckin_timetype','acheckin_checkintime','acheckin_location','acheckin_longlat','acheckin_picture');

	function find_checkin($acheckin_code,$acheckin_code=null,$condition=array()){
		if(!$this->has_auth("select")){
			return null;
		}
		if($acheckin_code==0){
			return $this->where(array("acheckin_code"=>$acheckin_code))->find();
		}
		if(!empty($condition)){

		}
	}
}
//select * from oa_attend_checkin where date_format(acheckin_checkintime,'%Y-%m-%d')='2017-08-07';
//mysql> insert into oa_attend_checkin value(NUll,"1000000107",'1','1','1','2017-08-06 09:32:55','13422154','12333','1111');
// mysql> insert into oa_attend_checkin value(NUll,"1000000107",'1','1','1','2017-08-05 09:36:55','13422154','12333','1111');
// mysql> insert into oa_attend_checkin value(NUll,"1000000107",'1','1','1','2017-08-07 19:36:55','13422154','12333','1111');