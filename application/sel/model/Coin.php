<?php
namespace app\sel\model;
use think\Model;

class Coin extends Model{
	public function sel($str=null){
		if($str==null){
			return db("coin")->where("state","1")->select();
		}else{
			return db("coin")->field($str)->where("state","1")->select();
		}
	}
	public function coin_list(){
		return db("coin")->field("Id,name,if_small_amount,min_withdraw")->where("state","1")->select();
	}
}
?>