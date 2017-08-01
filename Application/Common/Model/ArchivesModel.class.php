<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-07-28 15:26:56
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-01 11:12:07
 */
namespace Common\Model;
use Common\Model\AmongModel;
class ArchivesModel extends AmongModel{
	protected $trueTableName = 'oa_archives'; 
	protected $fields = array('archives_id', 'archives_usercode','archives_degree','archives_idn','archives_idexp','archives_bank','archives_enformp','archives_cvp','archives_degreep','archives_idp','archives_horp','archives_bankp','archives_physicalp','archives_cultivatep','archives_receivep','archives_agreement','archives_secretp','archives_quitp');

	/**
	 * [search_all 查询档案]
	 * @param  [type] $start     [开始]
	 * @param  [type] $limit     [限制]
	 * @param  array  $dataArray [条件数组]
	 * @return [type]            [description]
	 */
	function search_all($start,$limit,$dataArray=array()){
		if(!$this->has_auth("select")){
			return false;
		}
		array_push($this->fields, "u.user_name archives_name","CASE u.user_state WHEN 0 THEN '未激活' WHEN 1 THEN '在职' ELSE '离职' END archives_status", "u.user_state archives_statu");
		$tableObject=$this->field($this->fields)->join("left join oa_user u on u.user_code=oa_archives.archives_usercode")->limit($start.','.$limit);
		if (empty($dataArray)){
			return $tableObject->select();
		}else{
			return $tableObject->where($dataArray)->select();
		}
	}

	/**
	 * [find_archive 查找指定]
	 * @param  [type]  $archive_id [档案id]
	 * @param  boolean $archives_usercode    [档案人员code]
	 * @return [type]           [description]
	 */
	function find_archive($archive_id,$archives_usercode=0){
		if(!$this->has_auth("select")){
			return false;
		}
		array_push($this->fields, "u.user_name archives_name");
		$tableObject=$this->field($this->fields)->join("left join oa_user u on u.user_code=oa_archives.archives_usercode");
		if($archive_code==0){
			return $tableObject->where(array("archives_id"=>$archive_id))->find();
		}else{
			return $tableObject->where(array("archives_usercode"=>$archives_usercode))->find();
		}
	}

	/**
	 * [add_archive 新建档案]
	 * @param [type] $dataArray [description]
	 */
	function add_archive($dataArray){
		if(!$this->has_auth("insert")){
			return false;
		}
		$hasArch=$this->find_archive("",$dataArray["archives_usercode"]);
		if(empty($hasArch)){
			return $this->add($dataArray);
		}
	}

	function setArchive($archives_usercode,$dataArray){
		if(!$this->has_auth("update")){
			return false;
		}
		return $this->where(array("archives_usercode"=>$archives_usercode))->save($dataArray);
	}
}