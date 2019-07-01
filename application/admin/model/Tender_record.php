<?php
namespace app\admin\model;

use think\Model;

class Tender_record extends Model{
	//查看投标列表
	public function sel($page,$limit,$content=null){
		$data=db("tender_record")->alias('t')->join("coin c",'t.coin_id=c.Id')->join('user u','t.user_id=u.Id')->field('t.Id id,u.EOS_account account,t.bid_id bid,t.time time,t.money money,c.name coin,t.state state')->order("Id","desc")->page($page,$limit);
		if($content==null){
			return $data->select();
		}else{
			return $data->where($content)->select();
		}
	}
	//求总数
	public function sum($content=null){
		if($content==null){
			return db("tender_record")->count();
		}else{
			return db("tender_record")->alias("t")->join('user u','t.user_id=u.Id')->where($content)->count();
		}
	}
}
?>