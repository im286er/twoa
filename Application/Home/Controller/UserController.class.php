<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends AmangController {
	//新建用户
	public function create(){
		$this->display("create");
	}
}