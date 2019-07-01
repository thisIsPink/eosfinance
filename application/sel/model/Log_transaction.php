<?php
namespace app\sel\model;
use think\Model;
use think\Db;
class Log_transaction extends Model{
	//分利排行
	public function int_ranking($page,$limit){
		return db("log_transaction")->alias("lt")->join("coin c","c.Id=lt.coin")->join("user u","u.Id=lt.user")->field("EOS_account nick,SUM(cast(cast(lt.money as decimal(18,4))/cast(c.proportion as decimal(18,2)) as decimal(18,6))) sum")->where("type","3")->group("lt.user")->order("sum","desc")->page($page,$limit)->select();
	}
	//邀请排行
	public function inv_ranking($page,$limit){
		return db("log_transaction")->alias("lt")->join("coin c","c.Id=lt.coin")->join("user u","u.Id=lt.user")->field("EOS_account nick,SUM(cast(cast(lt.money as decimal(18,4))/cast(c.proportion as decimal(18,2)) as decimal(18,6))) sum")->where("type","4")->group("lt.user")->order("sum","desc")->page($page,$limit)->select();
	}
	//分利总额
	public function user_profit_all($user_id){
		return db("log_transaction")->alias("lt")->join("coin c","c.Id=lt.coin")->field("SUM(cast(cast(lt.money as decimal(18,6))/cast(c.proportion as decimal(18,4)) as decimal(18,6))) sum")->where("type","3")->where("user",$user_id)->select();
	}
	//收益排名
	public function user_ranking($id,$up=false){
		if($up){
			return Db::query("select * from (select @rownum := @rownum +1 AS no,a.* from (SELECT @rownum := 0) r,(select lt.user,SUM(cast(cast(lt.money as decimal(18,4))/cast(c.proportion as decimal(18,2)) as decimal(18,6))) sum from ef_log_transaction as lt,ef_coin as c,ef_user as u where lt.coin=c.Id and lt.user=u.Id and lt.type in (3,4) group by lt.user order by sum desc) a) b where no=".$id);
		}else{
			return Db::query("select * from (select @rownum := @rownum +1 AS no,a.* from (SELECT @rownum := 0) r,(select lt.user,SUM(cast(cast(lt.money as decimal(18,4))/cast(c.proportion as decimal(18,2)) as decimal(18,6))) sum from ef_log_transaction as lt,ef_coin as c,ef_user as u where lt.coin=c.Id and lt.user=u.Id and lt.type in (3,4) group by lt.user order by sum desc) a) b where user=".$id);
		}
	}
	//分利数量
	public function user_sum_num($user){
		return db("log_transaction")->where("user",$user)->where("type","3")->count();
	}
	//交易记录
	public function user_log($user,$time){
		return db("log_transaction")->alias("lt")->join("coin c","lt.coin=c.Id")->field("lt.type,lt.time,lt.money,c.name coin")->where("user",$user)->where("time","<",$time)->limit(20)->order("time","desc")->select();
	}
	//添加记录
	public function add($data){
		return db("log_transaction")->insert($data);
	}
}
?>