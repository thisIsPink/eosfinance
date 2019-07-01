<?php
namespace app\admin\model;

use think\Model;

class Bid_issuing extends Model{
	//查询
	public function sel_min($page,$limit,$content=null){
		if($content==null){
			return db("bid_issuing")->alias("b")->join("coin c","b.need_coin_id=c.Id")->field("b.Id id,title,start_time time,total,c.name need_coin,b.state")->order("Id",'desc')->page($page,$limit)->select();
		}else{
			return db("bid_issuing")->alias("b")->join("coin c","b.need_coin_id=c.Id")->field("b.Id id,title,start_time time,total,c.name need_coin,b.state")->where($content)->order("Id",'desc')->page($page,$limit)->select();
		}
	}
	//查询数目
	public function sum($content=null){
		if($content==null){
			return db("bid_issuing")->alias("b")->count();
		}else{
			return db("bid_issuing")->alias("b")->where($content)->count();
		}
	}
	//还款情况
	public function repay_sel($page,$limit,$content=null){
		$data=db("bid_issuing")->alias("b")->join("coin c","b.need_coin_id=c.Id")->join("coin c2","b.interest_coin_id=c2.Id")->join("tender_record t","b.Id=t.bid_id")->join("periods p","b.Id=p.bid")->field("b.Id id,b.repayment_time rtime,sum(t.money) tmoney,c.name coin,sum(p.money) pmoney,c2.name icoin,b.state state")->group('b.Id')->where("b.state","8");
		if($content==null){
			return $data->page($page,$limit)->select();
		}else{
			return $data->where($content)->page($page,$limit)->select();
		}
	}
	//还款总数
	public function repay_sum($content=null){
		if($content==null){
			return db("bid_issuing")->where("state","8")->count();
		}else{
			return db("bid_issuing")->alias("b")->where("b.state","8")->where($content)->count();
		}
	}
	//修改状态
	public function state_edit($id,$state){
		$flag=db("bid_issuing")->where("Id",$id)->update(["state"=>$state]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	//查找id信息
	public function sel_id($id){
		return db("bid_issuing")->where("Id",$id)->find();
	}
	//添加
	public function add($data){
		$flag=db("bid_issuing")->insertGetId($data);
		if($flag){
			return $flag;
		}else{
			return false;
		}
	}
	//编辑
	public function edit($id,$data){
		$flag=db("bid_issuing")->where("Id",$id)->update($data);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
}
?>