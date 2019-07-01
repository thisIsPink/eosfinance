<?php
namespace app\sel\model;
use think\Model;

class Log_login extends Model{
	public function add($user_id){
		$flag=db("log_login")->insert(["user_id"=>$user_id,"time"=>time(),"state"=>"1","ip"=>ip()]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
}
?>