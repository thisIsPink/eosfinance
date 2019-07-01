<?php
namespace app\sel\model;
use think\Model;
use think\Db;
class Bonus extends Model{
	public function add($data){
		$flag=db("bonus")->insert($data);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
	public function ranking($page,$limit){
		return Db::query("select * from (select @rownum := @rownum +1 AS no,nick,SUM(cast(cast(b.money as decimal(18,2))/cast(c.proportion as decimal(18,2)) as decimal(18,2))) sum from (SELECT @rownum := 0) r,ef_bonus as b,ef_user as u,ef_coin as c where b.coin=c.Id and b.user=u.Id group by u.Id) t order by no limit ".($page-1)*$limit.','.$limit);
	}
	public function friend_ranking_all($user_id,$page,$limit){
		$data=db("bonus")->alias("b")->join("coin c","b.coin=c.Id")->join("user u","b.in_user=u.Id")->field("nick,u.Id id,COUNT(distinct b.bid) num,SUM(cast(cast(b.money as decimal(18,4))/cast(c.proportion as decimal(18,2)) as decimal(18,6))) sum")->where("user",$user_id)->group("b.in_user")->page($page,$limit)->select();
		if($data){
			return $data;
		}else{
			return [];
		}
	}
	public function friend_ranking_single($in_user_id,$time){
		$data=db("bonus")->alias("b")->join("coin c","b.coin=c.Id")->join("user u","b.in_user=u.Id")->field("nick,1 num,SUM(cast(cast(b.money as decimal(18,4))/cast(c.proportion as decimal(18,2)) as decimal(18,6))) sum")->where("in_user",$in_user_id)->where("time",">",$time)->group("bid")->select();
		if($data){
			return $data;
		}else{
			return [];
		}
	}
	public function friend_ranking_sum($user_id){
		return db("bonus")->field("COUNT(distinct in_user) num")->where("user",$user_id)->select();
	}
	public function personal_ranking($user_id,$time=0){
		return Db::query("select no,sum from (select @rownum := @rownum +1 AS no,user,SUM(cast(cast(b.money as decimal(18,4))/cast(c.proportion as decimal(18,2)) as decimal(18,6))) sum from (SELECT @rownum := 0) r,ef_bonus as b,ef_coin as c where b.coin=c.Id and time>".$time." group by b.user) t where user=".$user_id);
	}
}
?>