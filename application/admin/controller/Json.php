<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\User;
use app\admin\model\Log_login;
use app\admin\model\Administrator;
use app\admin\model\Coin;
use app\admin\model\Bid_issuing;
use app\admin\model\Tender_record;
use app\admin\model\Periods;
use app\admin\model\Super_agend;
use app\admin\model\Notice;
use app\admin\model\Message;
use app\admin\model\Notify;
use app\admin\model\Img_carousel;
use app\admin\model\Help;
use app\admin\model\Log_real;
use app\admin\model\Cooperation;
use app\admin\model\Super_apply;
class Json extends Controller{
	public function user(){
		$page=input("page");
		$limit=input("limit");
		$id=input("id");
		$name=input("name");
		$phone=input("phone");
		$email=input("email");
		$state=input("state");
		if($page===null||$limit==null){
			return;
		}
		$arr_str=["code"=>0,"msg"=>"","count"=>10,"data"=>[]];
		$user = new User;
		if($id==null&&$phone==null&&$email==null&&$name==null&&$state==null){
			$data=$user->json_sel($page,$limit);
			$num=$user->sum();
		}else{
			$content=[["EOS_account","like","%".$name."%"],["phone","like","%".$phone."%"],["email","like","%".$email."%"]];
			if($id!=null){
				$content[]=["Id","=",$id];
			}
			if($state!=null){
				$content[]=["state",'=',$state];
			}
			$data=$user->json_sel($page,$limit,$content);
			$num=$user->sum($content);
		}
		$arr_str["data"]=$data;
		$arr_str["count"]=$num;
		echo json_encode($arr_str);
	}
	public function user_msg_recode(){
		$id=input("id");
		if($id==null){
			return;
		}
		$arr_str=["flag"=>"false"];
		$message = new Message;
		$data=$message->sel_id($id);
		if($data){
			$arr_str["flag"]="true";
			$arr_str["data"]=$data;
			echo json_encode($arr_str);
		}else{
			$arr_str["msg"]="还没有给该用户发过消息";
			echo json_encode($arr_str);
			return;
		}
	}
	public function user_log(){
		$page=input("page");
		$limit=input("limit");
		$id=input("id");
		$name=input("name");
		$phone=input("phone");
		$email=input("email");
		$start=input("start");
		$end=input("end");
		if($page===null||$limit==null){
			return;
		}
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
		$log_login = new Log_login;
		if($id==null&&$phone==null&&$email==null&&$name==null&&$start==null&&$end==null){
			$data=$log_login->sel($page,$limit);
			$num=$log_login->sum();
		}else{
			$content=[["user.EOS_account","like","%".$name."%"],["user.phone","like","%".$phone."%"],["user.email","like","%".$email."%"]];
			if($id!=null){
				$content[]=["user.Id","=",$id];
			}
			if($start!=null){
				$content[]=["log.time",">",$start];
			}
			if($end!=null){
				$content[]=["log.time","<",$end];
			}
			$data=$log_login->sel($page,$limit,$content);
			$num=$log_login->sum($content);
		}
		if(is_array($data)){
			$arr_str["data"]=$data;
			$arr_str["count"]=$num;
			echo json_encode($arr_str);
		}else{
			$arr_str["msg"]="错误";
			echo json_encode($arr_str);
		}
	}
	public function admin_list(){
		$page=input("page");
		$limit=input("limit");
		$name=input("username");
		if($page===null||$limit==null){
			return;
		}
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
		$admin = new Administrator;
		if($name==null){
			$data=$admin->sel($page,$limit);
			$num=$admin->sum();
		}else{
			$content=[["user_name","like","%".$name."%"]];
			$data=$admin->sel($page,$limit,$content);
			$num=$admin->sum($content);
		}
		if($data!=[]){
			$arr_str["data"]=$data;
			$arr_str["count"]=$num;
			echo json_encode($arr_str);
		}else{
			$arr_str["msg"]="错误";
			echo json_encode($arr_str);
		}
	}
	public function inviter(){
		$page=input("page");
		$limit=input("limit");
		$name=input("name");
		if($page===null||$limit==null){
			return;
		}
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
		$user = new User;
		if($name==null){
			$data=$user->inviter_sel($page,$limit);
			$num=$user->inviter_sum();
		}else{
			$content=[["a.Id","like",$name]];
			$data=$user->inviter_sel($page,$limit,$content);
			$num=$user->inviter_sum();
		}
		$arr_str["data"]=$data;
		$arr_str["count"]=$num;
		echo json_encode($arr_str);
	}
	public function coin(){
		$page=input("page");
		$limit=input("limit");
		if($page===null||$limit==null){
			return;
		}
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
		$coin = new Coin;
		$data=$coin->sel($page,$limit);
		$num=$coin->sum();
		$arr_str["data"]=$data;
		$arr_str["count"]=$num;
		echo json_encode($arr_str);
	}
	public function bid(){
		$page=input("page");
		$limit=input("limit");
		$price_min=input("price_min");
		$price_max=input("price_max");
		$start=input("start");
		$end=input("end");
		$coin=input("coin");
		$state=input("state");
		if($page===null||$limit==null){
			return;
		}
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
		$bid=new Bid_issuing;
		if($price_min==null&&$price_max==null&&$start==null&&$end==null&&$coin==null&&$state==null){
			$data=$bid->sel_min($page,$limit);
			$num=$bid->sum();	
		}else{
			$content=[];
			if($price_min!=null){
				$content[]=["CAST(total AS decimal(16,8))",">",$price_min];
			}
			if($price_max!=null){
				$content[]=["CAST(total AS decimal(16,8))","<",$price_max];
			}
			if($start!=null){
				$content[]=["start_time",">",$start];
			}
			if($end!=null){
				$content[]=["end_time","<",$end];
			}
			if($coin!=null){
				$content[]=["need_coin_id","=",$coin];
			}
			if($state!=null){
				$content[]=["b.state","=",$state];
			}
			$data=$bid->sel_min($page,$limit,$content);
			$num=$bid->sum($content);	
		}
		$arr_str["data"]=$data;
		$arr_str["count"]=$num;
		echo json_encode($arr_str);
	}
	public function coin_list(){
		$coin=new Coin;
		$data=$coin->coin_list();
		echo json_encode($data);
	}
	public function bid_tender_record(){
		$page=input("page");
		$limit=input("limit");
		$username=input("username");
		$bid=input("bid");
		$start=input("start");
		$end=input("end");
		$state=input("state");
		if($page===null||$limit==null){
			return;
		}
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
		$bid_record=new Tender_record;
		if($username==null&&$bid==null&&$start==null&&$end==null&&$state==null){
			$data=$bid_record->sel($page,$limit);
			$num=$bid_record->sum();
		}else{
			$content=[["u.EOS_account","like","%".$username.""]];
			if($state!=null){
				$content[]=["t.state","=",$state];
			}
			if($bid!=null){
				$content[]=["bid_id","=",$bid];
			}
			if($start!=null){
				$content[]=["time",">",$start];
			}
			if($end!=null){
				$content[]=["time","<",$end];
			}
			$data=$bid_record->sel($page,$limit,$content);
			$num=$bid_record->sum($content);
		}
		$arr_str["data"]=$data;
		$arr_str["count"]=$num;
		echo json_encode($arr_str);
	}
	public function profit(){
		$page=input("page");
		$limit=input("limit");
		$bid=input("bid");
		$start=input("start");
		$end=input("end");
		$state=input("state");
		if($page===null||$limit==null){
			return;
		}
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
		$per=new Periods;
		if($bid==null&&$start==null&&$end==null&&$state==null){
			$data=$per->sel($page,$limit);
			$num=$per->sum();
		}else{
			$content=[];
			if($state!=null){
				$content[]=["per.state","=",$state];
			}
			if($bid!=null){
				$content[]=["per.bid","=",$bid];
			}
			if($start!=null){
				$content[]=["per.time",">",$start];
			}
			if($end!=null){
				$content[]=["per.time","<",$end];
			}
			$data=$per->sel($page,$limit,$content);
			$num=$per->sum($content);
		}
		
		$arr_str["data"]=$data;
		$arr_str["count"]=$num;
		echo json_encode($arr_str);
	}
	public function repay(){
		$page=input("page");
		$limit=input("limit");
		$start=input("start");
		$end=input("end");
		if($page===null||$limit==null){
			return;
		}
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
		$bid_re=new Bid_issuing;
		if($start==null&&$end==null){
			$data=$bid_re->repay_sel($page,$limit);
			$num=$bid_re->repay_sum();
		}else{
			$content=[];
			if($start!=null){
				$content[]=["b.repayment_time",">",$start];
			}
			if($end!=null){
				$content[]=["b.repayment_time","<",$end];
			}
			$data=$bid_re->repay_sel($page,$limit,$content);
			$num=$bid_re->repay_sum($content);
		}
		if($num!=0){
			$arr_str["data"]=$data;
		}
		$arr_str["count"]=$num;
		echo json_encode($arr_str);
	}
	public function super_user(){
		$page=input("page");
		$limit=input("limit");
		if($page===null||$limit==null){
			return;
		}
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
		$super=new Super_agend;
		$data=$super->sel($page,$limit);
		$num=$super->sum();
		$arr_str["data"]=$data;
		$arr_str["count"]=$num;
		echo json_encode($arr_str);
	}
	public function notice(){
		$page=input("page");
		$limit=input("limit");
		$id=input("id");
		$title=input("title");
		$abstract=input("abstract");
		$start=input("start");
		$end=input("end");
		$type=input("type");
		$state=input("state");
		if($page===null||$limit==null){
			return;
		}
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
		$notice=new Notice;
		if($id==null&&$title==null&&$start==null&&$end==null&&$type==null&&$state==null&&$abstract==null){
			$data=$notice->sel($page,$limit);
			$num=$notice->sum();
		}else{
			$content=[["title","like","%".$title."%"],["abstract","like","%".$abstract."%"]];
			if($id!=null){
				$content[]=["id","=",$id];
			}
			if($start!=null){
				$content[]=["time",">",$start];
			}
			if($end!=null){
				$content[]=["time","<",$end];
			}
			if($type!=null){
				$content[]=["type","=",$type];
			}
			if($state!=null){
				$content[]=["state","=",$state];
			}
			$data=$notice->sel($page,$limit,$content);
			$num=$notice->sum($content);
		}
		$arr_str["data"]=$data;
		$arr_str["count"]=$num;
		echo json_encode($arr_str);
	}
	public function notify(){
		$page=input("page");
		$limit=input("limit");
		$start=input("start");
		$end=input("end");
		if($page===null||$limit==null){
			return;
		}
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
		$notify=new Notify;
		if($start==null&&$end==null){
			$data=$notify->sel($page,$limit);
			$num=$notify->sum();
		}else{
			if($start!=null){
				$content[]=["time",">",$start];
			}
			if($end!=null){
				$content[]=["time","<",$end];
			}
			$data=$notify->sel($page,$limit,$content);
			$num=$notify->sum($content);
		}
		$arr_str["data"]=$data;
		$arr_str["count"]=$num;
		echo json_encode($arr_str);
	}
	public function wheel_img(){
		$img=new Img_carousel;
		$data=$img->sel();
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>$data];
		echo json_encode($arr_str);
	}
	public function help(){
		$page=input("page");
		$limit=input("limit");
		$type=input("type");
		if($page===null||$limit==null&&$type==null){
			return;
		}
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
		$help=new Help;
		$data=$help->sel($page,$limit,$type);
		$num=$help->sum($type);
		$arr_str["data"]=$data;
		$arr_str["count"]=$num;
		echo json_encode($arr_str);
	}
	public function help_info(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$help=new Help;
			$data=$help->info($id);
			if($data){
				$arr_str["data"]=$data;
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="服务器错误";
				echo json_encode($arr_str);
			}
		}
	}
	public function cash(){
		$page=input("page");
		$limit=input("limit");
		$type=input("type");
		if($page===null||$limit==null&&$type==null){
			return;
		}
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
		$real=new Log_real;
		$data=$real->sel($page,$limit,2);
		$num=$real->sum(2);
		$arr_str["data"]=$data;
		$arr_str["count"]=$num;
		echo json_encode($arr_str);
	}
	public function recharge(){
		$page=input("page");
		$limit=input("limit");
		$type=input("type");
		if($page===null||$limit==null&&$type==null){
			return;
		}
		eospark();
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
		$real=new Log_real;
		$data=$real->sel($page,$limit,1,2);
		$num=$real->sum(1);
		$arr_str["data"]=$data;
		$arr_str["count"]=$num;
		echo json_encode($arr_str);
	}
	public function binding(){
		$page=input("page");
		$limit=input("limit");
		$type=input("type");
		if($page===null||$limit==null&&$type==null){
			return;
		}
		eospark();
		$arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
		$real=new Log_real;
		$data=$real->sel($page,$limit,1,1);
		$num=$real->sum(1);
		$arr_str["data"]=$data;
		$arr_str["count"]=$num;
		echo json_encode($arr_str);
	}
	public function cooperation(){
        $page=input("page");
        $limit=input("limit");
        $arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
        $cooper=new Cooperation();
        $data=$cooper->getAll('*','','Id',[$page,$limit]);
        $arr_str['data']=$data[0];
        $arr_str['count']=$data[1];
        echo json_encode($arr_str);
    }
    public function super_apply(){
        $page=input("page");
        $limit=input("limit");
        $arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
        $super_apply=new Super_apply();
        $data=$super_apply->getAll('*','','Id',[$page,$limit]);
        $arr_str['data']=$data[0];
        $arr_str['count']=$data[1];
        echo json_encode($arr_str);
    }
}
?>