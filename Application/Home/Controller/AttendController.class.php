<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-03 16:43:53
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-07 18:11:20
 */

/*{"control":"Attend","name":"考勤管理","icon":"fa fa-calendar","menus":[{"name":"考勤配置","icon":"fa fa-gear","menus":"config"},{"name":"考勤申请","icon":"fa fa-list-alt","menus":"userlist"},{"name":"申请管理","icon":"fa fa-pencil-square","menus":"archives"},{"name":"打卡","icon":"fa fa-square","menus":"arch"}]}*/
namespace Home\Controller;
use Common\Controller\AmongController;
class AttendController extends AmongController {

	/**
	 * [checkin 打卡页面]
	 * @return [type] [description]
	 */
	public function checkin(){
		$this->assign("usercode",session("oa_user_code"));
		$this->assign("SignPackage",$this->Wxqy->jssdk()->GetSignPackage());
		$this->display("checkin");
	}

	/**
	 * [getLocation 根据经纬度获取地址]
	 * @param  [type] $latitude  [latitude]
	 * @param  [type] $longitude [longitude]
	 * @return [type]            [description]
	 */
	public function getPosition($latitude=0,$longitude=0){

		if($latitude==0 || $longitude==0){
			$latitude=I("latitude");
			$longitude=I("longitude");
		}
		$xmlstr=file_get_contents("http://apis.map.qq.com/ws/geocoder/v1/?location={$latitude},{$longitude}&key=V6EBZ-4EN35-7OHIH-QDJTA-KNYBO-IHFFN");
		$objPosition=json_decode($xmlstr);
		$Position=$objPosition->result->address;
		echo $Position;
	}
	

}