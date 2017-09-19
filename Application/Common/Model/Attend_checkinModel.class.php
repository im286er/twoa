<?php
/**
 * @Author: vition
 * @Email:369709991@qq.com
 * @Date:   2017-08-07 18:39:02
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-10 12:07:15
 */
namespace Common\Model;
use Common\Model\AmongModel;
class Attend_checkinModel extends AmongModel{
	protected $trueTableName = 'oa_attend_checkin'; 
	protected $fields = array('acheckin_id', 'acheckin_code','acheckin_checkinway','acheckin_type','acheckin_timetype','acheckin_addtime','acheckin_checkintime','acheckin_location','acheckin_longlat','acheckin_picture',"acheckin_state","acheckin_tempstorage","acheckin_applyid");

	function search_checkin($acheckin_code,$condition=array(),$start=0,$limit=0){
		if(!$this->has_auth("select")) return false;
		$this->table("oa_attend_checkin ch")->field(array_merge($this->fields,array("ct.config_value acheckin_types","ctt.config_value acheckin_timetypes")))->join("left join oa_config ct on ct.config_class='acheckin_type' AND ct.config_key=ch.acheckin_type")->join("left join oa_config ctt on ctt.config_class='acheckin_timetype' AND ctt.config_key=ch.acheckin_timetype")->order("acheckin_checkintime desc,acheckin_id desc");
		if($limit>0){
			$this->limit($start.','.$limit);
		}
		if($acheckin_code>0){
			$this->where(array("acheckin_code"=>$acheckin_code));
		}
		if(!empty($condition)){
			// print_r($condition);
			return $this->where($condition)->select();
		}
		return $this->select();

		// return $acheckin->select();
	}

	/**
	 * [seekCheckin 寻找打卡记录]
	 * @param  [type] $user_code [人员code]
	 * @param  [type] $type      [类型，]
	 * @param  [type] $date      [日期，格式如：2017-08-09]
	 * @return [type]            [description]
	 */
	function seekCheckin($user_code,$type=null,$date=null,$state=null,$applyid=null){
		if(!$this->has_auth("select")) return false;
		$where=array();
		$where["acheckin_code"]=array("eq",$user_code);

		if($type!=null){
			$where["acheckin_type"]=array("eq",$type);
		}
		if($date!=null){
			$where["date_format(acheckin_checkintime,'%Y-%m-%d')"]=array("eq",$date);
		}
		if($state!=null){
			$where["acheckin_state"]=array("eq",$state);
		}
		if($applyid!=null){
			$where["acheckin_applyid"]=array("eq",$applyid);
		}
		
		return $this->where($where)->order("acheckin_checkintime")->select();
		
	}

	function applySeekCheckin($apply_id){
		if(!$this->has_auth("select")) return false;
		return $this->where(array("acheckin_applyid"=>$apply_id))->select();
	}

	/**
	 * [hasCheckin 判断指定打卡记录是否存在]
	 * @param  [type]  $acheckin_code     [人员code]
	 * @param  [type]  $acheckin_type     [打卡类型，1正常上班，2外勤，3加班]
	 * @param  [type]  $acheckin_timetype [打卡时间类型，1开始，2结束]
	 * @param  [type]  $date              [打卡发生时间，格式：2017-08-10]
	 * @return boolean                    [description]
	 */
	function hasCheckin($acheckin_code,$acheckin_type,$acheckin_timetype,$date){
		if(!$this->has_auth("select")) return false;
		return $this->where(array("acheckin_code"=>$acheckin_code,"acheckin_type"=>$acheckin_type,"acheckin_timetype"=>$acheckin_timetype))->where("date_format(acheckin_checkintime,'%Y-%m-%d')='{$date}'")->find();
	}

	/**
	 * [checkin 打卡方法]
	 * @param  [type] $dataArray [打卡数据]
	 * @return [type]            [description]
	 */
	function checkin($dataArray){
		if(!$this->has_auth("insert")) return false;
		if($this->hasCheckin($dataArray["acheckin_code"],$dataArray["acheckin_type"],$dataArray["acheckin_timetype"],date("Y-m-d",strtotime($dataArray["acheckin_checkintime"])))["acheckin_id"]==null){
			return $this->add($dataArray);
		}
		return false;
		
	}

	/**
	 * setCheckin function
	 *
	 * @param [type] $acheckin_id
	 * @param [type] $dataArray 
	 * @return void
	 */
	function setCheckin($acheckin_id,$dataArray){
		if(!$this->has_auth("update")) return false;
		return $this->where(array("acheckin_id"=>$acheckin_id))->save($dataArray);
	}
	/**
	 * isOverTime function 判断加班申请
	 *
	 * @param [type] $user_code
	 * @param [type] $date
	 * @return 不存在返回false，存在放回对应的申请id 已审核
	 */
	function isOverTime($user_code,$date){
		if(!$this->has_auth("select")) return false;
		$checkinResult=$this->table("(select acheckin_id,acheckin_timetype,acheckin_applyid from oa_attend_checkin where acheckin_type=3 and date_format(acheckin_checkintime,'%Y-%m-%d')='{$date}') a")->union("select * from (select acheckin_id,acheckin_timetype,acheckin_applyid from oa_attend_checkin where acheckin_type=3 and date_format(acheckin_checkintime,'%Y-%m-%d')<'{$date}' order by acheckin_id desc limit 0,1) b")->union(" select * from (select acheckin_id,acheckin_timetype,acheckin_applyid from oa_attend_checkin where acheckin_type=3 and date_format(acheckin_checkintime,'%Y-%m-%d')>'{$date}'  order by acheckin_id asc limit 0,1) c order by acheckin_id")->select();


		if($checkinResult[0]["acheckin_timetype"]==2){
			array_shift($checkinResult);
		}
		if(count($checkinResult)>=2){
			$applyid=array();
			
			foreach ($checkinResult as $key => $value) {
				if(($key%2)==1){
					if($first==1 && $value["acheckin_timetype"]==2){
						$applyResult=$this->table("oa_attend_apply")->field("aapply_state")->where(array("aapply_id"=>$value["acheckin_applyid"]))->find();
						if($applyResult["aapply_state"]>0){
							array_push($applyid,$value["acheckin_applyid"]);
						}
					}
				}else{
					$first=$value["acheckin_timetype"];
				}
			}
			if(empty($applyid)!==null){
				return $applyid;
			}
		}
		return false;
	}
}