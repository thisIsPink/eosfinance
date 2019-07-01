<?php
namespace app\admin\model;
use think\Model;
class Super_agend extends Model{
	public function sel($page,$limit){
		return db("super_agent")->alias("s")->join("user u","u.Id=s.user_id")->field("u.Id id,u.EOS_account account,u.email,u.phone,u.up_login_time,s.proportion,s.state")->page($page,$limit)->select();
	}
	public function sum(){
		return db("super_agent")->count();
	}
	public function state_change($id,$state){
		$flag=db("super_agent")->where("Id",$id)->update(["state"=>$state]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function sel_id($id){
		$flag=db("super_agent")->where("user_id",$id)->find();
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function add($id){
		$data=db("agent_income")->find(1);
		$flag=db("super_agent")->insert(["user_id"=>$id,"time"=>time(),"state"=>"1","proportion"=>$data["super"]]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function proportion_edit($id,$value){
		$flag=db("super_agent")->where("Id",$id)->update(["proportion"=>$value]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
}
?>