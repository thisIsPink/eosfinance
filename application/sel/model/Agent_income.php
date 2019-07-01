<?php
namespace app\sel\model;
use think\Model;

class Agent_income extends Model{
	public function sel(){
		return db("agent_income")->find(1);
	}
}
?>