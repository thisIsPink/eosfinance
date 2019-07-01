<?php
namespace app\admin\model;
use think\Model;
class Notice extends Model{
	public function sel($page,$limit,$content=null){
		$data=db("notice")->field("Id id,title,abstract,time,type,state");
		if($content==null){
			return $data->page($page,$limit)->select();
		}else{
			return $data->where($content)->page($page,$limit)->select();
		}
	}
	public function sum($content=null){
		if($content==null){
			return db("notice")->count();
		}else{
			return db("notice")->where($content)->count();
		}
	}
	public function state_change($id,$state){
		$flag=db("notice")->where("Id",$id)->update(["state"=>$state]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function sel_id($id){
		return db("notice")->where("Id",$id)->find();
	}
	public function add($title,$abstract,$type,$content){
		$flag=db("notice")->insert(["title"=>$title,"time"=>time(),"abstract"=>$abstract,"content"=>$content,"type"=>$type,"state"=>"1"]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function edit($id,$title,$abstract,$type,$content){
		$flag=db("notice")->where("Id",$id)->update(["title"=>$title,"time"=>time(),"abstract"=>$abstract,"content"=>$content,"type"=>$type,"state"=>"1"]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	
}
?>