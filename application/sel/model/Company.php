<?php
namespace app\sel\model;
use think\Model;

class Company extends Model{
	public function sel(){
		return db("company")->where("Id","1")->find();
	}
}
?>