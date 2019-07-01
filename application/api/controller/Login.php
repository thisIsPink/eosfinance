<?php
namespace app\api\controller;
use app\sel\model\Log_login;
use think\Controller;
use app\sel\controller\Login_after;
use app\sel\controller\Msg;
use app\index\controller\Ase;
class Login extends Controller{
	//420参数错误,431验证码错误,432账号已存在,433账号不存在,434账户名或密码错误,451信息不对称,200正常,500服务器错误
	private $arr_str=["code"=>420,"msg"=>'',"data"=>[]];
	private function state_fwq(){
		$this->arr_str["code"]=500;
		//服务器错误
	}
	private function state_ok(){
		$this->arr_str["code"]=200;
		//正常
	}
	private function set_token($data){
		$this->arr_str["data"]["token"]=$data;
	}
	//提示错误
	private function set_error($num){
		$this->arr_str["code"]=$num;
		$cuowu=config('errormsg.');
		$this->arr_str["msg"]=$cuowu["zn"][$num];
	}
	//警告
	private function warning($num){
		$this->arr_str["code"]=$num;
	}
	private function set_data($data){
		$this->state_ok();
		$this->arr_str["data"]=$data;
	}
	private function is_out($data){
		if($data){
			$this->set_data($data);
		}else{
			$this->state_fwq();
		}
		echo json_encode($this->arr_str);
	}
	private function out(){
		echo json_encode($this->arr_str);
	}
	//获取手机或邮箱的验证码
	public function get_code(){
		$name=input("name");
		$phone_head=input("phone_head");
		$login=new Login_after;
		if(mp_ver($name)=="email"){
			//如果是邮箱
			if($login->is_user($name,"email")){
				$this->set_error(432);
			}else{
				$flag=email_code($name);
		        if(is_null($flag)){
		        	$this->set_error(436);
		        }else if($flag!=true){
		        	echo $flag;
		        }else{
		            $this->state_ok();
		        }
			}
		}else{
			if($name!==null&&$phone_head!=null){
				//如果是手机
				if(mp_ver($name,$phone_head)=="phone"){
					if($login->is_user($name,"phone")){
						$this->set_error(432);
					}else{
						$flag=phone_code($name,$phone_head);
						if($flag){
							$this->state_ok();
						}else if($flag==null){
							$this->set_error(436);
						}
					}
				}
			}
		}
		$this->out();
	}
	public function reg(){
		$name=input("name");
		$phone_head=input("phone_head");
		$pwd=input("password");
		$code=input("code");
		$inviter=input("inviter");
		$ver='/^(\w){6,16}$/';
		if(preg_match($ver,$pwd)!=0){
			//是否传参
			if($name!=null&&$pwd!=null&&$code!=null){
				//验证验证码
				if(ver_code($name,$code)){
					$login=new Login_after;
					if(mp_ver($name)=="email"){
						//格式邮箱正确
						$flag=$login->is_user($name,"email");
						if($flag){
							$this->set_error(432);
						}else{
							//flag可能为false
							if($flag==null){
								$flags=$login->reg($name,$pwd,"email");
								if($flags){
									if($inviter!=null||$inviter!=''&&$inviter!=$name){
										$login->add_inviter($flags,$inviter);
									}
									$this->state_ok();
								}elseif($flags==null){
									$this->state_fwq();
								}
								
							}
						}	
					}else if($name!=null&&$phone_head!=null){
						//手机格式验证
						if(mp_ver($name,$phone_head)=="phone"){
							$flag=$login->is_user($name,"phone");
							if($flag){
								$this->set_error(432);
							}else{
								//flag可能为false
								if($flag==null){
									$flags=$login->reg($name,$pwd,"phone",$phone_head);
									if($flags){
										if($inviter!=null||$inviter!=''){
											$login->add_inviter($flags,$inviter);
										}
										$this->state_ok();
									}elseif($flags==null){
										$this->state_fwq();
									}	
									
								}
							}
						}
					}
				}else{
					$this->set_error(431);
				}
			}
		}
		$this->out();
	}
	public function login(){
		$name=input("name");
		$pwd=input("password");
		$preg_email='/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims';
		$preg_phone='/^1[34578]{1}\d{9}$/';
		if(preg_match($preg_email,$name)==0){
			if(preg_match($preg_phone,$name)==0){
			}else{
				$type="phone";
			}
		}else{
			$type="email";
		}
		if(isset($type)){
			$login=new Login_after;
			$id=$login->login($name,$pwd,$type);
			if($id){
				$login->add_log_login($id);
				$nick=$login->sel($id,"nick,email,phone,pay_password,EOS_account account,safe_time");
				if($nick["safe_time"]<time()){
					$nick["safe_time"]=0;
				}
				$data=["user_info"=>["nick"=>$nick["nick"],"email"=>$nick["email"]?hideStar($nick["email"]):false,"phone"=>$nick["phone"]?hideStar($nick["phone"]):false,"pay_password"=>$nick["pay_password"]==null?false:true,"account"=>$nick["account"]?substr_replace($nick["account"], '****', 3, 4):false,"safe_time"=>(int)$nick["safe_time"]],"security"=>[]];
				session('index_user',$id);
				$this->set_data($data);
				$this->set_token(set_token($id));
			}else{
				$this->set_error(434);
			}
		}else{
			$this->set_error(434);
		}
		$this->out();
	}
	public function message(){
		$msg=new Msg;
		$notice=$msg->notice();
    	if(Request()->header('token')!=null){
			$ase=new Ase;
			$token=json_decode($ase->decrypt(Request()->header('token')),true);
			$id=$token['user_id'];
			$notify=$msg->notify($id);
			$message=$msg->message($id);
			$user_data=["notify"=>$notify[0],"message"=>$message[0],'notify_num'=>$notify[1],'message_num'=>$message[1]];
		}else{
			$user_data=false;
		}
		$data=["notice"=>$notice,"user_data"=>$user_data];
		$this->is_out($data);
	}
	public function lock_msg(){
	    $type=input('type');
        if(Request()->header('token')!=null&&($type=='message'||$type=='notify')){
            $ase=new Ase;
            $token=json_decode($ase->decrypt(Request()->header('token')),true);
            $id=$token['user_id'];
            $login=new Login_after();
            $this->set_data($login->lock_msg($id,$type));
        }
        $this->out();
    }
	public function notice_info(){
		$id=input("id");
		if($id!=null){
			$msg=new Msg;
			$data=$msg->notice_info($id);
			if($data){
				$this->set_data($data);
			}
		}
		$this->out();
	}
}
?>