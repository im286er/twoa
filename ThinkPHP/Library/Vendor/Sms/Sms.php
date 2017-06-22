<?php
// +----------------------------------------------------------------------
// | 短信发送模块，目前支持智讯和E讯通两个服务商
// +----------------------------------------------------------------------
// | Author: vition <369709991@qq.com>
// +----------------------------------------------------------------------
class Sms{
	/**
	* 短信服务商相关属性
	* @service 服务商名字
	* @userid 用户id
	* @account 账户名
	* @password 账户密码
	* @sign 账户签名
	*/
	protected $service;
	protected $userid;
	protected $account;
	protected $password;
	protected $sign;
	function __construct($service="智讯"){
		$this->service=$service;
	}	
	/**
	* 修改服务商
	* @service 服务商名字
	*/
	protected function changeService($service){
		$this->service=$service;
		return $this;
	}

	/**
	* 设置服务商账号密码
	* @userid 用户id
	* @account 账户名
	* @password 账户密码
	* @sign 账户签名
	*/
	public function setOption($userid,$account,$password,$sign){
		$this->userid=$userid;
		$this->account=$account;
		$this->password=$password;
		$this->sign=$sign;
		return $this;
	}

	/**
	* 发送短信
	* @phone 手机号码
	* @content 内容 短信内容不需要urlencode编码
	* @time 不定时发送，值为0，定时发送，智讯输入格式YYYYMMDDHHmmss的日期值 E讯通格式为 YYYY-MM-DD HH:MM:SS 如:2008-05-12 10:00:00
	* @sign 账户签名
	*/
	public function send($phone,$content,$time="",$sign=""){
		if(!empty($sign)) $this->sign=$sign;
		if($this->service=="智讯"){
			/*智讯发送方式*/
			$postData = array();
			$postData['userid'] = $this->userid;
			$postData['account'] = $this->account;
			$postData['password'] = $this->password;
			$postData['content'] = "{$content}"; 
			$postData['mobile'] = "{$phone}";
			$postData['sendtime'] = $time;
			$url='http://hyt.uewang.net/sms.aspx?action=send';
			echo $this->post($url,$postData);

		}else{
			/*其他默认为E讯通

			*/
			$postData = array();
			$postData['userid'] = $this->userid;
			$postData['password'] = $this->password;
			$postData['msg'] = "{$content}"; //短信内容不需要urlencode编码
			$postData['destnumbers'] = "{$phone}";
			$postData['sendtime'] = $time;
			$url="http://211.147.244.114:9801/CASServer/SmsAPI/SendMessage.jsp";
			echo $this->post($url,$postData);

		}
	}
	/**
	* 私有方法，内部调用，发送数据
	* @url 服务商api接口地址
	* @content 内容 短信内容不需要urlencode编码
	* @time 不定时发送，值为0，定时发送，输入格式YYYYMMDDHHmmss的日期值
	* @sign 账户签名
	*/
	private function post($url,$postData){
		$o='';
		foreach ($postData as $k=>$v)
		{
			$o.="$k=".urlencode($v).'&';
		}
		$postData=substr($o,0,-1);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
		return curl_exec($ch);
	}
}