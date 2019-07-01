<?php
namespace app\sel\model;
use think\Model;

class Bid_issuing extends Model{
	public function sel($page,$limit,$type=null,$state='1,4,7,8',$offset=0){
		$data=db("bid_issuing")->alias("b")->leftJoin("tender_record t","b.Id=t.bid_id")->leftJoin("coin c","b.need_coin_id=c.Id")->field("b.Id id,b.title,b.total,b.annual_profit,b.repayment_time repay,b.end_bid_time end_time,SUM(t.money) reised,c.name coin,b.state")->where([["b.state",'in',$state]])->where("start_time",'<',time())->group("b.Id")->limit($limit,$offset)->page($page);
		if($type==null){
			return $data->select();
		}else{
			return $data->where("type",$type)->select();
		}
	}
	public function sum($type=null,$state='1,4,7,8'){
		if($type==null){
			return db("bid_issuing")->where([["state",'in',$state]])->count();
		}else{
			return db("bid_issuing")->where([["state",'in',$state]])->where("type",$type)->count();
		}
	}
	public function sel_id($id){
		return db("bid_issuing")->alias("b")->leftJoin("tender_record t","b.Id=t.bid_id")->join("coin c","c.Id=interest_coin_id")->join("coin ca","ca.Id=b.need_coin_id")->field("b.Id id,title,total,annual_profit,b.repayment_time repay,b.end_bid_time end_time,start_time start,SUM(t.money) reised,c.name coin,compute,min_eos,max_eos,b.purpose,b.proportion,b.state,ca.name need")->where([["b.state",'in','1,4,5,6,7,8'],["b.Id",'=',$id]])->group("b.Id")->find();
	}
	public function sel_details_id($id){
		return db("bid_issuing")->field("Id id,details,info")->where([["state",'in','1,4,5,6,7,8'],["Id",'=',$id]])->find();
	}
	public function set_state($id,$state){
		$flag=db("bid_issuing")->where("Id",$id)->update(["state"=>$state]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
}
?>