<?php
namespace app\admin\model;
use think\Model;
class User extends Model{
	//查询用户
	public function json_sel($page,$limit,$content=null){
		if($content==null){
			return db("user")->field('Id id,EOS_account account,phone,email,money,inviter,reg_time,up_login_time,state')->page($page,$limit)->select();
		}else{
			return db("user")->field('Id id,EOS_account account,phone,email,money,inviter,reg_time,up_login_time,state')->where($content)->page($page,$limit)->select();
		}
	}
	//用户总和
	public function sum($content=null){
		if($content==null){
			return db("user")->count();
		}else{
			return db("user")->where($content)->count();
		}
	}
	//改变状态
	public function state_change($id,$state){
		$flag=db("user")->where("Id",$id)->update(["state"=>$state]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	//邀请人查询
	public function inviter_sel($page,$limit,$content=null){
		if($content==null){
			return db("user")->alias("a")->join('user b','a.inviter=b.Id')->where("a.inviter",'>','0')->field('a.Id aid,a.nick anick,b.Id bid,b.nick bnick,a.reg_time time')->page($page,$limit)->select();
		}else{
			return db("user")->alias("a")->join('user b','a.inviter=b.Id')->where("a.inviter",'>','0')->field('a.Id aid,a.nick anick,b.Id bid,b.nick bnick,a.reg_time time')->where($content)->page($page,$limit)->select();
		}
	}
	//邀请人总和
	public function inviter_sum($content=null){
		if($content==null){
			return db("user")->where("inviter",'<>','null')->count();
		}else{
			return db("user")->alias("a")->where("inviter",'<>','null')->where($content)->count();
		}
	}
	//设置金额
	public function set_money($user_id,$money){
		db("user")->where("Id",$user_id)->update(["money"=>$money]);
	}
	public function is_binding($value){
		$flag=db("user")->where("EOS_account",$value)->find();
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	//绑定账号
	public function binding($id,$value){
		db("user")->where("Id",$id)->update(["EOS_account"=>$value]);
	}
	//tag值改变
}
?>