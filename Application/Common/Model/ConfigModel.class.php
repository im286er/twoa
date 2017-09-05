<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-21 16:00:59
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-08 10:18:54
 */
namespace Common\Model;
use Think\Model;

class ConfigModel extends Model{
	protected $trueTableName="oa_config";
	protected $fields=array('config_id', 'config_class','config_key','config_value','config_upper','province_id','province_name','city_id','city_name','city_proid','district_id','district_name','district_cityid');

	function search_all($condition=array()){
		if(!$this->has_auth("select")) return false;
		if (empty($condition)){
			return $this->select();
		}else{
			return $this->where($condition)->select();
			// echo $this->getLastSql();
		}
	}
}