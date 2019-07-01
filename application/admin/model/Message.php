<?php
namespace app\admin\model;
use think\Model;

class Message extends Model{
	public function add($id,$content){
		$flag=db("message")->insert(["user_id"=>$id,"time"=>time(),"content"=>$content,'state'=>'0']);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function sel_id($id){
		return db("message")->where("user_id",$id)->field("time,content")->select();
	}
}
?>