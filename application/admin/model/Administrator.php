<?php
namespace app\admin\model;

use think\Model;
class Administrator extends Model{
	public function is_login($name,$pwd){
		$data=db("administrator")->where(["user_name"=>$name,"password"=>$pwd])->where("state","1")->find();
		if($data!=null){
			$this->update_ip($data["Id"]);
			return true;
		}else{
			return false;
		}
	}
	private function update_ip($id){
		db("administrator")->where('Id',$id)->update(["ip"=>ip()]);
	}
	public function sel($page,$limit,$content=null){
		if($content==null){
			return db('administrator')->field("Id id,user_name name,role,ip,state")->page($page,$limit)->select();
		}else{
			return db('administrator')->field("Id id,user_name name,role,ip,state")->where($content)->page($page,$limit)->select();
		}
	}
	public function sum($content=null){
		if($content==null){
			return db("administrator")->count();
		}else{
			return db("administrator")->where($content)->count();
		}
	}
	public function add($name,$pwd,$role){
		$flag=db("administrator")->insert(["user_name"=>$name,"password"=>$pwd,"role"=>$role]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function del($id){
		$flag=db("administrator")->delete($id);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function state_change($id,$state){
		$flag=db("administrator")->where("Id",$id)->update(["state"=>$state]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
}
?>