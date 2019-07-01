<?php
namespace app\sel\model;
use think\Model;

class Super_agent extends Model{
	public function is_user($id){
		$flag=db("super_agent")->where("user_id",$id)->where("state","1")->find();
		if($flag){
			return true;
		}else{
			return false;
		}
	}
}
?>