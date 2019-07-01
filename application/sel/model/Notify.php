<?php
namespace app\sel\model;
use think\Model;

class Notify extends Model{
	public function sel($page,$limit){
		return db("notify")->field("time,content")->where("state",1)->order("time","desc")->page($page,$limit)->select();
	}
	public function unread_num($id){
	    return db('notify_msg')->where('user',$id)->count();
    }
    public function lock($id){
	    $flag=db('notify_msg')->where('user',$id)->delete();
	    return $flag?true:false;
    }
}
?>