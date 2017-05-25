<?php
/*作为中间层的类*/
namespace Common\Controller;
use Think\Controller;
class AmangController extends Controller {
	//初始化，类似构造方法，判断是否登录
	public function _initialize(){
		$oa_login=session("oa_islogin");
		if(empty($oa_login)){
			//防止死循环跳转
			if (strtoupper(CONTROLLER_NAME)!="INDEX") {
				$url=U("index/index");
				echo "<script>top.location.href='$url'</script>";exit;
			}
		}else{
			//获取到用户的信息
			$user=M("oa_user u");
			$userData=$user->field("user_name,user_code,c.config_value user_company,g.config_value user_group,p.config_value user_place,r.role_name user_role,user_higher,user_phone,user_avatar,user_born,user_sex,user_lastlogin,user_entry,user_login,user_state")->where("user_name='".session("oa_user_name")."' AND  u.user_company=c.config_key AND u.user_group=g.config_key AND u.user_place=p.config_key AND u.user_role=r.role_id AND c.config_class='company' AND g.config_class='group' AND p.config_class='place'")->join("oa_config c,oa_config g,oa_config p,oa_role r")->find();
			$userData["user_age"]=get_age($userData["user_born"]);
			$userData["user_joinDay"]=get_day($userData["user_entry"]);
			$this->user=$userData;
			
			$this->assign("user",$userData);
		}
	}
	//获取html页面
	public function gethtml(){
		if(IS_POST){
			if($this->authority()){
				print_r($this->user);
				$this->display(I("html"));
			}else{
				$this->show("<h1>对不起！您木有权限</h1>");
			}
			
		}
	}
	//默认所有控制器index就是登录的页面，存在就跳转，不存在就登录
	public function index(){
    	$oa_login=session("oa_islogin");
		if(empty($oa_login)){
			$this->display("login");
		}else{
			$this->success("已登录",U("Menu/menu"));
		}
    	
    }
    //登录功能
    public function login(){
    	if(IS_POST){
    		$user=M("oa_user u");
    		$userData=$user->field("user_id,r.role_upper user_roleg")->where("u.user_name='".I("user_name")."' AND u.user_passwd='".sha1(I("user_passwd"))."' AND u.user_role=r.role_id AND u.user_state=1")->join("oa_role r")->find();
    		if($userData["user_id"]>0){
    			if(strtoupper(MODULE_NAME)=="ADMIN" AND $userData["user_roleg"]!=1){
    				$this->error("抱歉！非管理员无法登陆后台",U("index/index"),3);
    			}else{
    				session("oa_islogin","1");
	    			session("oa_user_name",I("user_name"));
	    			$this->success("登录成功",U("Menu/menu"));
    			}
    			
    		}else{
    			$this->error("登录失败",U("index/index"),1);
    		}
    	}
    }
    //退出
	public function logout(){
		if(IS_POST){
			session("oa_islogin",NULL);
    		session("oa_user_name",NULL);
    		echo "logout";
		}
	}
    //权限控制
    public function authority(){

    	$authority=array("Menu-company","User-list");
    	$user=M("oa_user");
    	$userData=$user->field("user_role")->where("user_name='".session("oa_user_name")."'")->find();
    	if($userData["user_role"]==2){
    		return true;
    	}
    	if(ACTION_NAME=="gethtml"){//通过gethtml方法获取页面的权限名为：控制器+"-"+参数html
    		$powerName=CONTROLLER_NAME."-".I("html");
    	}else{//其他权限名为：控制器+"-"+方法名
    		$powerName=CONTROLLER_NAME."-".ACTION_NAME;
    	}
    	if(in_array($powerName, $authority)){
    		return true;
    	}else{
    		return false;
    	}

    }

}