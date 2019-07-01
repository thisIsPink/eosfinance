<?php
namespace app\admin\model;
use think\Model;

class Log_login extends Model{
	public function sel($page,$limit,$content=null){
		if($content==null){
			return db("log_login")->alias('log')->join('user','log.user_id=user.Id')->field('log.user_id id,user.EOS_account account,log.time,log.ip,log.state')->order("time","desc")->page($page,$limit)->select();
		}else{
			return db("log_login")->alias('log')->join('user','log.user_id=user.Id')->field('log.user_id id,user.EOS_account account,log.time,log.ip,log.state')->where($content)->page($page,$limit)->select();
		}
	}
	public function sum($content=null){
		if($content==null){
			return db("log_login")->alias('log')->join('user','log.user_id=user.Id')->count("log.Id");
		}else{
			return db("log_login")->alias('log')->join('user','log.user_id=user.Id')->where($content)->count("log.Id");
		}	
	}
}
?>