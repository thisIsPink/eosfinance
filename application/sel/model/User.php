<?php
namespace app\sel\model;
use think\Model;

class User extends Model{
	public function add($data){
		$flag=db("user")->insertGetId($data);
		$num=(string)ceil($flag*1.4);
		if(strlen($num)<2){
			$str="u95000".$num;
		}else if(strlen($num)<3){
			$str="u9500".$num;
		}else if(strlen($num)<4){
			$str="u950".$num;
		}else{
			$str="u95".$num;
		}
		db("user")->where("Id",$flag)->update(["nick"=>$str,"invitation"=>md5($num),"tag"=>100000+ceil($flag*1.3)]);
		if($flag){
			return $flag;
		}else{
			return false;
		}
	}
	public function is_user($name,$type){
		$flag=db('user')->field("Id id")->where($type,$name)->find();
		if($flag){
			return $flag;
		}else{
			return false;
		}
	}
	public function login($name,$pwd,$type){
		$flag=db("user")->where($type,$name)->where("password",md5($pwd))->find();
		if($flag){
			$this->set_time($flag["Id"]);
			return $flag["Id"];
		}else{
			return false;
		}
	}
	public function sel($id,$str){
		$data=db("user")->field($str)->where("Id",$id)->find();
		if($data){
			return $data;
		}else{
			return false;
		}
	}
	public function is_sel($field,$value){
		$data=db("user")->where($field,$value)->count();
		if ($data) {
			return $data;
		}else{
			return false;
		}
	}
	public function set_inviter($id,$to_id){
		$flag=db("user")->where("Id",$id)->update(["inviter"=>$to_id]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	private function set_time($id){
		db("user")->where("Id",$id)->update(["ip"=>ip(),"up_login_time"=>time()]);
	}
	public function bind_account($id,$EOS){
		$sel=db("user")->field("EOS_account account")->where("Id",$id)->find();
		if($sel["account"]==null){
			db("user")->where("Id",$id)->update(["EOS_account"=>$EOS]);
			return true;
		}else{
			return false;
		}
	}
	public function edit_field($id,$field,$value){
		if($field=="phone"||$field=="email"||$field=="password"||$field=="pay_password"||$field=="nick"||$field=='phone_head'){
			$flag=db("user")->where("Id",$id)->update([$field=>$value]);
			if($flag){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	public function money_def($id,$money){
		db("user")->where("Id",$id)->update(["money"=>$money]);
	}
	public function auto_bid($id,$money){
		$flag=db("user")->where("Id",$id)->update(["auto_bidding"=>$money]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function set_money($id,$data){
		db("user")->where("Id",$id)->update(["money"=>$data]);
	}
	public function out($id){
		return db("user")->where("Id",$id)->update(["token"=>"Unexpectedly, it's me."]);
	}
	public function set_safe($id,$time){
		return db("user")->where("Id",$id)->update(["safe_time"=>$time]);
	}
	public function inv_list_id($user_id){
		return db('user')->field('Id,nick')->where('inviter',$user_id)->select();
	}
}
?>