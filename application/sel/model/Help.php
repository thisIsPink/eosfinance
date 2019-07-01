<?php
namespace app\sel\model;
use think\Model;

class Help extends Model{
	public function sel($type){
		return db("help")->field("Id id,title,content,f_id")->where("type",$type)->select();
	}
}
?>