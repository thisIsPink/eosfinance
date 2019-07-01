<?php
namespace app\admin\model;
use think\Model;

class Help extends Model{
	public function sel($page,$limit,$type,$f_id=null){
		if($f_id==null){
			return db("help")->field("Id id,title")->where("type",$type)->page($page,$limit)->select();
		}else{
			return db("help")->field("Id id,title")->where("type",$type)->where("f_id",$f_id)->page($page,$limit)->select();
		}
	}
	public function sum($type,$f_id=null){
		if($f_id==null){
			return db("help")->where("type",$type)->count();
		}else{
			return db("help")->where("type",$type)->where("f_id",$f_id)->count();
		}
	}
	public function add($data){
		$flag=db("help")->insert($data);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function del($id){
		$flag=db("help")->delete($id);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function info($id){
		return db("help")->find($id);
	}
	public function edit($id,$data){
		$flag=db("help")->where("Id",$id)->update($data);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
}
?>