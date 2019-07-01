<?php
namespace app\admin\model;
use think\Model;
class Img_carousel extends Model{
	public function sel(){
		return db("img_carousel")->field("Id id,img_url url,herf")->order("Id")->select();
	}
	public function del($id){
		$flag=db("img_carousel")->delete($id);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function edit($id,$value){
		$flag=db("img_carousel")->where("Id",$id)->update(["herf"=>$value]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function sel_max_id(){
		return db("img_carousel")->max("Id");
	}
	public function add($id,$url){
		$flag=db("img_carousel")->insert(["Id"=>$id,"img_url"=>$url]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
}
?>