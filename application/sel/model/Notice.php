<?php
namespace app\sel\model;
use think\Model;

class Notice extends Model{
	public function sel($page,$limit){
		return db("notice")->field("Id id,title,time")->where("state","1")->order("time","desc")->page($page,$limit)->select();
	}
	public function info($id){
		return db("notice")->field("title,abstract,time,type,content")->where("state","1")->where("Id",$id)->find();
	}
}
?>