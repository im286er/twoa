<?php
/*作为中间层的类*/
namespace Common\Controller;
use Think\Controller;
class AmongController extends Controller {
    protected $selfUser;
    protected $baseInfo;//定义基本信息
    protected $user;//用户模型
    protected $Wxqy;//用户模型

	//初始化，类似构造方法，判断是否登录
	public function __construct(){
        parent::__construct();
        $this->baseInfo=D("Info");
        $this->user=D("User");
		$oa_login=session("oa_islogin");

		if(empty($oa_login)){
            vendor('WeixinQy.WeixinQy');//引入Sms
            $this->Wxqy = new \WeixinQy("wx650b23fa694c8ff7","w_oV6aNTMaNUrOjwah0zupDxnWeYmtDR3QiUcD3Uqf584CpwYPB-U79QuhLLD_eJ");
			//防止死循环跳转
            if($_GET["code"]){
                $userInfo=$this->Wxqy->user()->getUserInfo($_GET["code"],true);
                if($userInfo->userid!=""){
                    session("oa_islogin","1");
                    session("oa_user_code",$userInfo->userid);
                }
            }

			if (strtoupper(CONTROLLER_NAME)!="INDEX" && session("oa_user_code")==null) {
				$url=U("index/index");
				echo "<script>top.location.href='$url'</script>";exit;
			}else{

                if(I("user_code")!==null){
                    session("is_register",I("user_code"));
                } 
                //**这里是新员工注册涉及的权限
                if(session("is_register")!==null){
                    $user=M("oa_user");
                    $this->selfUser=$user->where(array("user_code"=>session("is_register"),"user_state"=>"0"))->find();
                    if(!empty($this->selfUser["user_code"])){
                        session("oa_user_code",$this->selfUser["user_code"]);
                    }
                    
                }

            }

		}else{
			//获取到用户的信息
			$user=M("oa_user u");
            // print_r($userData);
            $subQuery = $user->field('user_name ud_name,user_code ud_code')->select(false); 
            $this->selfUser=$user->field("user_id,user_username,user_name,user_code,user_passwd,c.company_name user_company, d.department_name user_department,g.group_name user_group,p.place_name user_place,user_roles,rs.role_name user_rolesn,user_role,r.role_name user_rolen,ud.ud_name user_director,user_phone ,user_avatar,user_sex,user_born ,user_lastlogin,user_entry,user_quit,user_login,user_state")->join(array("left join oa_company c on u.user_company=c.company_id","left join oa_department d on u.user_department=d.department_id","left join oa_group g on u.user_group=g.group_id","left join oa_place p on u.user_place=p.place_id","left join oa_role rs on u.user_roles = rs.role_id","left join oa_role r on u.user_role=r.role_id","left join (". $subQuery.") ud on u.user_director=ud.ud_code"))->where("u.user_code='".session("oa_user_code")."' AND u.user_state=1")->find();
            // print_r($this->selfUser);
			if(empty($this->selfUser["user_username"])){//防止用户离职后还使用
				session("oa_islogin",NULL);
    			session("oa_user_code",NULL);
			}else{
                
				if(strtoupper(MODULE_NAME)=="ADMIN" AND $this->selfUser["user_roles"]>1){
					$url=U("Home/Menu/menu");//防止普通员工进入admin
					echo "<script>top.location.href='$url'</script>";exit;
				}else{
					// if($this->authority()){
					$this->selfUser["user_age"]=get_age($this->selfUser["user_born"]);
					$this->selfUser["user_joinDay"]=get_day($this->selfUser["user_entry"]);
					$this->user=$this->selfUser;
					$this->assign("user",$this->selfUser);
					$this->assign("rand",lcg_value());
					// }else{
					// 	// echo "对不起，您没有权限！";
					// }
					
				}
				
			}
			
		}
	}
	//获取html页面
	public function gethtml(){
		if(IS_POST){
			if($this->authority()){
				$this->display(I("html"));
				return true;
			}
		}
		$this->show("<h1>对不起！您木有权限</h1>");
		return false;
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
            $userData=$user->field("user_id,user_roles,user_role,user_code")->where("u.user_username='".I("user_username")."' AND u.user_passwd='".sha1(I("user_passwd"))."' AND u.user_state=1")->find();
    		if($userData["user_id"]>0){

    			if(strtoupper(MODULE_NAME)=="ADMIN" AND $userData["user_roles"]!=1){
    				$this->error("抱歉！非管理员无法登陆后台",U("index/index"),3);
    				return false;
    			}else{
                    if($userData["user_roles"]==0 && $userData["user_role"]==0){
                        $this->error("抱歉！您还未分配权限，请联系管理员",U("index/index"),3);
                    }else{
                        session("oa_islogin","1");
                        session("oa_user_code",$userData["user_code"]);
                        $this->success("登录成功",U("Menu/menu"));
                        return true;
                    }
    				
    			}
    		}
    	}
    	$this->error("登录失败",U("index/index"),1);
    	return false;
    }
    //退出
	public function logout(){
		if(IS_POST){
			session("oa_islogin",NULL);
    		session("oa_user_code",NULL);
    		echo "logout";
		}
	}
    //权限控制
    public function authority(){

    	$authority=$this->get_auth();

    		if(isset($authority[CONTROLLER_NAME])){
	    		foreach ($authority[CONTROLLER_NAME]["menus"] as $names => $menus) {
	    			if($this->menu_list($menus,I("html"))){
	    				return true;
	    			}
	    		}
	    		return false;
	    	}
    	
    	
    	// foreach ($authority as $key => $value) {
    	// 	$html=explode(",", $value);
    	// }
    	// $authority=explode(",", $this->get_auth());
    	// $user=M("oa_user");
    	// $userData=$user->field("user_role")->where("user_username='".session("oa_user_code")."'")->find();
    	// if($userData["user_role"]==2){
    	// 	return true;
    	// }
    	// if(ACTION_NAME=="gethtml"){//通过gethtml方法获取页面的权限名为：控制器+"-"+参数html
    	// 	$powerName=CONTROLLER_NAME."-".I("html");
    	// }else{//其他权限名为：控制器+"-"+方法名
    	// 	$powerName=CONTROLLER_NAME."-".ACTION_NAME;
    	// }
    	// echo $powerName;
    	// echo $authority;
    	// if(in_array($powerName, $authority)){
    	// 	return true;
    	// }else{
    	// 	return false;
    	// }
        //就是汇总每周的数据，然后以周一作为显示字段？ 
    }

    /**
     * [get_auth 获取权限]
     * @param  array  $elimArray [排除的功能]
     * @param  boolean $module    [是否选择指定的模块Admin或者Home,false则取所有]
     * @param  integer $user_role [获取指定的个别权限]
     * @return [type]             [description]
     */
    public function get_auth($elimArray="",$module=true,$user_role=0){

   	
    	$rauth=M("oa_rauth a");
    	if($user_role>0){
    		$rauthData=json_decode($rauth->field("rauth_auth")->where("a.rauth_role='{$user_role}'")->find()["rauth_auth"],true);
    	}else{
            if($this->selfUser["user_role"]==0){
                $rauthData=json_decode($rauth->field("rauth_auth")->join("oa_user u")->where("u.user_id='".$this->selfUser["user_id"]."' AND u.user_roles=a.rauth_role")->find()["rauth_auth"],true);
            }else{
                $rauthData=json_decode($rauth->field("rauth_auth")->join("oa_user u")->where("u.user_id='".$this->selfUser["user_id"]."' AND u.user_role=a.rauth_role")->find()["rauth_auth"],true);
            }
    		
    	}
    	if($module){
    		$rAtuhArray=$rauthData[MODULE_NAME];
    		if(!empty($elimArray)){
	    		foreach ($elimArray as $elim) {
	    			if(isset($rAtuhArray[$elim])){
	    				unset($rAtuhArray[$elim]);
	    			}
	    		}
	    	}
    	}else{
			$rAtuhArray=$rauthData;
    	}
    	return $rAtuhArray;
    }

    /**
     * [menu_list 循环判断权限]
     * @param  [type] $array [被判断的数组/值]
     * @param  [type] $menu  [要判断的html名]
     * @return [type]        [布尔值]
     */
    protected function menu_list($array,$menu){

    	if(is_array($array["menus"])){

    		foreach ($array["menus"] as $key => $value) {
    			return $this->menu_list($value,$menu);
    		}
    	}else{
    		if($menu==$array["menus"]){
    			return true;
    		}else{
    			return false;
    		}
    	}
    }

}