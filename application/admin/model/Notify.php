<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Notify extends Model{
	public function sel($page,$limit,$content=null){
		$data=db("notify")->field("Id id,time,content")->where("state","1");
		if($content==null){
			return $data->page($page,$limit)->select();
		}else{
			return $data->where($content)->page($page,$limit)->select();
		}
	}
	public function sum($content=null){
		if($content==null){
			return db("notify")->where("state","1")->count();
		}else{
			return db("notify")->where("state","1")->where($content)->count();
		}
	}
	public function add($value){
		$flag=db("notify")->insertGetId(["time"=>time(),"content"=>$value,"state"=>"1"]);
		if($flag){
            Db::query('insert into ef_notify_msg(user,no_id) select Id,'.$flag.' from ef_user;');
			return true;
		}else{
			return false;
		}
	}
	public function del($id){
		$flag=db("notify")->where("Id",$id)->update(["state"=>"0"]);
		if($flag){
			return true;
		}else{
			return false;
		}
	}
}
?>