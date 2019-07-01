<?php
namespace app\sel\controller;
use app\admin\model\Super_apply;
use think\Controller;
use app\sel\model\Img_carousel;
use app\sel\model\Bid_issuing;
use app\sel\model\Tender_record;
use app\sel\model\Periods;
use app\sel\model\Notice;
use app\sel\model\Notify;
use app\sel\model\Company;
use app\sel\model\Help;
use app\sel\model\Message;
use app\sel\model\Coin;
use app\sel\model\User;
use app\sel\model\Bonus;
use app\sel\model\Cooperation;
use app\sel\model\Log_transaction;
use app\sel\model\Log_real;
class Msg extends Controller{

	//420参数错误200正常,500服务器错误
	//基础信息
	private $arr_str=["code"=>420,"msg"=>'',"data"=>[]];


	private function state_fwq(){
		$this->arr_str["code"]=500;
	}
	private function state_ok(){
		$this->arr_str["code"]=200;
	}
	private function set_data($data){
		$this->state_ok();
		$this->arr_str["data"]=$data;
	}
	private function out(){
		return json_encode($this->arr_str);
	}
	//首页轮播图片
	public function wheel_img($number){
		$img=new Img_carousel;
		$data=$img->sel_num($number);
		if(is_array($data)){
			return $data;
		}else{
			return false;
		}
	}
	//交易金额
	public function money(){
		$tender=new Tender_record;
		$deal_eos=$tender->ok_money()[0]["a"];
	}
	//标列表
	public function credit_bid($page,$limit,$type){
		$bid=new Bid_issuing;
		// $data=$bid->sel($page,$limit,$type);
		$data=[];
		$data1=db("bid_issuing")->alias("b")->leftJoin("tender_record t","b.Id=t.bid_id")->leftJoin("coin c","b.need_coin_id=c.Id")->field("b.Id id,b.title,b.total,b.annual_profit,b.repayment_time repay,b.end_bid_time end_time,SUM(t.money) reised,c.name coin,b.state")->where("b.state",'1')->where("start_time",'<',time())->group("b.Id")->order('start_time','desc')->where('type','1')->select();
		$data2=db("bid_issuing")->alias("b")->leftJoin("tender_record t","b.Id=t.bid_id")->leftJoin("coin c","b.need_coin_id=c.Id")->field("b.Id id,b.title,b.total,b.annual_profit,b.repayment_time repay,b.end_bid_time end_time,SUM(t.money) reised,c.name coin,b.state")->where("b.state",'8')->where("start_time",'<',time())->group("b.Id")->order('start_time','desc')->where('type','1')->select();
		$data3=db("bid_issuing")->alias("b")->leftJoin("tender_record t","b.Id=t.bid_id")->leftJoin("coin c","b.need_coin_id=c.Id")->field("b.Id id,b.title,b.total,b.annual_profit,b.repayment_time repay,b.end_bid_time end_time,SUM(t.money) reised,c.name coin,b.state")->where("b.state",'7')->where("start_time",'<',time())->group("b.Id")->order('start_time','desc')->where('type','1')->select();
		$data4=db("bid_issuing")->alias("b")->leftJoin("tender_record t","b.Id=t.bid_id")->leftJoin("coin c","b.need_coin_id=c.Id")->field("b.Id id,b.title,b.total,b.annual_profit,b.repayment_time repay,b.end_bid_time end_time,SUM(t.money) reised,c.name coin,b.state")->where("b.state",'4')->where("start_time",'<',time())->group("b.Id")->order('start_time','desc')->where('type','1')->select();
		$data_list=array_merge($data1,$data2,$data3,$data4);
		for($i=0;$i<20;$i++){
			if(($page-1)*$limit+$i>=count($data_list)){
				break;
			}
			$data[]=$data_list[($page-1)*$limit+$i];
		}
		if(is_array($data)){
			$arr=[];
			foreach ($data as $key => $value){
				$state='';
				switch($value["state"]){
					case '1':
						$state='募集中';
						break;
					case '4':
						$state='已完成';
						break;
					case '5':
						$state='过期';
						break;
					case '6':
						$state='标已满';
						break;
					case '7':
						$state='认购完成';
						break;
					case '8':
						$state='还款中';
						break;
				}
				// $type='';
				// switch ($value["type"]) {
				// 	case '1':
				// 		$type='EOS表';
				// 		break;
				// 	case '2':
				// 		$type='活动表';
				// 		break;
				// }
				$arr[]=["id"=>$value["id"],"title"=>$value["title"],"total"=>num_point($value["total"],2),"day"=>ceil((int)$value["repay"]),"profit"=>$value["annual_profit"],"surplus"=>$value["total"]-$value["reised"],"need_coin"=>$value["coin"],"state"=>$state];
			}
			return $arr;
		}else{
			return false;
		}
	}
	//标总数
	public function sum_bid($type){
		$bid=new Bid_issuing;
		$data=$bid->sum($type);
		if(is_int($data)){
			return $data;
		}else{
			return false;
		}
	}
	//标详情
	public function bid_info($id){
		$bid=new Bid_issuing;
		$data=$bid->sel_id($id);
		if($data){
			$state='';
			switch($data["state"]){
				case '1':
					$state='募集中';
					break;
				case '4':
					$state='已完成';
					break;
				case '5':
					$state='过期';
					break;
				case '6':
					$state='标已满';
					break;
				case '7':
					$state='认购完成';
					break;
				case '8':
					$state='还款中';
					break;
			}
			$type='EOS';
			if($data["max_eos"]>$data["total"]-$data["reised"]){
				$data["max_eos"]=$data["total"]-$data["reised"];
			}
			$arr=["id"=>$data["id"],"total"=>(int)$data["total"],"day"=>(int)$data["repay"],"state"=>$state,"annual_profit"=>$data["annual_profit"].'%',"coin"=>$data["coin"],'compute'=>$data["compute"],"start"=>$data["start"],"title"=>$data["title"],"reised"=>$data["reised"],"surplus"=>$data["total"]-$data["reised"],"repay"=>$data["repay"],"end"=>$data["end_time"],"min_eos"=>$data["min_eos"],"max_eos"=>$data["max_eos"],"proportion"=>$data["annual_profit"]/360/100*$data["repay"]*$data["proportion"],"need"=>$data["need"],"purpose"=>$data["purpose"],'type'=>$type];
			return $arr;
		}else{
			return false;
		}
	}
	//标富文本内容
	public function bid_details($id){
		$bid=new Bid_issuing;
		$data=$bid->sel_details_id($id);
		if($data){
			return $data;
		}else{
			return false;
		}
		
	}
	//投标记录
	public function bid_recode($id,$page=1,$limit=20){
		$tender=new Tender_record;
		$data=$tender->tender_recode_id($id,$page,$limit);
		foreach ($data as $key => $value) {
			$data[$key]['name']=hideStar($data[$key]['name']);
		}
		if(is_array($data)){
			return $data;
		}else{
			return false;
		}
	}
	//标的利息记录
	public function bid_cycle($id){
		$periods=new Periods;
		$data=$periods->cycle_recode_id($id);
		if(is_array($data)){
			$arr=[];
			foreach ($data as $key => $value) {
				$state='';
				switch($value["state"]){
					case '1':
						$state='分发中';
						break;
					case '2':
						$state='已还款';
						break;
					case '3':
						$state='请等待';
						break;
				}
				$arr[]=["phase"=>$value["phase"],"time"=>$value["time"],"type"=>"利息","state"=>$state];
			}
			return $arr;
		}else{
			return false;
		}
		
	}
	//公告列表
	public function notice($page=1,$limit=20){
		$notice=new Notice;
		$data=$notice->sel($page,$limit);
		if(is_array($data)){
			return $data;
		}else{
			return false;
		}
	}
	//公告详情
	public function notice_info($id){
		$notice=new Notice;
		$data=$notice->info($id);
		if($data){
			$arr=["title"=>$data["title"],"abstract"=>$data["abstract"],"time"=>$data["time"],"type"=>$data["type"]==1?"网站公告":"系统公告","content"=>$data["content"]];
			return $arr;
		}else{
			return false;
		}
		
	}
	//通知
	public function notify($user_id,$page=1,$limit=20){
		$notify=new Notify;
		$data=$notify->sel($page,$limit);
		$num=$notify->unread_num($user_id);
		return [$data,$num];
	}
	//消息
	public function message($user_id,$page=1,$limit=20){
		$message=new Message;
		$data=$message->sel($user_id,$page,$limit);
		$num=$message->unread_num($user_id);
        return [$data,$num];
	}
	//关于我们
	public function about(){
		$company=new Company;
		$data=$company->sel();
		if(is_array($data)){
			return $data;
		}else{
			return false;
		}
	}
	public function help($type){
		$help=new Help;
		$data=$help->sel($type);
		if(is_array($data)){
			$arr=[];
			foreach ($data as $key => $value) {
				$arr[]=["title"=>$value["title"],"content"=>$value["content"]];
			}
			return $arr;
		}else{
			return false;
		}
	}
	public function type_con($type){
		$help=new Help;
		$data=$help->sel($type);
		if(is_array($data)){
			$arr=[];
			foreach ($data as $key => $value) {
				$arr=[$value["content"]];
			}
			return $arr;
		}else{
			return false;
		}
	}
	//帮助中心QQ和电话
	public function information(){
		$company=new Company;
		$data=$company->sel();
		if($data){
			return ["qq"=>$data["qq"],"phone"=>$data["phone"]];
		}else{
			return false;
		}
	}
	//成交金额
	public function trading_amount(){
		$bid=new Tender_record;
		$money=$bid->all_money();
		if($money){
			return null_0($money[0]["sum"]);
		}else{
			return false;
		}
	}
	//创造收益
	public function areate_amount(){
		$periods=new Periods;
		$money=$periods->ok_money();
		if($money){
			return null_0($money[0]["sum"]);
		}else{
			return false;
		}
	}
	//已还本金
	public function repayment(){
		$bid=new Tender_record;
		$money=$bid->repay_money();
		if($money){
			return null_0($money[0]["sum"]);
		}else{
			return false;
		}
	}
	//首页轮播消息，某某人投资了什么
	public function investment_msg(){
		$tender=new Tender_record;
		$data=$tender->sel_newdata();
		$arr=[];
		foreach ($data as $key => $value) {
			$arr[]=hideStar($value["nick"])."投资了".$value["title"];
		}
		return $arr;
	}
	//获取用户邀请码
	public function invitation_code($id){
		$user=new User;
		return $user->sel($id,"invitation")["invitation"];
	}
	//获取用户的余额
	public function balance($user_id){
		$user=new User;
		$money=$user->sel($user_id,"money")["money"];
		if($money==null){
			$money=[];
		}else{
			$money=json_decode($money,true);
		}
		$coin=new Coin;
		$coin_list=$coin->sel("name,smallest_unit");
		if($money==[]){
			for($i=0;$i<count($coin_list);$i++) {
				$money[$coin_list[$i]["name"]]=["money"=>0,"frozen"=>0];
			}
		}else{
			$money1 = array_keys($money);
			 foreach ($coin_list as $key => $value) {
				if(!in_array($value['name'],$money1)){
					$money[$value["name"]]=["money"=>0,"frozen"=>0];	
				}else{
                    $money[$value["name"]]=["money"=>num_point($money[$value["name"]]["money"],$value['smallest_unit']),"frozen"=>num_point($money[$value["name"]]["frozen"],$value['smallest_unit'])];
                }
			 }
		}
		$user->money_def($user_id,json_encode($money));
		$small=$coin->coin_list();
		foreach ($small as $key => $value) {
			$money[$value["name"]]["small"]=$value["if_small_amount"]==0?false:true;
		}
		return $money;
	}
	//利息排行
	public function interest_ranking($page,$limit){
		$lt=new Log_transaction;
		$data=$lt->int_ranking($page,$limit);
		foreach ($data as $key => $value) {
			$data[$key]['nick']=hideStar($data[$key]['nick']);
		}
		return $data;
	}
	//邀请排行
	public function invitation_ranking($page,$limit){
		$lt=new Log_transaction;
		$data=$lt->inv_ranking($page,$limit);
		foreach ($data as $key => $value) {
			$data[$key]['nick']=hideStar($data[$key]['nick']);
		}
		return $data;
	}
	//邀请图片
	public function invitation_img(){
		$company=new Company;
		return $company->sel()["inviation"];
	}
	//查询用户的邀请码
	public function sel_user_inv($id){
		$user=new User;
		return $user->sel($id,"invitation")["invitation"];
	}
	//添加一个企业入住
	public function cooperation_add($data){
		$coop=new Cooperation;
		return $coop->add($data);
	}
	//新增超级代理人
    public function super_apply_add($data){
	    $super_apply=new Super_apply();
	    return $super_apply->add($data);
    }
	//用户投标记录
	public function bid_log($user,$page=1,$limit=20){
		$tender=new Tender_record;
		$data=$tender->user_log($user,$page,$limit);
		foreach ($data as $key => $value) {
			switch ($value["state"]) {
				case '1':
					$data[$key]["state"]="投资中";
					break;
				case '2':
					$data[$key]["state"]="退款中";
					break;
				case '3':
					$data[$key]["state"]="已还本金";
					break;
			}
			$data[$key]['end']=$value['up1']+24*60*60*$value['end'];
			$data1=db("bid_issuing")->alias("b")->leftJoin("tender_record t","b.Id=t.bid_id")->leftJoin("coin c","b.need_coin_id=c.Id")->field("b.Id id,b.title,b.total,b.annual_profit,b.repayment_time repay,b.end_bid_time end_time,SUM(t.money) reised,c.name coin,b.state")->where('b.Id',$value['bid'])->find();
			$state='';
			switch($data1["state"]){
				case '1':
					$state='募集中';
					break;
				case '4':
					$state='已完成';
					break;
				case '5':
					$state='过期';
					break;
				case '6':
					$state='标已满';
					break;
				case '7':
					$state='认购完成';
					break;
				case '8':
					$state='还款中';
					break;
			}
			$data[$key]['info']=["id"=>$data1["id"],"title"=>$data1["title"],"total"=>$data1["total"],"day"=>ceil((int)$data1["repay"]),"profit"=>$data1["annual_profit"],"surplus"=>$data1["total"]-$data1["reised"],"need_coin"=>$data1["coin"],"state"=>$state,'type'=>'EOS标'];
			
		}
		return $data;
	}
	//用户投标数量及总数
	public function bid_cum($user_id){
		$tender=new Tender_record;
		$sum=$tender->user_sum_num($user_id);
		$money=$tender->user_sum_money($user_id);
		return ["num"=>$sum,"money"=>null_0($money[0]["sum"])];
	}
	//用户代收本金数量及总数
    public function bid_coll($user_id){
    	$tender=new Tender_record;
    	$sum=$tender->collected_num($user_id);
		$money=$tender->due_money($user_id);
		return ["num"=>$sum,"money"=>null_0($money[0]["sum"])];
    }
    //用户发放利息数量及总数
    public function bid_dis($user_id){
    	$lt=new Log_transaction;
    	$sum=$lt->user_sum_num($user_id);
		$money=$lt->user_profit_all($user_id);
		return ["num"=>$sum,"money"=>null_0($money[0]["sum"])];
    }
    //待收本金数量及总数
    public function bid_coll_record($user_id){
    	$tender=new Tender_record;
    	$bid=new Bid_issuing;
    	$sum=$tender->user_coll_num($user_id);
		$money=$tender->user_coll_money($user_id);
		return ["num"=>$sum,"money"=>num_point(null_0($money),2)];
    }
    //用户投标例时记录
    public function his_record($user_id,$time){
    	$lt=new Log_transaction;
    	$lr=new Log_real;
    	$company=new Company;
    	$user=new User;
    	$com_account=$company->sel()["account"];
    	$user_account=$user->sel($user_id,"EOS_account")["EOS_account"];
    	$lt_data=$lt->user_log($user_id,$time);
    	$lr_data=$lr->user_log($user_id,$time);
    	foreach ($lt_data as $key => $value) {
    		switch ($lt_data[$key]["type"]) {
    			case '1':
    				$lt_data[$key]["type"]="投标";
    				$lt_data[$key]["symbol"]=false;
    				break;
    			case '2':
    				$lt_data[$key]["type"]="回本";
    				$lt_data[$key]["symbol"]=true;
    				break;
    			case '3':
    				$lt_data[$key]["type"]="分利";
    				$lt_data[$key]["symbol"]=true;
    				break;
    			case '4':
    				$lt_data[$key]["type"]="邀请";
    				$lt_data[$key]["symbol"]=true;
    				break;
    		}
    	}
    	foreach ($lr_data as $key => $value) {
    		switch ($lr_data[$key]["type"]) {
    			case '1':
    				$lr_data[$key]["type"]="充值";
    				$lr_data[$key]["account"]=$com_account;
    				$lr_data[$key]["symbol"]=true;
    				break;
    			case '2':
    				$lr_data[$key]["type"]="提现";
    				$lr_data[$key]["account"]=$user_account;
    				$lr_data[$key]["symbol"]=false;
    				break;
    		}
    		switch ($lr_data[$key]["state"]) {
    			case '1':
    				$lr_data[$key]["state"]="处理中";
    				break;
    			case '2':
    				$lr_data[$key]["state"]="已完成";
    				break;
                case '3':
    				$lr_data[$key]["state"]="作废";
    				break;
    		}
    	}
    	$lt_num=0;
    	$lr_num=0;
    	$data=[];
    	if($lt_data==null){
    		for ($i=0; $i < 20; $i++) { 
    			if($lr_num==count($lr_data)){
    				break;
    			}
    			$data[]=["type"=>$lr_data[$lr_num]["type"],"time"=>$lr_data[$lr_num]["time"],"state"=>$lr_data[$lr_num]["state"],"coin"=>$lr_data[$lr_num]["coin"],"money"=>num_point($lr_data[$lr_num]["number"],4),"txid"=>$lr_data[$lr_num]["txid"],"fee"=>$lr_data[$lr_num]["fee"],"address"=>$lr_data[$lr_num]["address"],"symbol"=>$lr_data[$lr_num]["symbol"]];
    			$lr_num++;
    		}
    	}else if($lr_data==null){
    		for ($i=0; $i < 20; $i++) { 
    			if($lt_num==count($lt_data)){
    				break;
    			}
    			$data[]=["type"=>$lt_data[$lt_num]["type"],"time"=>$lt_data[$lt_num]["time"],"state"=>"已完成","coin"=>$lt_data[$lt_num]["coin"],"money"=>num_point($lt_data[$lt_num]["money"],4),"symbol"=>$lt_data[$lt_num]["symbol"]];
    			$lt_num++;
    		}
    	}else{
	    	for($i=0;$i<20;$i++){
	    		if($lt_data[$lt_num]["time"]>$lr_data[$lr_num]["time"]){
	    			$data[]=["type"=>$lt_data[$lt_num]["type"],"time"=>$lt_data[$lt_num]["time"],"state"=>"已完成","coin"=>$lt_data[$lt_num]["coin"],"money"=>num_point($lt_data[$lt_num]["money"],4),"symbol"=>$lt_data[$lt_num]["symbol"]];
	    			if($lt_num+1<count($lt_data)){
	    				$lt_num++;
	    			}else{
	    				for($j=$lr_num;$j<20,$j<count($lr_data);$j++){
	    					$data[]=["type"=>$lr_data[$j]["type"],"time"=>$lr_data[$j]["time"],"state"=>$lr_data[$j]["state"],"coin"=>$lr_data[$j]["coin"],"money"=>num_point($lr_data[$j]["number"],4),"txid"=>$lr_data[$j]["txid"],"fee"=>$lr_data[$j]["fee"],"address"=>$lr_data[$j]["address"],"symbol"=>$lr_data[$j]["symbol"]];
	    				}
	    				break;
	    			}
	    		}else{
	    			$data[]=["type"=>$lr_data[$lr_num]["type"],"time"=>$lr_data[$lr_num]["time"],"state"=>$lr_data[$lr_num]["state"],"coin"=>$lr_data[$lr_num]["coin"],"money"=>num_point($lr_data[$lr_num]["number"],4),"txid"=>$lr_data[$lr_num]["txid"],"fee"=>$lr_data[$lr_num]["fee"],"address"=>$lr_data[$lr_num]["address"],"symbol"=>$lr_data[$lr_num]["symbol"]];
	    			if($lr_num+1<count($lr_data)){
	    				$lr_num++;
	    			}else{
	    				for($j=$lt_num;$j<20,$j<count($lt_data);$j++){
	    					$data[]=["type"=>$lt_data[$j]["type"],"time"=>$lt_data[$j]["time"],"state"=>"已完成","coin"=>$lt_data[$j]["coin"],"money"=>num_point($lt_data[$j]["money"],4),"symbol"=>$lt_data[$j]["symbol"]];
	    				}
	    				break;
	    			}
	    		}
	    	}
    	}
    	return $data;
    }
    //获取公司账户
    public function recharge_info(){
    	$company=new Company;
    	return $company->sel();
    }
    //获取所有货币充值地址及最小金额
    public function coin_address(){
    	$coin=new Coin;
    	return $coin->sel("name,min_recharge,img");
    }
    //用户的tag值
    public function user_tag($user_id){
    	$user=new User;
    	return $user->sel($user_id,"tag")["tag"];
    }
    //朋友邀请排行
    public function inviation_friend_ranking($user_id,$page,$limit){
    	$bonus=new Bonus;
    	$user=new User;
    	$inv_list=$user->inv_list_id($user_id);
    	foreach ($inv_list as $key => $value) {
    		$inv_list[]=$value["nick"];
    		unset($inv_list[$key]);
    	}
    	$all=$bonus->friend_ranking_all($user_id,$page,$limit);
    	foreach ($all as $key => $value){
			$all[$key]["content"]=$bonus->friend_ranking_single($value["id"],0);
			$all[$key]['sum']=num_point($all[$key]['sum'],4);
    		unset($all[$key]["id"]);
    		if(in_array($value["nick"], $inv_list)){
    			unset($inv_list[array_search($value["nick"],$inv_list)]);
    		}
			foreach ($all[$key]["content"] as $keys => $values) {
				$all[$key]["content"][$keys]['sum']=num_point($all[$key]["content"][$keys]['sum'],4);
				$all[$key]["content"][$keys]['nick']='';
			}
		}
		foreach ($inv_list as $key => $value) {
			$all[]=['nick'=>$value,'sum'=>0,'content'=>[]];
		}
    	$sum=$bonus->friend_ranking_sum($user_id);
		return [$all,$sum];
    }
    //用户好友收益表
    public function inv_personal_ranking($user,$time=0){
    	$bonus=new Bonus;
    	return $bonus->personal_ranking($user,$time);
    }
    //查看用户的eos地址
    public function EOS_account($id){
    	$user= new User;
    	return $user->sel($id,"EOS_account account")["account"];
    }
    public function coin_info(){
    	$coin=new Coin;
    	return $coin->coin_list();
    }
}
?>