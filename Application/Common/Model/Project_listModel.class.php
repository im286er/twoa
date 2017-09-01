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
class Project_listModel extends AmongModel{
    protected $trueTableName = 'oa_project_list'; 
    protected $fields = array('project_id', 'project_company','project_name','project_type','project_startdate','project_enddate','project_state');
    
    /**
     * 查询项目 function
     *
     * @param [type] $start
     * @param [type] $limit
     * @param array $dataArray
     * @return void
     */
    function search_all($start,$limit,$dataArray=array()){
        if(!$this->has_auth("select")) return false;
        $tableObject=$this->limit($start.','.$limit);
		if (empty($dataArray)){
			return $tableObject->select();
        }
		return $tableObject->where($dataArray)->select();
		// 	// echo $this->getLastSql();
		// }
    }
    /**
     * 通过id查找项目信息 function
     *
     * @param [type] $project_id
     * @return void
     */
    function find_project($project_id){
        if(!$this->has_auth("select")) return false;
        return $this->where(array("project_id"=>$project_id))->find();
    }

    /**
     * 修改制定id的项目信息 function
     *
     * @param [type] $project_id
     * @param [type] $project_data
     * @return void
     */
    function set_project($project_id,$project_data){
        if(!$this->has_auth("update")) return false;
        return $this->where(array("project_id"=>$project_id))->save($project_data);
    }

    /**
     * 添加项目信息 function
     *
     * @param [type] $project_data
     * @return void
     */
    function add_project($project_data){
        dump($this->has_auth("insert"));
        if(!$this->has_auth("insert")) return false;
        echo "a";
        return $this->add($project_data);
    }
}