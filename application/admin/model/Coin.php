<?php
namespace app\admin\model;
use think\Model;

class Coin extends Model{
	public function sel($page,$limit){
		return db("coin")->field("Id id,name coin,smallest_unit min_unit,min_recharge min_re,max_recharge max_re,if_small_amount amount,proportion,img,publisher,state")->page($page,$limit)->select();
	}
	public function sum(){
		return db("coin")->count();
	}
	public function add($name,$unit,$min,$max,$amount,$proportion){
		$flag=db("coin")->insert(["name"=>$name,"smallest_unit"=>$unit,"min_recharge"=>$min,"max_recharge"=>$max,"if_small_amount"=>$amount,"proportion"=>$proportion,"state"=>"1"]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function state_change($id,$state){
		$flag=db("coin")->where("Id",$id)->update(["state"=>$state]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function edit($id,$field,$value){
		$flag= db("coin")->where("Id",$id)->update([$field=>$value]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function amount_edit($id,$value){
		$flag=db("coin")->where("Id",$id)->update(["if_small_amount"=>$value]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function coin_list(){
		return db("coin")->field("Id id,name,proportion")->select();
	}
}
?>