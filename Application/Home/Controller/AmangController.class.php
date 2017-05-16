<?php
namespace Home\Controller;
use Think\Controller;
class AmangController extends Controller {
	public function _initialize(){
		$oa_login=session("oa_islogin");
		if(empty($oa_login)){
			$url=U("index/index");
			echo "<script>top.location.href='$url'</script>";exit;
		}
	}

}