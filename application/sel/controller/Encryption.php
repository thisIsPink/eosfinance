<?php
namespace app\sel\controller;
use think\Controller;
use app\api\model\User;
class Encryption extends Controller{
	public function set_token($user_id){
		$information['state']=false;
		$time=time();
		$header=array('typ'=>'JWT');
		$array=array('iss'=>'kingcat','iat'=>$time,'exp'=>3600,'sub'=>"text",'user_name'=>$user_id);
		$str=base64_encode(json_encode($header)).'.'.base64_encode(json_encode($array));
		$information['token']=$str;
		$flag=$this->save_token($user_name,$infomation["token"]);
		$information['username']=$user_name;
		if($flag){
			$information["state"]=true;
		}
		return $information;
	}
	private function save_token($user_id,$token){
		$user=new User;
		return $user->set_token($user_id,$token);
	}
}
?>