<?php
namespace app\sel\controller;
use app\sel\model\Message;
use app\sel\model\Notify;
use think\Controller;
use app\sel\controller\Msg;
use app\sel\model\User;
use app\sel\model\Log_login;
use app\sel\model\Bid_issuing;
use app\sel\model\Super_agent;
use app\sel\model\Tender_record;
use app\sel\model\Coin;
use app\sel\model\Agent_income;
use app\sel\model\Bonus;
use app\sel\model\Log_transaction;
use app\sel\model\Log_real;
use app\sel\model\Periods;
class Login_after extends Controller{
	//判断用户某个东西是否绑定
	public function is_user($name,$type){
		if($type!="phone"||$type!="email"||$type!="EOS_account"){
			$user=new User;
			if($user->is_user($name,$type)){
				return true;
			}else{
				return null;
			}
		}else{
			return false;
		}
	}
	//注册
	public function reg($name,$pwd,$type,$phone_head=null){
		$user=new User;
		$data=["password"=>md5($pwd),"ip"=>ip(),"reg_time"=>time(),"up_login_time"=>time(),"state"=>"1"];
		if($type=="phone"){
			$data["phone"]=$name;
			$data["phone_head"]=$phone_head;
		}elseif ($type=="email") {
			$data["email"]=$name;
		}else{
			return false;
		}
		$data=$user->add($data);
		if($data){
			return $data;
		}else{
			return null;
		}
	}
	//登陆
	public function login($name,$pwd,$type){
		$user=new User;
		return $user->login($name,$pwd,$type);
	}
	//添加用户的邀请人
	public function add_inviter($id,$inviation){
		$user=new User;
		switch (1) {
			case '1':
				$to_id=$user->is_user($inviation,"invitation");
			case '2':
				if($to_id==null){
					$to_id=$user->is_user($inviation,"email");
				}
			case '3':
				if($to_id==null){
					$to_id=$user->is_user($inviation,"phone");
				}
		}
		if($to_id){
			$user->set_inviter($id,$to_id["id"]);
		}
		
	}
	//添加登陆日志
	public function add_log_login($user_id){
		$log=new Log_login;
		if($log->add($user_id)){
			return true;
		}else{
			return false;
		}
	}
	//查询用户单个信息
	public function sel($id,$str){
		$user=new User;
		return $user->sel($id,$str);
	}
	//查看用户某个字段是否有值
	public function is_sel($field,$value){
		$user=new User;
		return $user->is_sel($field,$value);
	}
	//用户邮箱或手机绑定及设置昵称
	public function security($user_id,$field,$value){
		$user=new User;
		return $user->edit_field($user_id,$field,$value);
	}
	//查看标详情
	public function bid_info($id){
		$bid=new Bid_issuing;
		return $bid->sel_id($id);
	}
	//查看用户余额
	public function user_money($id){
		$msg=new Msg;
		return $msg->balance($id);
	}
	//用户使用金额
	public function user_use_money($id,$coin,$use_money){
		$user=new User;
		$not_money=$this->user_money($id);
		$money=[];
		foreach ($not_money as $key => $value) {
			if($key==$coin){
				if($value["money"]-$use_money<0){
					return false;
				}
				$money[$key]=["money"=>$value["money"]-$use_money,"frozen"=>$value["frozen"]];
			}else{
				$money[$key]=["money"=>$value["money"],"frozen"=>$value["frozen"]];
			}
		}
		$user->set_money($id,json_encode($money));
		return true;
	}
	//用户投标
	public function user_up_bid($user_id){
		$tender=new Tender_record;
		return $tender->is_user($user_id);
	}
	//判断用户是否为超级邀请人
	public function user_is_super($user_id){
		$super=new Super_agent;
		return $super->is_user($user_id);
	}
	//投标动作
	public function up_add($user_id,$bids,$money){
		$tender=new Tender_record;
		$bid=new Bid_issuing;
		$lt=new Log_transaction;
		$periods=new Periods;
		$sel_bid=$bid->sel_id($bids);
		$coin=new Coin;
		$coin_list=$coin->sel("Id,name,smallest_unit");
		$coin_id='0';
     	$n=0;
		foreach ($coin_list as $key => $value) {
          	$n++;
			if($value["name"]==$sel_bid["need"]){
				$coin_id=$value["Id"];
				$small=$value["smallest_unit"];
			}
		}
		$data=["user_id"=>$user_id,"bid_id"=>$bids,"time"=>time(),"money"=>$money,"coin_id"=>$coin_id,"state"=>"1"];
		if($sel_bid["reised"]+$money==$sel_bid["total"]){
			$time=strtotime(date('Ymd',strtotime("+1 day")));
			$pdata=[];
          	$n=0;
            for($i=1; $i*30<=$sel_bid["repay"];$i++) { 
              	$n++;
            	$pdata[]=["phase"=>$i,"bid"=>$bids,"time"=>$time+30*$i*24*60*60,"money"=>num_point($sel_bid["total"]*30*($sel_bid["annual_profit"]/360/100)*$sel_bid["proportion"],$small),"day"=>'30',"state"=>"3"];
            };
            if($sel_bid["repay"]%30!=0){
            	$pdata[]=["phase"=>ceil($sel_bid["repay"]/30),"bid"=>$bids,"time"=>$time+$sel_bid["repay"]*24*60*60,"money"=>num_point($sel_bid["total"]*ceil($sel_bid["repay"]%30)*($sel_bid["annual_profit"]/360/100)*$sel_bid["proportion"],$small),"day"=>ceil($sel_bid["repay"]%30),"state"=>"3"];
            }
            foreach ($pdata as $key => $value) {
				$periods->add($value);
            }
			$bid->set_state($bids,"7");
         	db('bid_issuing')->where('Id',$bids)->update(['end_bid_time'=>$time]);
		}elseif($sel_bid["reised"]+$money>$sel_bid["total"]){
			return false;
		}
		$tender->add($data);
		$lt->add(["type"=>"1","user"=>$user_id,"remarks"=>json_encode(['action'=>'投标','bid'=>$bids]),"time"=>time(),"money"=>$money,"coin"=>$coin_id]);
      	if($sel_bid["reised"]+$money==$sel_bid["total"]){
            //超级受益人收益
            $this->super_inv($bids);
		}
	}
	//查看用户累计收益
	public function user_cumulative($user_id){
		$lt=new Log_transaction;
		return $lt->user_profit_all($user_id);
	}
	//用户账户余额与待收本金
	public function sum_money($user_id){
		$coin=new Coin;
		$coin_list=$coin->sel("Id,name,proportion");
		$user_money=$this->user_money($user_id);
		$sum_money=0;
		foreach ($user_money as $key => $value) {
			foreach ($coin_list as $keys => $values) {
				if($key==$values["name"]){
					$sum_money+=$value["money"]/$values["proportion"];
				}
			}
		}
		$tender=new Tender_record;
		$due=$tender->due_money($user_id)[0]["sum"];
		$due=$due==null?0:$due;
		return ["sum_money"=>$sum_money,"due"=>$due];
	}
	//用户当前收益排名
	public function user_ranking($user_id){
		$lt=new Log_transaction;
		return $lt->user_ranking($user_id);
	}
	//用户上级排名
	public function ranking_user($no){
		$lt=new Log_transaction;
		return $lt->user_ranking($no,true);
	}
	//自动投标查询
	public function automatic($id){
		$user=new User;
		$money=$user->sel($id,"auto_bidding")["auto_bidding"];
		if($money==null){
			$money=[];
		}else{
			$money=json_decode($money,true);
		}
		$coin=new Coin;
		$coin_list=$coin->sel("name");
		if($money==[]){
	        for($i=0;$i<count($coin_list);$i++) {
	            $money[$coin_list[$i]["name"]]=false;
	        }
	    }else{
	        $money1 = array_keys($money);
	         foreach ($coin_list as $key => $value) {
	            if(!in_array($value['name'],$money1)){
	                $money[$value["name"]]=false;   
	            }
	         }
	    }
		$user->auto_bid($id,json_encode($money));
		return $money;
	}
	//设置24小时的安全时间
	public function set_safe_time($id){
		$user=new User;
		return $user->set_safe($id,time()+24*60*60);
	}
	//自动投标设置
	public function set_automatic($id,$data){
		$user=new User;
		return $user->auto_bid($id,json_encode($data));
	}
	//退出登陆
	public function sign_out($user_id){
		$user=new User;
		return $user->out($user_id);
	}
	//提币
	public function refiect($user_id,$coin,$money){
		$user=new User;
		$fee=$this->fee();
		$user_money=balance($user_id);
		if($user_money[$coin]["money"]>$money){
			$user_money[$coin]["money"]=$user_money[$coin]["money"]-$money;
			$user_money[$coin]["frozen"]=$user_money[$coin]["frozen"]+$money;
			$user->set_money($user_id,json_encode($user_money));
			$user_account=$user->sel($user_id,"EOS_account")['EOS_account'];
			$data=["type"=>"2","user"=>$user_id,"time"=>time(),"address"=>$user_account,"number"=>$money,"coin"=>$this->coin_sel($coin),"state"=>"1","fee"=>$money*$fee];
			$real=new Log_real;
			return $real->add($data);
		}else{
			return false;
		}
	}
	//查看手续费
	public function fee(){
		$income=new Agent_income;
		return $income->sel()["fee"]/100;
	}
	//将货币id与name转换
	public function coin_sel($name,$id=false){
		$coin=new Coin;
		$coin_list=$coin->sel("Id,name");
		foreach ($coin_list as $key => $value) {
			if($id){
				if($value["Id"]==$name){
					return $value["name"];
				}
			}else{
				if($value["name"]==$name){
					return $value["Id"];
				}
			}
		}
	}
	//获取第一级邀请收益和第二次邀请收益
	public function inv_earning(){
		$income=new Agent_income;
		$data=$income->sel();
		return ["first"=>$data["first"],"second"=>$data["second"],"super"=>$data["super"]];
	}
	//查看消息
    public function lock_msg($user_id,$type){
	    switch ($type){
            case 'message':
                $msg=new Message();
                $msg->lock($user_id);
                break;
            case 'notify':
                $notify=new Notify();
                $notify->lock($user_id);
                break;
        }
        return true;
    }
	//超级受益人吃利息
	public function super_inv($bid){
	    $user_list=db('tender_record')->alias('t')->join('user u','t.user_id=u.Id')->join('super_agent sa','u.inviter=sa.user_id')->field('sa.user_id,u.Id u_id,sa.proportion s_pro,t.time,u.reg_time,sum(t.money) sum')->where('t.bid_id',$bid)->where('sa.state','1')->group('u.Id')->select();
	    $bid_info=db('bid_issuing')->alias('b')->join('coin c','b.interest_coin_id=c.Id')->field('b.proportion propor,annual_profit profit,repayment_time repay,c.smallest_unit unit,c.Id coin')->where('b.Id',$bid)->find();
	    foreach ($user_list as $key => $value){
	        if($value==null){
            	break;
            }
	        if($value["time"]-$value["reg_time"]<30*24*60*60){
	            $money=num_point($value["sum"]*$bid_info["propor"]*$bid_info["profit"]/100/360*$bid_info["repay"]*$value["s_pro"]/100,$bid_info["unit"]);
	            db("bonus")->insert(["user"=>$value["user_id"],"in_user"=>$value["u_id"],"cause"=>"3","money"=>$money,"bid"=>$bid,"time"=>time(),"coin"=>$bid_info["coin"]]);
	            $user_money=balance($value["user_id"]);
	            $user_money[coin_transformation($bid_info["coin"],true)]["money"]+=$money;
	            db("user")->where("Id",$value["user_id"])->update(["money"=>json_encode($user_money)]);
	            db("log_transaction")->insert(["type"=>"4","remarks"=>json_encode(['action'=>"标分利",'bid'=>$bid,'in_user'=>$value['u_id']]),"time"=>time(),"money"=>$money,"coin"=>$bid_info["coin"],"user"=>$value["user_id"]]);
	        }
	    }
	}
}
?>