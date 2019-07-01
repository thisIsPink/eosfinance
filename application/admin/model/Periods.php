<?php
namespace app\admin\model;
use think\Model;

class Periods extends Model{
	public function sel($page,$limit,$content=null){
		$data=db("periods")->alias('per')->join("bid_issuing b",'b.Id=per.bid')->join("coin c","c.Id=b.interest_coin_id")->field('per.Id id,bid,phase,per.time,money,c.name coin,day,per.state')->order("Id")->page($page,$limit);
		if($content==null){
			return $data=$data->select();
		}else{
			return $data=$data->where($content)->select();
		}
	}
	public function sum($content=null){
		if($content==null){
			return db("periods")->count();
		}else{
			return db("periods")->alias('per')->join("bid_issuing b",'b.Id=per.bid')->join("coin c","c.Id=b.interest_coin_id")->where($content)->count();
		}
	}
	public function state_ok($id){
		$flag=db("periods")->where("Id",$id)->update(["state"=>"2"]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function info($id){
		return db("periods")->where("Id",$id)->find();
	}
}
?>