<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use app\sel\controller\Login_after;
use think\Config;
use app\sel\controller\Msg;
use app\index\controller\Ase;
class Security extends Controller{
	private $arr_str=["code"=>420,"msg"=>'',"data"=>[]];
	private function state_ok(){
		$this->arr_str["code"]=200;
		//正常
	}
	private function state_fwq(){
		$this->arr_str["code"]=500;
		//服务器错误
	}
	private function set_token($data){
		$this->arr_str["data"]["token"]=$data;
	}
	private function set_error($num){
		$this->arr_str["code"]=$num;
		$cuowu=config('errormsg.');
		$this->arr_str["msg"]=$cuowu["zn"][$num];
	}
	private function set_data($data){
		$this->state_ok();
		$this->arr_str["data"]=$data;
	}
	private function is_out($data){
		if($data){
			$this->state_ok();
		}else{
			$this->state_fwq();
		}
		$this->out();
		time_inspect();
	}
	private function out(){
		echo json_encode($this->arr_str);
		time_inspect();
	}
	//验证token
	private function pre(){
		//$token=Request()->header('token')?Request()->header('token'):input("token");
     	$token=Request()->header('token');
		if($token!=null){
			if($token){
				return true;
			}else{
				$this->set_error(421);
			}
		}
		$this->out();
		return false;
	}
	//设置昵称
	public function nick(){
		if(!$this->pre()){return;}
		$name=input("name");
		$ase=new Ase;
		$token=json_decode($ase->decrypt(Request()->header('token')),true);
		$id=$token['user_id'];
		if($name!=null&&$id!=null){
			$login=new Login_after;
			$names=$login->sel($id,"nick");
			if($name!=$names["nick"]){
				$flag=$login->security($id,"nick",$name);
				if($flag){
					$token=set_token($id);
					$this->set_data(["token"=>$token,"nick"=>$name]);
				}else{
					$this->state_fwq();
				}
			}else{
				$this->set_error(452);
			}
		}
		$this->out();
	}
	//设置邮箱或手机
	public function edit_pe(){
		if(!$this->pre()){return;}
		$ase=new Ase;
		$token=json_decode($ase->decrypt(Request()->header('token')),true);
		$user_id=$token['user_id'];
		$data=json_decode(cache($user_id.'pe_name'),true);
		$login=new Login_after;
		$user_pe=$login->sel($user_id,"phone,email,phone_head");
		$code=false;//验证码是否通过
		$second=false;//是否为第二次
		if($user_pe[$data["type"]]==null){
			//第一次设置
			if(ver_code($data["name"],input($data["type"]."_code"))){
				$code=true;
			}else{
				$this->set_error(431);
				$code=false;
			}
		}else{
			//修改
			if(input("phone_code")!=null&&input("email_code")!=null){
				if(ver_code($user_pe["phone"],input("phone_code"))&&ver_code($user_pe["email"],input("email_code"))){
					//两个验证码
					$second=true;
					$code=true;
				}else{
					$this->set_error(431);
					$code=false;
				}
			}else{
				if(input("phone_code")){
					$code=input("phone_code");
				}else if(input("email_code")){
					$code=input("email_code");
				}else{
					$this->set_error(431);
				}
				if($code){
					$data=json_decode(cache($user_id.'pe_name'),true);
					if(ver_code($data["name"],input($data["type"]."_code"))){
						$token=set_token($user_id);
						$this->set_data(["token"=>$token]);
					}else{
						$this->set_error(431);
					}
				}
				$code=false;
			}
		}
		if($code){
			if($data["type"]=="email"){
				$flag=$login->security($user_id,"email",$data["name"]);
				$flags=true;
			}else{
				$flag=$login->security($user_id,"phone",$data["name"]);
				$flags=$login->security($user_id,"phone_head",$data["phone_head"]);
			}
			if($second){
				$login->set_safe_time($user_id);
			}
			if($flag){
				$token=set_token($user_id);
				$this->set_data(["token"=>$token,$data["type"]=>hideStar($data["name"])]);
			}else{
				$this->state_fwq();
			}
		}
		$this->out();
	}
	//验证支付密码
	public function ver_pay_pwd(){
		if(!$this->pre()){return;}
		$paypwd=input("pay_pwd");
		$ase=new Ase;
		$token=json_decode($ase->decrypt(Request()->header('token')),true);
		$id=$token['user_id'];
		if($paypwd!=null&&$id!=null){
			$login=new Login_after;
			$user_pwd=$login->sel($id,"pay_password")["pay_password"];
			if(md5($paypwd)==$user_pwd){
				$this->state_ok();
				$token=set_token($id);
				$this->set_data(["token"=>$token]);
				user_inspect($id,true);
			}else{
				$this->set_error(467);
			}
		}
		$this->out();
	}
	//修改密码
	public function pwd(){
		$new=input("new");
		if(!$this->pre()){return;}
		if($new!=null){
			$login=new Login_after;
			$ase=new Ase;
			$token=json_decode($ase->decrypt(Request()->header('token')),true);
			$user_id=$token['user_id'];
			$user_pe=$login->sel($user_id,"phone,email");
			if(ver_code($user_pe["phone"],input("phone_code"))&&ver_code($user_pe["email"],input("email_code"))){
				$pwd=$login->sel($user_id,"pay_password");
				if($pwd!=md5($new)){
					$flag=$login->security($user_id,"password",md5($new));
					if($flag){
						$token=set_token($user_id);
						$this->set_data(["token"=>$token]);
					}else{
						$this->state_fwq();
					}
				}else{
					$this->set_error(452);
				}
			}else{
				$this->set_error(431);
			}
		}
		$this->out();
	}
	//设置或修改支付密码
	public function set_repay_pwd(){
		$new=input("new");
		if(!$this->pre()){return;}
		$ase=new Ase;
		$token=json_decode($ase->decrypt(Request()->header('token')),true);
		$user_id=$token['user_id'];
		if($new!=null&&$user_id!=null){
			$login=new Login_after;
			$pwd=$login->sel($user_id,"pay_password");
			//第一次或者修改都需要验证码
			// if($pwd!=null){
				$user_pe=$login->sel($user_id,"phone,email");
				if(ver_code($user_pe["phone"],input("phone_code"))&&ver_code($user_pe["email"],input("email_code"))){
					if($pwd!=md5($new)){
						$flag=$login->security($user_id,"pay_password",md5($new));
						if($flag){
							user_inspect($user_id,true);
							$token=set_token($user_id);
							$this->set_data(["token"=>$token]);
						}else{
							$this->state_fwq();
						}
					}else{
						$this->set_error(452);
					}
				}else{
					$this->set_error(431);
				}
			// }else{
				// $flag=$login->security($user_id,"pay_password",md5($new));
				// if($flag){
				// 	$token=set_token($user_id);
				// 	$this->set_data(["token"=>$token]);
				// }else{
				// 	$this->state_fwq();
				// }
			// }
		}
		$this->out();
	}
	//获取手机验证码  //第一次设置/修改/获取
	public function phone_code(){
		if(!$this->pre()){return;}
		$ase=new Ase;
		$token=json_decode($ase->decrypt(Request()->header('token')),true);
		$id=$token['user_id'];
		$login=new Login_after;
		$phone=$login->sel($id,"phone,phone_head");
		$phone_head=$phone["phone_head"];
		$phone=$phone["phone"];
		if($phone==null){
			//查看是否设置过手机，这里是第一次设置手机
			$phone=input("phone");
			$phone_head=input("phone_head");
			if($phone!=null&&$phone_head!=null){
				//手机格式
				if(mp_ver($phone,$phone_head)=='phone'){
					//判断手机是否存在
					if($login->is_sel("phone",$phone)){
						$this->set_error(435);
						$this->out();
						return;
					}else{
						//发送验证码
						if(phone_code($phone,$phone_head)){
							$token=set_token($id);
							$this->set_data(["token"=>$token]);
							cache($id.'pe_name',json_encode(["type"=>"phone","name"=>$phone,"phone_head"=>$phone_head]));
						}else{
							$this->set_error(436);
						}
					}
				}else{
					$this->set_error(450);
				}
			}
		}else{
			//设置过手机，判断是修改还是获取
			if(input("phone")!=null&&input("phone_head")!=null){
				//修改
				if(mp_ver(input("phone"),input("phone_head"))=='phone'){
					$phone=input("phone");
					$phone_head=input("phone_head");
					if($login->is_sel("phone",$phone)){
						$this->set_error(435);
						$this->out();
						return;
					}
					cache($id.'pe_name',json_encode(["type"=>"phone","name"=>$phone,"phone_head"=>$phone_head]));
				}
			}
			//获取
			if(phone_code($phone,$phone_head)){
				$token=set_token($id);
				$this->set_data(["token"=>$token]);
			}else{
				$this->set_error(436);
			}
		}
		$this->out();
	}
	//获取邮箱验证码		//第一次设置/修改/获取
	public function email_code(){
		if(!$this->pre()){return;}
		$ase=new Ase;
		$token=json_decode($ase->decrypt(Request()->header('token')),true);
		$id=$token['user_id'];
		$login=new Login_after;
		$email=$login->sel($id,"email")["email"];
		if($email==null){
			//第一次设置获取
			$email=input("email");
			if($email!=null&&mp_ver($email)=="email"){
				if($login->is_sel("email",$email)){
					$this->set_error(435);
					$this->out();
					return;
				}else{
					if(email_code($email)){
						$token=set_token($id);
						$this->set_data(["token"=>$token]);
						cache($id.'pe_name',json_encode(["type"=>"email","name"=>$email]));
					}else{
						$this->set_error(436);
					}
				}
			}else{
				$this->set_error(450);
			}
		}else{
			//设置过邮箱，判断是修改还是获取
			if(input("email")!=null){
				//修改
				if(mp_ver(input("email"))=='email'){
					$email=input("email");
					if($login->is_sel("email",$email)){
						$this->set_error(435);
						$this->out();
						return;
					}
					cache($id.'pe_name',json_encode(["type"=>"email","name"=>$email]));
				}
			}
			//获取
			if(email_code($email)){
				$token=set_token($id);
				$this->set_data(["token"=>$token]);
			}else{
				$this->set_error(436);
			}
		}
		$this->out();
	}
	//投标操作
	public function investment(){
		if(!$this->pre()){return;}
		$ase=new Ase;
		$token=json_decode($ase->decrypt(Request()->header('token')),true);
		$id=$token['user_id'];
		$bid=input("bid");
		$money=input("money");
		$paypwd=input("pay_pwd");
		if($bid!=null&&$money!=null&&$paypwd!=null&&$id!=null){
			$money=floor($money);
			$login=new Login_after;
			$bid_info=$login->bid_info($bid);
			$user=$login->sel($id,"state,EOS_account,phone,email,pay_password,inviter");
			$time=time();
			switch (1) {
				case 1:
					//判断是否开标，是否结标
					if($bid_info["start"]>$time||$bid_info["end_time"]<$time){
						$this->set_error(461);
						break;
					}
				case 2:
					//判断金额是否再范围
					if($bid_info["min_eos"]>$money||$bid_info["max_eos"]==-1?false:($bid_info["max_eos"]<$money)||$money==null){
						$this->set_error(462);
						break;
					}
				case 3:
					//标是否正常
					if($bid_info["state"]!="1"){
						$this->set_error(463);
						break;
					}
				case 4:
					//是否还能投那么多
					if(($bid_info["total"]-$money-$bid_info["reised"])<0){
						$this->set_data(["surplus"=>$bid_info["total"]-$bid_info["reised"]]);
						$this->set_error(464);
						break;
					}
				case 5:
					//用户是否有钱
					$user_money=$login->user_money($id);
					if($user_money[$bid_info["need"]]["money"]<$money){
						$this->set_error(465);
						break;
					}
				case 6:
					//用户状态是否能支付
					$user_state=$user["state"];
					if($user_state!=2&&$user_state!=3){
						$this->set_error(466);
						break;
					}
				case 7:
					//验证密码
					$user_pwd=$user["pay_password"];
					if(md5($paypwd)!=$user_pwd){
						$this->set_error(467);
						break;
					}
				case 8:
					//开始扣钱
					$user_money[$bid_info["need"]]["money"]=$user_money[$bid_info["need"]]["money"]-$money;
					if($login->user_use_money($id,$bid_info["need"],$money)){
						$login->up_add($id,$bid,$money);
						$token=set_token($id);
						$this->state_ok();
						$this->set_data(["token"=>$token]);
					}else{
						break;	
					}
			}
		}
		$this->out();
	}
	//账户中心
	public function center(){
		if(!$this->pre()){return;}
		$ase=new Ase;
		$token=json_decode($ase->decrypt(Request()->header('token')),true);
		$id=$token['user_id'];
		if($id!=null){
			$login=new Login_after;
			//累计收益
			$porportion=22.5;//比例
			$accumulated=$login->user_cumulative($id);
			$accumulated=$accumulated[0]["sum"]==null?0:num_point($accumulated[0]["sum"],4);
			$sum_money=$login->sum_money($id);
			$user_ranking=$login->user_ranking($id);//当前用户排名
			$fee=$login->fee();
			if($user_ranking!=null){
				$user_ranking=$user_ranking[0];
				if($user_ranking["no"]!=1){
					$ranking_user=$login->ranking_user($user_ranking["no"]-1)[0];//上个用户
				}else{
					$ranking_user=false;
				}
			}else{
				$user_ranking=false;
				$ranking_user=false;
			}
			$msg=new Msg;
		
			$account=$msg->EOS_account($id);
			//自动投标
			$auto_b=$login->automatic($id);
			$money=$msg->balance($id);
			foreach ($auto_b as $key => $value){
				$auto_b[$key]=["money"=>$money[$key]["money"],"state"=>$value];
			}
			$coin_list=$msg->coin_info();//货币最小币种
			foreach ($coin_list as $key => $value) {
				$auto_b[$value["name"]]["withdraw"]=(double)$value["min_withdraw"];
			}
			$eos_money=EOS_to_RMB();
			$data=["no"=>$user_ranking["no"],"difference"=>num_point($ranking_user["sum"]-$user_ranking["sum"],4),"accumulated"=>$accumulated,"sum_money"=>num_point($sum_money["sum_money"],4),"due"=>num_point((float)$sum_money["due"],4),"aoto"=>$auto_b,"porportion"=>$porportion,"content"=>["fee"=>$fee,"account"=>$account],'eos_money'=>$eos_money];
			$this->set_data($data);
		}
		$this->out();
	}
	//合作人
    public function cooperation(){
    	if(!$this->pre()){return;}
    	$ase=new Ase;
		$token=json_decode($ase->decrypt(Request()->header('token')),true);
		$id=$token['user_id'];
    	if($id!=null){
            $project=input("project");
            $url=input("url");
            $token_name=input('token');
            $time=time();
            $name=input("name");
            $phone=input("phone");
            $email=input("email");
	    	$location=input("location");
	    	if($project!=null&&$url!=null&&$token_name!=null&&$name!=null&&$phone!=null&&$email!=null&&$location!=null){
                $msg=new Msg;
                $data=["user"=>$id,"project_name"=>$project,"url"=>$url,"token"=>$token_name,"time"=>$time,"name"=>$name,"phone"=>$phone,"email"=>$email,"location"=>$location];
                if($msg->cooperation_add($data)){
                    $this->state_ok();
                    $token=set_token($id);
                    $this->set_data(["token"=>$token]);
                }

	    	}
    	}
    	$this->out();
    }
    //超级代理人申请
    public function super_apply_add(){
        if(!$this->pre()){return;}
        $ase=new Ase;
        $token=json_decode($ase->decrypt(Request()->header('token')),true);
        $id=$token['user_id'];
        if($id!=null){
            $name=input("name");
            $location=input("location");
            $phone=input('phone');
            $email=input('email');
            $time=time();
            if($location!=null&&$name!=null&&$phone!=null&&$email!=null){
                $msg=new Msg;
                $data=["user"=>$id,"location"=>$location,"time"=>$time,"name"=>$name,"phone"=>$phone,"email"=>$email];
                if($msg->super_apply_add($data)){
                    $this->state_ok();
                    $token=set_token($id);
                    $this->set_data(["token"=>$token]);
                }

            }
        }
        $this->out();
    }
    //提币
    public function reflect(){
		if(!$this->pre()){return;}
		$coin=input("coin");
		$sum=input("sum");
		$paypwd=input("pay_pwd");
		$email_code=input("email_code");
		$ase=new Ase;
		$token=json_decode($ase->decrypt(Request()->header('token')),true);
		$user_id=$token['user_id'];
		if($coin!=null&&$sum!=null&&$paypwd!=null&&$email_code!=null&&$user_id!=null){
			$login=new Login_after;
			$user_safe=$login->sel($user_id,"safe_time")["safe_time"];
			if($user_safe<time()){
				$user_email=$login->sel($user_id,"email")["email"];
				if(ver_code($user_email,$email_code)){
					$user_paypwd=$login->sel($user_id,"pay_password")["pay_password"];
					if($user_paypwd==md5($paypwd)){
						$login=new Login_after;
						if($login->refiect($user_id,$coin,$sum)){
							$this->state_ok();
							$token=set_token($user_id);
							$this->set_data(["token"=>$token]);
						}else{
							$this->set_error(465);
						}
					}else{
						$this->set_error(467);
					}
				}else{
					$this->set_error(431);
				}
			}else{
				$this->set_error(453);
			}
		}
		$this->out();
	}
	//自动投标设置
	public function set_auto(){
		if(!$this->pre()){return;}
		$name=input("name");
		$paypwd=input("pay_pwd");
		$ase=new Ase;
		$token=json_decode($ase->decrypt(Request()->header('token')),true);
		$user_id=$token['user_id'];
		if($name!=null&&$paypwd!=null&&$user_id!=null){
			$login=new Login_after;
			$user_pay=$login->sel($user_id,"pay_password");
			if(md5($paypwd)==$user_pay["pay_password"]){
				$data=[];
				foreach (json_decode($name) as $key => $value) {
					$data[$key]=$value->state;
				}
				// var_dump($data);
				if($login->set_automatic($user_id,$data)){
					$this->state_ok();
					$token=set_token($user_id);
					$this->set_data(["token"=>$token]);
				}else{
					$this->state_fwq();
				}
			}else{
				$this->set_error(467);
			}
			
		}
		$this->out();
	}
	//退出登陆
	public function sign_out(){
		if(!$this->pre()){return;}
		$ase=new Ase;
		$token=json_decode($ase->decrypt(Request()->header('token')),true);
		$id=$token['user_id'];
		if($id!=null){
			$login=new Login_after;
			$flag=$login->sign_out($id);
			if($flag){
				$this->state_ok();
			}
		}
		$this->out();
	}
	//邀请好友
	public function inviation(){
		if(!$this->pre()){return;}
		$ase=new Ase;
		$token=json_decode($ase->decrypt(Request()->header('token')),true);
		$id=$token['user_id'];
		if($id!=null){
			$img_url=config("url.")["ht"]."/static/public/img/invitation/";
			$msg=new Msg;
			$img_url.='img1.jpg';
			$inv_code=$msg->invitation_code($id);
			$inv_url=config("url.")["qt"]."/register?invite=";
			$user_inv=$msg->sel_user_inv($id);//邀请码
			$data=["img_url"=>$img_url,"url"=>$inv_url.$user_inv,"img"=>config("url.")["ht"]."/api/api/create?inv=".$user_inv];
			$this->set_data($data);
		}
		$this->out();
	}
    //个人邀请好友排行
    public function inviation_friend_ranking(){
    	if(!$this->pre()){return;}
    	$page=input("page")==null?1:input("page");
    	$limit=input("limit")==null?3:input("limit");
    	$ase=new Ase;
		$token=json_decode($ase->decrypt(Request()->header('token')),true);
		$id=$token['user_id'];
    	if($id!=null){
    		$msg=new Msg;
    		$login=new Login_after;
    		$friend=$msg->inviation_friend_ranking($id,$page,$limit);
    		$sum=$friend[1][0]["num"];
    		$friend=$friend[0];
    		$total=$msg->inv_personal_ranking($id);
    		$month=$msg->inv_personal_ranking($id,mktime(0,0,0,date('m'),1,date('Y')));
    		$is_super=$login->user_is_super($id);
    		$super_propor=0;
    		if ($is_super) {
    			$super_propor=db("super_agent")->where("user_id",$id)->find()['proportion'];
    		}
    		$proportion=$login->inv_earning();
    		if($total){
    			$total=$total[0];
    			$total['sum']=num_point($total['sum'],4);
    		}else{
    			$total=["no"=>"-","sum"=>0];
    		}
    		if($month){
    			$month=$month[0];
    			$month['sum']=num_point($month['sum'],4);
    		}else{
    			$month=["no"=>"-","sum"=>0];
    		}
    		$data=["firend"=>$friend,"total"=>$total,"month"=>$month,"is_super"=>$is_super,"proportion"=>$proportion,"max_page"=>ceil($sum/$limit),"page"=>$page,'super_propor'=>$super_propor];
    		$this->set_data($data);
    	}
    	$this->out();
    }
}
?>