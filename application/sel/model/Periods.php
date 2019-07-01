<?php
namespace app\sel\model;
use think\Model;
use think\Db;
class Periods extends Model{
	public function cycle_recode_id($id){
		return db("periods")->field("phase,time,state")->where("bid",$id)->order('phase')->select();
	}
	public function ok_money(){
		return db("periods")->alias("up")->join("bid_issuing b","up.bid=b.Id")->join("coin c","b.interest_coin_id=c.Id")->field("SUM(cast(cast(up.money as decimal(18,2))/cast(c.proportion as decimal(18,2)) as decimal(18,2))) sum")->where("up.state","2")->select();
	}
	public function user_all($id){
		return db("periods")->alias("up")->join("tender_record t","up.tender_record_id=t.Id")->join("coin c","t.coin_id=c.Id")->field("SUM(cast(cast(up.money as decimal(18,2))/cast(c.proportion as decimal(18,2)) as decimal(18,2))) sum")->where("up.state","1")->where("user_id",$id)->select();
	}
	public function user_ranking($id,$up=false){
		if($up){
			return Db::query("select * from (select @rownum := @rownum +1 AS no,user_id,SUM(cast(cast(up.money as decimal(18,2))/cast(c.proportion as decimal(18,2)) as decimal(18,2))) sum from (SELECT @rownum := 0) r,ef_periods as up,ef_tender_record as t,ef_coin as c,ef_user as u where up.tender_record_id=t.Id and t.coin_id=c.Id and t.user_id=u.Id and up.state='2' group by user_id) t where no=".$id." order by no");
		}else{
			return Db::query("select * from (select @rownum := @rownum +1 AS no,user_id,SUM(cast(cast(up.money as decimal(18,2))/cast(c.proportion as decimal(18,2)) as decimal(18,2))) sum from (SELECT @rownum := 0) r,ef_periods as up,ef_tender_record as t,ef_coin as c,ef_user as u where up.tender_record_id=t.Id and t.coin_id=c.Id and t.user_id=u.Id and up.state='2' group by user_id) t where user_id=".$id." order by no");
		}
	}
	public function ranking($page,$limit){
		return Db::query("select * from (select @rownum := @rownum +1 AS no,nick,SUM(cast(cast(up.money as decimal(18,2))/cast(c.proportion as decimal(18,2)) as decimal(18,2))) sum from (SELECT @rownum := 0) r,ef_periods as up,ef_tender_record as t,ef_coin as c,ef_user as u where up.tender_record_id=t.Id and t.coin_id=c.Id and t.user_id=u.Id and up.state='2' group by user_id) t  order by no limit ".($page-1)*$limit.','.$limit);
	}
	public function add($data){
		return db("periods")->insert($data);
	}
}
?>