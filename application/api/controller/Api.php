<?php
namespace app\api\controller;
use think\Controller;
use app\sel\controller\Msg;
use app\index\controller\Ase;
class Api extends Controller{
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
	private function is_out($data){
		if($data){
			$this->set_data($data);
		}else{
			$this->state_fwq();
		}
		echo json_encode($this->arr_str);
		time_inspect();
		// eospark();
	}
	private function out(){
		echo json_encode($this->arr_str);
		time_inspect();
		// eospark();
	}
	public function api(){
		return $this->fetch();
	}
	public function text(){
		var_dump(input("id"));
	}
	//首页内容
	public function index(){
		$msg=new Msg;
		$img=$msg->wheel_img(6);
		foreach ($img as $key => $value) {
			$img[$key]["image"]=config("url.")["ht"]."/static/public/img/wheel/".$value["image"];
		}
		$trading_amount=$msg->trading_amount();
		$areate_amount=$msg->areate_amount();
		$repayment=$msg->repayment();
		$investment_msg=$msg->investment_msg();
		$money=EOS_to_RMB();
		$data=["trading_amount"=>["EOS"=>$trading_amount,"money"=>num_point($money*$trading_amount,2)],"areate_amount"=>["EOS"=>$areate_amount,"money"=>num_point($money*$areate_amount,2)],"repayment"=>["EOS"=>$repayment,"money"=>num_point($money*$repayment,2)],"img"=>$img,"investment_msg"=>$investment_msg];
		$this->is_out($data);
	}
	//关于我们
	public function about(){
		$msg=new Msg;
		$data=$msg->about();
		$this->is_out($data["about"]);
	}
	//标列表
	public function bid_list(){
		$msg=new Msg;
		$page=input("page")?input("page"):1;
		$limit=input('limit')?input("limit"):20;
		$type=input("type");
		if($type==null){
			$one=$msg->credit_bid($page,$limit,'1');
			$one_sum=$msg->sum_bid("1")/$limit;
			// $two=$msg->credit_bid($page,$limit,'2');
			// $two_sum=$msg->sum_bid("2")/$limit;
			if(is_array($one)){
			// $data=[["type"=>"1","data"=>$one,"title"=>"EOS标","page"=>$page,"max_page"=>ceil($one_sum)],["type"=>"2","data"=>$two,"title"=>"活动标","page"=>(int)$page,"max_page"=>ceil($two_sum)]];
			$data=[["type"=>"1","data"=>$one,"title"=>"EOS标","page"=>$page,"max_page"=>ceil($one_sum)]];
			}else{
				$data=false;
			}
		}else{
			switch ($type) {
				case '1':
					$title="信用表";
					break;
				case '2':
					$title="活动标";
					break;
				default:
					$this->out();
					return;
			}
			$sum=$msg->sum_bid($type);
			$datas=$msg->credit_bid($page,$limit,$type);
			$data=["type"=>$type,"data"=>$datas,"title"=>$title,"page"=>(int)$page,"max_page"=>ceil($sum/$limit)];
		}
		$this->is_out($data);
	}
	//标详情
	public function bid_info(){
		$id=input("id");
		if($id==null){
			$this->out();
		}else{
			$msg=new Msg;
			$info=$msg->bid_info($id);
			$details=$msg->bid_details($id);
			$recode=$msg->bid_recode($id,1,20);
			$cycle=$msg->bid_cycle($id,1,20);
			if($info){
				$data=["info"=>$info,"details"=>$details,"recode"=>$recode,"cycle"=>$cycle];
			}
			$this->is_out($data);
		}
	}
	//帮助信息
	public function help(){
		$msg=new Msg;
		$type1=$msg->help(1);
		$type2=$msg->help(2);
		$type3=$msg->type_con(3);
		$type4=$msg->type_con(4);
		$type5=$msg->type_con(5);
		$type6=$msg->help(6);
		$information=$msg->information();
		if($type1||$type2||$information||$type3||$type4||$type5||$type6){
			$data=["information"=>$information,"reg"=>$type1,"charge"=>$type2,'user'=>$type3,'law'=>$type4,'privacy'=>$type5,'security'=>$type6];
		}else{
			$data=false;
		}
		$this->is_out($data);
	}
	//用户余额
	public function per_order(){
		if(Request()->header('token')!=null){
			$ase=new Ase;
			$token=json_decode($ase->decrypt(Request()->header('token')),true);
			$id=$token['user_id'];
			$msg=new Msg;
			$data=$msg->balance($id);
			foreach ($data as $key => $value) {
				$data[$key]['money']=num_point($data[$key]['money'],4);
				$data[$key]['frozen']=num_point($data[$key]['frozen'],4);
			}
			$this->set_data($data);
		}
		$this->out();
	}
	//收益排行榜
	public function ranking(){
		$img_url=config("url.")["ht"]."/static/public/img/invitation/";
		$msg=new Msg;
		$img_url.=$msg->invitation_img();
		$interest_ranking=$msg->interest_ranking(1,20);//利息收益
		$inv_ranking=$msg->invitation_ranking(1,20);//邀请人收益
		foreach ($interest_ranking as $key => $value) {
			$interest_ranking[$key]['sum']=num_point($value['sum'],4);
		}
		foreach ($inv_ranking as $key => $value) {
			$inv_ranking[$key]['sum']=num_point($value['sum'],4);
		}
		$data=["int"=>$interest_ranking,"inv"=>$inv_ranking,'img_url'=>$img_url];
		$this->set_data($data);
		$this->out();
	}
	//输出图片
	public function create(){
        // // 自定义二维码配置
        
        header('Content-Type: image/png');
        $config = [
            'title'         => true,
            'title_content' => '快来加入把',
            'logo'          => true,
            'logo_url'      => './static/public/img/logo.png',
            'logo_size'     => 80,
        ];

        // 直接输出
        $qr_url = config("url.")["qt"].'/register?invite='.input("inv");
        // var_dump($qr_url);
        $qr_code = new QrcodeServer($config);
        $qr_img = $qr_code->createServer($qr_url);
        ob_clean();
        echo $qr_img;

        // 写入文件
        // $qr_url = '这是个测试二维码';
        // $file_name = './static/qrcode';  // 定义保存目录

        // $config['file_name'] = $file_name;
        // $config['generate']  = 'writefile';
        // // header('Content-Type: '.$qrCode->getContentType());
        // $qr_code = new QrcodeServer($config);
        // $rs = $qr_code->createServer($qr_url);
        // print_r($rs);

        exit;
    }
	public function charge_img(){
		$id=session('index_user');
		$coin=input('coin')!=null?input('coin'):'EOS';
		if($id!=null){
			$msg=new Msg();
			$tag=$msg->user_tag($id);
		}else{
			$tag='';
		}
		header('Content-Type: image/png');
		$config = [
			'logo'          => true,
			'logo_url'      => './static/public/img/logo.png',
			'logo_size'     => 80,
		];
		$publisher=db('coin')->where('name',$coin)->find();
		if($publisher==null){
			return '错误';
		}else{
			$publisher=$publisher['publisher'];
		}
		$account=db('company')->find(1)['account'];
		// 直接输出
		$qr_url=json_encode([
			'action'=>'transfer',
			'address'=>$account,
			'amount'=>'0',
			'blockchain'=>'EOS',
			'chainid'=>'aca376f206b8fc25a6ed44dbdc66547c36c6c33e3a119ffbeaef943642f0e906',
			'contract'=>$publisher,
			'precision'=>4,
			'protocol'=>'ScanProtocol',
			'symbol'=>$coin,
			'memo'=>$tag
		]);
		$qr_code = new QrcodeServer($config);
		$qr_img = $qr_code->createServer($qr_url);
		ob_clean();
		echo $qr_img;
		exit;
	}
	//邀请二维码
	public function agent_img(){
		$id=session('index_user');
		if($id!=null){
			$msg=new Msg();
			$tag=$msg->user_tag($id).'aaaa';
		}else{
			$tag='请登陆后再扫我';
		}
		header('Content-Type: image/png');
		$config = [
			'logo'          => true,
			'logo_url'      => './static/public/img/logo.png',
			'logo_size'     => 80,
		];
		$account=db('company')->find(1)['account'];
		// 直接输出
		$qr_url=json_encode([
			'action'=>'transfer',
			'address'=>$account,
			'amount'=>'0',
			'blockchain'=>'EOS',
			'chainid'=>'aca376f206b8fc25a6ed44dbdc66547c36c6c33e3a119ffbeaef943642f0e906',
			'contract'=>'eosio.token',
			'precision'=>4,
			'protocol'=>'ScanProtocol',
			'symbol'=>'EOS',
			'memo'=>$tag
		]);
		$qr_code = new QrcodeServer($config);
		$qr_img = $qr_code->createServer($qr_url);
		ob_clean();
		echo $qr_img;
		exit;
	}
    //投资记录(个人)
    public function investment_record(){
    	$page=input("page")==null?1:input("page");
    	$limit=input("limit")==null?20:input("limit");
    	$id=null;
    	if(Request()->header('token')!=null){
			$ase=new Ase;
			$token=json_decode($ase->decrypt(Request()->header('token')),true);
			$id=$token['user_id'];
		}
    	if($id!=null){
    		$msg=new Msg;
    		$bid_log=$msg->bid_log($id,$page,$limit);//记录
    		$cum=$msg->bid_cum($id);//累计投资
    		$coll=$msg->bid_coll($id);//待收本金
    		$dis=$msg->bid_dis($id);//发放利息
    		$coll_record=$msg->bid_coll_record($id);//待收利息
    		//！！！！！！！！！！！！！！！！！！！！！！！！现金比例
    		$data=["log"=>$bid_log,"cumulative"=>$cum,"collected"=>$coll,"distribute"=>$dis,"coll_record"=>$coll_record,"proportion"=>30];
    		$this->set_data($data);
    	}
    	$this->out();
    }
    //用户账户历史记录
    public function historical_record(){
    	$time=input("time")==null?9999999999:input("time");
    	$id=null;
    	if(Request()->header('token')!=null){
			$ase=new Ase;
			$token=json_decode($ase->decrypt(Request()->header('token')),true);
			$id=$token['user_id'];
		}
    	if($id!=null){
    		$msg=new Msg;
    		$data=$msg->his_record($id,$time);
    		$this->set_data($data);
    	}
    	$this->out();
    }
    //公司账户充值信息
    public function recharge(){
    	$id=null;
    	if(Request()->header('token')){
			$ase=new Ase;
			$token=json_decode($ase->decrypt(Request()->header('token')),true);
			$id=$token['user_id'];
		}
    	if($id!=null){
    		$msg=new Msg;
    		$info=$msg->recharge_info();
    		$tag=$msg->user_tag($id);
    		$img=$msg->coin_address();
    		//$url=config("url.")["ht"].'/static/public/img/QR_code/';
			$url=config("url.")["ht"].'/api/charge_img';
    		$arr=[];
    		foreach ($img as $key => $value) {
    			$arr[$value["name"]]=["min"=>$value["min_recharge"],"img"=>$url.'?coin='.$value["name"]];
    		}
    		$this->set_data(["account"=>$info["account"],"img"=>$arr,"tag"=>$tag]);
    	}
    	$this->out();
	}
	//绑定信息
	public function binding(){
		$id=null;
    	if(Request()->header('token')!=null){
			$ase=new Ase;
			$token=json_decode($ase->decrypt(Request()->header('token')),true);
			$id=$token['user_id'];
		}
    	if($id!=null){
    		$msg=new Msg;
    		$info=$msg->recharge_info();
    		$tag=$msg->user_tag($id);
    		$this->set_data(["account"=>$info["account"],"img"=>config("url.")["ht"].'/static/public/img/QR_code/'.$info["img"],"tag"=>$tag]);
    	}
    	$this->out();
	}
}
?>	