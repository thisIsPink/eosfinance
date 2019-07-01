<?php
namespace app\admin\model;
use think\Model;
class Log_real extends Model{
	public function sel($page,$limit,$type,$types=0){
		if($type==2){
			return db("log_real")->alias("lr")->join("coin c","c.Id=lr.coin")->join("user u","lr.user=u.Id")->field("lr.Id id,EOS_account user,lr.time,lr.number num,c.name coin,fee,lr.state")->where("type",$type)->where("lr.state","1")->page($page,$limit)->select();
		}else if($type==1){
			return db("log_real")->alias("lr")->join("coin c","c.Id=lr.coin")->join("user u","lr.user=u.Id")->field("lr.Id id,address account,lr.user user,lr.time,lr.number num,c.name coin,txid,(case when u.EOS_account is null then '1' else '2' end) types,lr.state")->where("type",$type)->group("lr.Id")->having("types=".$types)->where("lr.state","1")->page($page,$limit)->select();
		}
	}
	public function sum($type){
		return db("log_real")->where("type",$type)->where("state","1")->count();
	}
	public function state_ok($id,$value=null){
		if($value==null){
			return db("log_real")->where("Id",$id)->update(["state"=>"2"]);
		}else{
			return db("log_real")->where("Id",$id)->update(["state"=>"2","txid"=>$value]);
		}
	}
	public function state_no($id){
		return db("log_real")->where("Id",$id)->update(["state"=>"3"]);
	}
	public function remarks($id,$value){
		db("log_real")->where("Id",$id)->update(["remarks"=>$value]);
	}
	public function sel_id($id){
		return db("log_real")->where("Id",$id)->find();
	}
}
?>