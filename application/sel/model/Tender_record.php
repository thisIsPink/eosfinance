<?php
namespace app\sel\model;
use think\Model;

class Tender_record extends Model{
	//查询标的投资记录
	public function tender_recode_id($id,$page,$limit){
		return db("tender_record")->alias("t")->join("user u","u.Id=t.user_id")->field("u.EOS_account name,t.money,t.time")->where("bid_id",$id)->page($page,$limit)->select();
	}
	//总投标金额
	public function all_money(){
		return db("tender_record")->alias("t")->rightJoin("coin c","t.coin_id=c.Id")->field("sum(CAST(CAST(t.money as decimal(9,2)) / CAST(c.proportion as decimal(9,2)) as decimal(9,2))) sum")->select();
	}
	//返还金额
	public function repay_money(){
		return db("tender_record")->alias("t")->rightJoin("coin c","t.coin_id=c.Id")->field("sum(CAST(CAST(t.money as decimal(9,2)) / CAST(c.proportion as decimal(9,2)) as decimal(9,2))) sum")->where("t.state","3")->select();
	}
	//成交金额
	public function ok_money(){
		return db("tender_record")->alias("t")->join("coin c","t.coin_id=c.Id")->field("SUM(cast(cast(t.money as decimal(18,2))/cast(c.proportion as decimal(18,2)) as decimal(18,2))) a")->where("t.state","3")->select();
	}
	//首页滚动
	public function sel_newdata(){
		return db("tender_record")->alias("t")->join("user u","t.user_id=u.Id")->join("bid_issuing b","t.bid_id=b.Id")->order("t.time","desc")->field("u.EOS_account nick,b.title")->limit(15)->select();
	}
	//查看用户是否投过标
	public function is_user($id){
		$flag=db("tender_record")->where("user_id",$id)->find();
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	//添加一条记录
	public function add($data){
		db("tender_record")->insert($data);
	}
	//查看用户代还本金的总和
	public function due_money($user){
		return db("tender_record")->alias("t")->Join("coin c","t.coin_id=c.Id")->field("sum(CAST(CAST(t.money as decimal(9,2)) / CAST(c.proportion as decimal(9,2)) as decimal(9,2))) sum")->where("t.user_id",$user)->where([["t.state","in","1,2"]])->select();
	}
	//用户投资记录
	public function user_log($id,$page,$limit){
		return db("tender_record")->alias("t")->join("bid_issuing b","t.bid_id=b.Id")->join("coin c","t.coin_id=c.Id")->field("t.time up1,t.state,t.money,CONCAT(sum(t.money),' ',c.name) name,b.repayment_time end,b.title up,b.Id bid")->where("user_id",$id)->order('t.time',"desc")->page($page,$limit)->group('t.bid_id')->select();
	}
	//用户投标总数
	public function user_sum_num($id){
		return db("tender_record")->where("user_id",$id)->count();
	}
	//用户投标金额
	public function user_sum_money($id){
		return db("tender_record")->alias("t")->Join("coin c","t.coin_id=c.Id")->field("sum(CAST(CAST(t.money as decimal(9,2)) / CAST(c.proportion as decimal(9,2)) as decimal(9,2))) sum")->where("t.user_id",$id)->select();
	}
	//用户代收本金数量
	public function collected_num($user){
		return db("tender_record")->where("user_id",$user)->where([["state","in","1,2"]])->count();
	}
	public function user_coll_num($user_id){
		return db("tender_record")->alias("t")->join("bid_issuing b","t.bid_id=b.Id")->where("t.user_id",$user_id)->group("bid_id")->where("b.state","7")->count();
	}
	public function user_coll_money($user_id){
		$sum=db("tender_record")->alias("t")->join("coin c","t.coin_id=c.Id")->join("bid_issuing b","t.bid_id=b.Id")->field('sum(CAST(CAST(t.money as decimal(16,8)) / CAST(c.proportion as decimal(16,8)) as decimal(16,4))) sum,t.bid_id,annual_profit,repayment_time')->where("t.user_id",$user_id)->group("t.bid_id")->select();
		$money=0;
		foreach ($sum as $key => $value) {
			$money+=$value["sum"]*$value["annual_profit"]/360/100*$value["repayment_time"];
		}
		$issued=db("log_transaction")->alias("lt")->join("coin c","lt.coin=c.Id")->where("type","3")->where("user",$user_id)->field("sum(CAST(CAST(lt.money as decimal(16,8)) / CAST(c.proportion as decimal(16,8)) as decimal(16,4))) sum")->select();
		$money-=$issued[0]['sum'];
		return $money;
	}
}
?>