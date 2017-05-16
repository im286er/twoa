<?php
namespace Home\Controller;
use Think\Controller;
class MenuController extends AmangController {
	function index(){
		$this->display("menu");
	}
}