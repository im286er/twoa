<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-05-18 15:57:50
 * @Last Modified by:   369709991@qq.com
 * @Last Modified time: 2017-09-02 21:30:58
 */
//control要记得首字母大写。
/*{"control":"Project","name":"项目管理","icon":"fa fa-linode","menus":[{"name":"项目列表","icon":"fa fa-linode","menus":"project"}]}*/
namespace Home\Controller;
use Common\Controller\AmongController;
class ProjectController extends AmongController {
    function __construct(){
        parent::__construct();
        $this->baseInfo=D("Info");
        $this->projectList=D("Project_list");
    }
    /**
	 * [gethtml 重写gethtml方法]
	 * @return [type] []
	 */
	public function gethtml(){
        switch (I("html")) {
            case "project":
                $company=$this->baseInfo->company()->search_company();
                $this->assign("companyArray",$company);
                break;
        }
        parent::gethtml();
    }
    /**
     * Undocumented function 获取新建项目的模板
     *
     * @return void
     */
    function getCreateProject(){
        if(IS_POST){
            $region=array();
            
            foreach($this->baseInfo->getProvince() as $province){
                $provinces=array();
                $provinces["name"]=$province["province_name"];

                $citys=array();
                foreach($this->baseInfo->getCity($province["province_id"]) as $city){
                    array_push($citys,$city);
                    // foreach ($this->baseInfo->getDistrict($city["city_id"]) as $district) {
                    //     array_push($citys,$district);
                    // }
                }
                $provinces["city"]=$citys;
                array_push($region,$provinces);
            }
            $managerArray=$this->baseInfo->user()->searchManager();
            $this->assign("managerArray",$managerArray);
            $this->assign("regionArray",$region);
            if(I("project_id")!=null){
                $add="false";
                $project=$this->projectList->find_project(I("project_id"));
                $this->assign("projectArray",$project);
            }else{
                $add="true";
            }
        }
        $company=$this->baseInfo->company()->search_company();
        $this->assign("companyArray",$company);
        $this->assign("nowtime",$this->getNowTime(1));
        $this->assign("add",$add);
        echo $this->fetch("projectinfo");
    }
    /**
     * 获取项目列表 function
     *
     * @return void
     */
    function getProjectList(){
		$count=$this->projectList->count();

		$condition=array();
		foreach ($_POST["condition"] as $key => $value) {
			if ($value["value"]!=""){
				if($value["name"]=="project_state"){
					$condition[$value["name"]]=array("EQ","{$value["value"]}");
				}else{
					$condition[$value["name"]]=array("LIKE","%{$value["value"]}%");
				}
				
			}
		}

        
		$count=$this->projectList->where($condition)->count();

		if($_POST["p"]>ceil($count/$_POST["limit"])){
			$_POST["p"]=1;
		}
		$Page=new \Think\Page($count,$_POST["limit"]);

		$pageShow=$Page->show();

		$projectDataArray=$this->projectList->search_all($Page->firstRow,$Page->listRows,$condition);
		$projectHtml="";
		foreach ($projectDataArray as $projectData) {
			// $state=array("","","");
			// $state[$userData["user_state"]]='disabled="disabled"';
			// $this->assign("state",$state);
			$this->assign("projectData",$projectData);
			$projectHtml.=$this->fetch("projectlist");
		 } 

		echo json_encode(array("projectlist"=>$projectHtml,"pagehtml"=>$pageShow)) ;
    }

    /**
     * 新增修改项目 function
     *
     * @return void
     */
    function conProject(){
        if(IS_POST){
           
            if(I("project_id")>0){
                /*修改*/
                echo $this->projectList->set_project(I("project_id"),I("data"));
            }else{
                /*新增*/

                echo $this->projectList->add_project(I("data"));
                
            }
        }
    }

    function searchProjectName(){

            $condition=array("project_name"=>array("LIKE","%".I("key")."%"),"project_state"=>array("eq","0"));
            $result=$this->projectList->field("project_id,project_name")->search_all(0,10,$condition);
            // $projectHtml="";
            // foreach ($result as $value) {
            //     $projectHtml.="<option value='".$value['project_id']."'>".$value["project_name"]."</option>";
            // }
            return $result;

    }
}