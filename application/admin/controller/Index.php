<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Bid_issuing;
use app\admin\model\Notice;
class Index extends Controller{
	public function login(){
		return $this->fetch();
	}
	public function index(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function welcome(){
		$data=[];
		$data["user_sum"]=db("user")->count();
		$data["user_hsum"]=db("user")->where("EOS_account","<>","null")->count();
		$data["user_invest"]=db("tender_record")->group("user_id")->count();
		$data["user_inviter"]=db("user")->where("inviter","<>","null")->count();
		$data["user_use"]=db("user")->where("state","2")->count();
		$data["user_dongjie"]=db("user")->where("state","3")->count();
		$data['bid_sum']=db('bid_issuing')->count();
		$data['bid_uping']=db('bid_issuing')->where('state','1')->count();
		$data['bid_comeing']=db('bid_issuing')->where('state','7')->count();
		$data['bid_ok']=db('bid_issuing')->where('state','4')->count();
		$data['bid_over']=db('bid_issuing')->where('state','5')->count();
		$guo_nei=request_post('https://dx.ipyy.net/sms.aspx',['action'=>'overage','account'=>'8T00319','password'=>'8T0031913']);
		$guo_wai=request_post('https://dx.ipyy.net/I18NSms.aspx',['action'=>'overage','account'=>'8T00338','password'=>'8T0033807']);
		$data['guo_nei']=json_decode(json_encode(simplexml_load_string($guo_nei)),true)['overage'];
		$data['guo_wai']=json_decode(json_encode(simplexml_load_string($guo_wai)),true)['balance'];
		$this->assign("data",$data);
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function member_list(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function member_login_log(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function admin_list(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function admin_add(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function company(){
		$data=db("company")->field("account,qq,phone,img")->where("Id","1")->find();
		$datas=db("agent_income")->find(1);
		$this->assign("data",$data);
		$this->assign("datas",$datas);
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function inviter(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function coin(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function coin_add(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function order_list(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function bid_edit(){
		$id=input("id");
		if($id!=null){
			$bid=new Bid_issuing;
			$data=$bid->sel_id($id);
			if($data){
				$this->assign("data",$data);
				if($this->is_login()){
					return $this->fetch();
				}
			}
		}
	}
	public function bid_add(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function bid_tender_record(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function profit(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function repay(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function super_user(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function super_user_add(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function notice(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function notice_add(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function notice_edit(){
		$id=input("id");
		if($id!=null){
			$notice=new Notice;
			$data=$notice->sel_id($id);
			if($data){
				$this->assign("data",$data);
				if($this->is_login()){
					return $this->fetch();
				}
			}
		}
	}
	public function notify(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function wheel_img(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function about(){
		$data=db("company")->field("about")->where("Id","1")->find();
		$this->assign("data",$data);
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function help_manage(){
		$type=input('type');
		if($type!=null&&$this->is_login()){
			return $this->fetch('help');
		}
	}
	public function help_con(){
		$type=input('type');
		if($type!=null&&$this->is_login()){
			$data=db('help')->where('Id',$type)->find()['content'];
			$this->assign('data',$data);
			return $this->fetch('help_con');
		}
	}
	public function help_info(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function binding(){
		if($this->is_login()){
			return $this->fetch();
		}
		eospark();
	}
	public function recharge(){
		if($this->is_login()){
			return $this->fetch();
		}
		eospark();
	}
	public function cash(){
		if($this->is_login()){
			return $this->fetch();
		}
	}
	public function cooperation(){
        if($this->is_login()){
            return $this->fetch();
        }
    }
    public function super_apply(){
        if($this->is_login()){
            return $this->fetch();
        }
    }
//判断用户是否登陆
	private function is_login(){
	    if(session('?admin')){
	        return true;
	    }else{
	        $this->success('请登陆', '/admin');
	    }
	}
}
?>