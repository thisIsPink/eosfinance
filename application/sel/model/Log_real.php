<?php
namespace app\sel\model;
use think\Model;
class Log_real extends Model{
	public function add($data){
		$flag=db("log_real")->insert($data);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function user_log($user,$time){
		return db("log_real")->alias("lr")->join("coin c","lr.coin=c.Id")->field("type,number,lr.state,time,c.name coin,txid,address,fee")->where("user",$user)->where("time","<",$time)->limit(20)->order("time","desc")->select();
	}
}
?>