<?php
namespace Home\Controller;
use Think\Controller;
class MenuController extends AmangController {
	//menu
	function index(){
		$this->display("menu");
	}
	//个人信息
	function profile(){
		$this->display("profile");
	}
}