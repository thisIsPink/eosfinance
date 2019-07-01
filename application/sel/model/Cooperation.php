<?php
namespace app\sel\model;
use think\Model;
class Cooperation extends Model{
	public function add($data){
		$flag=db("cooperation")->insert($data);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
}
?>