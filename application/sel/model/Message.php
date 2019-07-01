<?php
namespace app\sel\model;
use think\Model;

class Message extends Model{
	public function sel($id,$page,$limit){
		return db("message")->where("user_id",$id)->order("time","desc")->page($page,$limit)->select();
	}
	public function unread_num($id){
	    return db('message')->where("user_id",$id)->where('state','0')->count();
    }
    public function lock($id){
	    $flag=db('message')->where('user_id',$id)->update(['state'=>'1']);
	    return $flag?true:false;
    }
}
?>