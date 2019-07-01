<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\User;
use app\admin\model\Coin;
use app\admin\model\Administrator;
use app\admin\model\Bid_issuing;
use app\admin\model\Periods;
use app\admin\model\Tender_record;
use app\admin\model\Super_agend;
use app\admin\model\Notice;
use app\admin\model\Message;
use app\admin\model\Notify;
use app\admin\model\Img_carousel;
use app\admin\model\Help;
use app\admin\model\Log_real;
use app\admin\model\Super_apply;
class Sub extends Controller{
	//管理员登陆验证
	public function admin_out(){
		session("admin",null);
	}
	public function admin_ver(){
		$user=input("username");
		$pwd=input('password');
		$str_msg=["flag"=>'false'];
		if($user==null||$pwd==null){
			$str_msg["msg"]="请填写信息";
			echo json_encode($str_msg);
		}else{
			$administrator=new Administrator;
			$flag=$administrator->is_login($user,md5($pwd));
			if($flag){
				$str_msg["flag"]="true";
				session("admin",$user);
				echo json_encode($str_msg);
			}else{
				$str_msg["msg"]="账户或密码不正确";
				echo json_encode($str_msg);
			}
		}
	}
	//添加管理员
	public function admin_add(){
		$name=input("name");
		$pwd=input("password");
		$role=input("role");
		$arr_str=["flag"=>"false"];
		if($name==null||$pwd==null||$role==null){
			echo json_encode($arr_str);
		}else{
			$admin = new Administrator;
			if($admin->add($name,$pwd,$role)){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				echo json_encode($arr_str);
			}
		}
	}
	//删除管理员
	public function admin_del(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			echo json_encode($arr_str);
		}else{
			$admin = new Administrator;
			if($admin->del($id)){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				echo json_encode($arr_str);
			}
		}
	}
	//管理员修改状态
	public function admin_state_up(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			echo json_encode($arr_str);
		}else{
			$admin = new Administrator;
			if($admin->state_change($id,"1")){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				echo json_encode($arr_str);
			}
		}
	}
	//管理员修改状态
	public function admin_state_down(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			echo json_encode($arr_str);
		}else{
			$admin = new Administrator;
			if($admin->state_change($id,"0")){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				echo json_encode($arr_str);
			}
		}
	}
	//发送私信
	public function send_out(){
		$id=input("id");
		$value=input("value");
		$arr_str=["flag"=>"false"];
		if($id==null&&$value==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$msg = new Message;
			if($msg->add($id,$value)){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="服务器错误";
				echo json_encode($arr_str);
			}
		}
	}
	//上线用户
	public function user_state_up(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			echo json_encode($arr_str);
		}else{
			$user = new User;
			if($user->state_change($id,"2")){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				echo json_encode($arr_str);
			}
		}
	}
	//冻结用户
	public function user_state_down(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			echo json_encode($arr_str);
		}else{
			$user = new User;
			if($user->state_change($id,"3")){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				echo json_encode($arr_str);
			}
		}
	}
	//修改公司账户
	public function company_edit_acconut(){
		$name=input("content");
		$arr_str=["flag"=>"false"];
		$info=file_get_contents('https://api.eospark.com/api?module=account&action=get_account_info&apikey=b775cc21c752db60065cb6a71bc01c36&account='.$name);
		$info_arr=json_decode($info);
		if($info_arr->data->creator==""){
			$arr_str["msg"]="EOS账户不存在";
			echo json_encode($arr_str);
			return;
		}
		if($name==null){
			$arr_str["msg"]="请输入值";
			echo json_encode($arr_str);
		}else{
			$flag=db("company")->where('Id',"1")->update(["account"=>$name]);
			if($flag){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="修改失败";
				echo json_encode($arr_str);
			}
		}
	}
	//修改公司QQ
	public function company_edit_qq(){
		$name=input("content");
		$arr_str=["flag"=>"false"];
		if($name==null){
			$arr_str["msg"]="请输入值";
			echo json_encode($arr_str);
		}else{
			$flag=db("company")->where('Id',"1")->update(["qq"=>$name]);
			if($flag){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="修改失败";
				echo json_encode($arr_str);
			}
		}
	}
	//修改公司手机
	public function company_edit_phone(){
		$name=input("content");
		$arr_str=["flag"=>"false"];
		if($name==null){
			$arr_str["msg"]="请输入值";
			echo json_encode($arr_str);
		}else{
			$flag=db("company")->where('Id',"1")->update(["phone"=>$name]);
			if($flag){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="修改失败";
				echo json_encode($arr_str);
			}
		}
	}
	//修改公司手机
	public function income_edit(){
		$field=input("field");
		$name=input("value");
		$arr_str=["flag"=>"false"];
		if($name==null||$field==null){
			$arr_str["msg"]="请输入值";
		}else{
			if($field!='super'||$field!='first'||$field!='second'||$field!='fee'){
				$flag=db("agent_income")->where('Id',"1")->update([$field=>$name]);
				if($flag){
					$arr_str["flag"]="true";
				}else{
					$arr_str["msg"]="修改失败";
				}
			}
		}
		echo json_encode($arr_str);
	}
	//充值二维码
	public function coim_repay_edit_img(){
		$id=input("id");
		$name=input("name");
		if($id!=null&&$name!=null){
			if(is_uploaded_file($_FILES['file']['tmp_name'])){
				$catalog="./static/public/img/QR_code/";
				$file_name=$catalog.$name.'.'.substr($_FILES['file']['type'],strripos($_FILES['file']['type'],"/")+1);
				$falg=move_uploaded_file($_FILES['file']['tmp_name'],$file_name);
				$coin=new Coin;
				$coin->edit($id,"img",$name.'.'.substr($_FILES['file']['type'],strripos($_FILES['file']['type'],"/")+1));
				if($falg){
					echo '{"code": 0,"msg": "true","data": {"src": "'.$file_name.'"}}';
				}else{
					echo '{"code": 1,"msg": "false","data": {"src": "Null"}}';
				}
			}
		}else{
			echo '{"code": 1,"msg": "false","data": {"src": "Null"}}';
		}
	}
	//绑定二维码
	public function company_edit_img(){
		if(is_uploaded_file($_FILES['file']['tmp_name'])){
			$catalog="./static/public/img/QR_code/";
			$file_name=$catalog.'Recharge.jpg';
			$falg=move_uploaded_file($_FILES['file']['tmp_name'],$file_name);
			if($falg){
				echo '{"code": 0,"msg": "true","data": {"src": "'.$file_name.'"}}';
			}else{
				echo '{"code": 1,"msg": "false","data": {"src": "Null"}}';
			}
		}
	}
	//添加一个货币
	public function coin_add(){
		$arr_str=["flag"=>"false"];
		$type=input("type");
		$min_company=input("min_company");
		$min_recharge=input("min_recharge");
		$max_recharge=input("max_recharge");
		$amount=input("amount");
		$proportion=input("proportion");
		if($type==null||$min_company==null||$min_recharge==null||$max_recharge==null||$amount==null||$proportion==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			if(getFloatLength($min_recharge)>$min_company||getFloatLength($max_recharge)>$min_company){
				$arr_str["msg"]="充值数量最低位不能低于设置的最小单位";
				echo json_encode($arr_str);
				return;
			}else{
				$coin=new Coin;
				if($coin->add(strtoupper($type),$min_company,$min_recharge,$max_recharge,$amount,$proportion)){
					$arr_str["flag"]="true";
					echo json_encode($arr_str);
				}
			}
		}
	}
	public function coin_edit(){
		$arr_str=["flag"=>"false"];
		$id=input("id");
		$field=input("field");
		$value=input("value");
        if($id==null||$field==null||$value==null){
            $arr_str["msg"]="传输错误";
            echo json_encode($arr_str);
        }else{
            $coin=new Coin;
            switch ($field) {
				case 'coin':
                    $field="name";
                    break;
                case 'min_unit':
                    $field="smallest_unit";
                    break;
                case 'min_re':
                    $field="min_recharge";
                    break;
                case 'max_re':
                    $field="max_recharge";
                    break;
                case 'proportion':
                    $field="proportion";
                    break;
                case 'publisher':
                    $field="publisher";
                    break;
                default:
                    $arr_str["msg"]="未找到对应数据";
                    echo json_encode($arr_str);
                    return;
            }
            $coin->edit($id,$field,$value);
			$arr_str["flag"]="true";
			echo json_encode($arr_str);
		}
	}
	public function coin_state_down(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$coin = new Coin;
			if($coin->state_change($id,"0")){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				echo json_encode($arr_str);
			}
		}
	}
	public function coin_state_up(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			echo json_encode($arr_str);
		}else{
			$coin = new Coin;
			if($coin->state_change($id,"1")){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				echo json_encode($arr_str);
			}
		}
	}
	public function coin_edit_amount(){
		$id=input("id");
		$state=input("state");
		$arr_str=["flag"=>"false"];
		if($id==null||$state==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			if($state=="true"){
				$state="1";
			}else{
				$state="0";
			}
			$coin=new Coin;
			if($coin->amount_edit($id,$state)){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="错误";
				echo json_encode($arr_str);
			}
		}
	}
	public function bid_state_edit(){
		$id=input("id");
		$state=input("state");
		$arr_str=["flag"=>"false"];
		if($id==null||$state==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$num=0;
			switch ($state) {
				case '1':
					$num=1;
					break;
				case '2':
					$num=2;
					break;
				case '3':
					$num=3;
					break;
				case '4':
					$num=4;
					break;
			}
			if($num==0){
				$arr_str["msg"]="未找到格式";
				echo json_encode($arr_str);
			}
			$bid=new Bid_issuing;
			if($bid->state_edit($id,$num)){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="服务器错误";
				echo json_encode($arr_str);
			}
		}
	}
	public function bid_add(){
	    sleep(2);
		$interest=input("interest");
		$title=input("title");
		$total=input("total");
		$need=input("need");
		$start=input("start");
		$end=input("end");
		$repay=input("repay");
		$details=input("details");
		$info=input("info");
		$purpose=input("purpose");
		$min_eos=input("min_eos");
		$max_eos=input("max_eos");
		$profit=input("profit");
		$type=input("type");
		$arr_str=["flag"=>"false"];
		if($interest==null||$title==null||$total==null||$need==null||$start==null||$end==null||$repay==null||$purpose==null||$min_eos==null||$max_eos==null||$profit==null||$type==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$bid=new Bid_issuing;
			$coin=new Coin;
			$coin_list=$coin->coin_list();
			foreach ($coin_list as $key => $value) {
				if($value["id"]==$need){
					$need_pro=$value["proportion"];
				}
				if($value["id"]==$interest){
					$inter_pro=$value["proportion"];
				}
			}
			$data=["title"=>$title,"time"=>time(),"total"=>$total,"need_coin_id"=>$need,"start_time"=>$start,"end_bid_time"=>$end,"repayment_time"=>$repay,"interest_coin_id"=>$interest,"details"=>$details,"info"=>$info,"purpose"=>$purpose,"min_eos"=>0,"max_eos"=>$max_eos,"annual_profit"=>$profit,"type"=>$type,"proportion"=>num_point(1/$need_pro*$inter_pro,8)];
			$bid_id=$bid->add($data);
			if($bid_id){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
				$this->auto_up_bid($bid_id);
			}else{
				$arr_str["msg"]="服务器错误";
				echo json_encode($arr_str);
			}
		}
	}
	public function bid_edit(){
		$id=input("id");
		$interest=input("interest");
		$title=input("title");
		$total=input("total");
		$need=input("need");
		$start=input("start");
		$end=input("end");
		$repay=input("repay");
		$details=input("details");
		$info=input("info");
		$purpose=input("purpose");
		$min_eos=input("min_eos");
		$max_eos=input("max_eos");
		$profit=input("profit");
		$type=input("type");
		$arr_str=["flag"=>"false"];
		if($id==null||$interest==null||$title==null||$total==null||$need==null||$start==null||$end==null||$repay==null||$purpose==null||$min_eos==null||$max_eos==null||$profit==null||$type==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$bid=new Bid_issuing;
         	$coin=new Coin;
			$coin_list=$coin->coin_list();
			foreach ($coin_list as $key => $value) {
				if($value["id"]==$need){
					$need_pro=$value["proportion"];
				}
				if($value["id"]==$interest){
					$inter_pro=$value["proportion"];
				}
			}
			$data=["title"=>$title,"time"=>time(),"total"=>$total,"need_coin_id"=>$need,"start_time"=>$start,"end_bid_time"=>$end,"repayment_time"=>$repay,"interest_coin_id"=>$interest,"details"=>$details,"info"=>$info,"purpose"=>$purpose,"min_eos"=>0,"max_eos"=>$max_eos,"annual_profit"=>$profit,"type"=>$type,"proportion"=>num_point(1/$need_pro*$inter_pro,8)];
			if($bid->edit($id,$data)){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="未修改或修改失败";
				echo json_encode($arr_str);
			}
		}
	}
	public function profit_state_ok(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
			return;
		}else{
			$periods = new Periods;
			if($periods->state_ok($id)){
				$info=$periods->info($id);
				$this->interest($info["bid"],$info["day"]);
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				echo json_encode($arr_str);
			}
		}

	}
	public function repay_state_ok(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			$arr_str["msg"]="传输错误";
		}else{
			$bid = new Bid_issuing;
			if($bid->state_edit($id,4)){
				$this->repay_money($id);
				$arr_str["flag"]="true";
			}
		}
		echo json_encode($arr_str);
	}
	public function super_state_down(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			echo json_encode($arr_str);
		}else{
			$super = new Super_agend;
			if($super->state_change($id,"2")){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				echo json_encode($arr_str);
			}
		}
	}
	public function super_state_up(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			echo json_encode($arr_str);
		}else{
			$super = new Super_agend;
			if($super->state_change($id,"1")){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				echo json_encode($arr_str);
			}
		}
	}
	public function super_add(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			$arr_str["msg"]="未定义参数";
			echo json_encode($arr_str);
		}else{
			$super = new Super_agend;
			if($super->sel_id($id)){
				$arr_str["msg"]="用户已是超级邀请人";
				echo json_encode($arr_str);
			}else{
				if($super->add($id)){
					$arr_str["flag"]="true";
					echo json_encode($arr_str);
				}else{
					$arr_str["msg"]="服务器错误";
					echo json_encode($arr_str);
				}
			}
		}
	}
	public function notice_add(){
		$arr_str=["flag"=>"false"];
		$title=input("title");
		$abstract=input("abstract");
		$type=input("type");
		$content=input("content");
		if($title==null||$abstract==null||$type==null||$content==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$notice=new Notice;
			if($notice->add($title,$abstract,$type,$content)){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="服务器错误";
				echo json_encode($arr_str);
			}
			
		}
	}
	public function notice_state_up(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			echo json_encode($arr_str);
		}else{
			$notice = new Notice;
			if($notice->state_change($id,"1")){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				echo json_encode($arr_str);
			}
		}
	}
	public function notice_state_down(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			echo json_encode($arr_str);
		}else{
			$notice = new Notice;
			if($notice->state_change($id,"2")){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				echo json_encode($arr_str);
			}
		}
	}
	public function notice_edit(){
		$arr_str=["flag"=>"false"];
		$id=input("id");
		$title=input("title");
		$abstract=input("abstract");
		$type=input("type");
		$content=input("content");
		if($id==null&&$title==null||$abstract==null||$type==null||$content==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$notice=new Notice;
			if($notice->edit($id,$title,$abstract,$type,$content)){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="服务器错误";
				echo json_encode($arr_str);
			}
			
		}
	}
	public function notify_add(){
		$value=input("value");
		$arr_str=["flag"=>"false"];
		if($value==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$notify=new Notify;
			$flag=$notify->add($value);
			if($flag){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="服务器错误";
				echo json_encode($arr_str);
			}
		}
	}
	public function notify_del(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$notify=new Notify;
			$flag=$notify->del($id);
			if($flag){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="服务器错误";
				echo json_encode($arr_str);
			}
		}
	}
	//首页轮播修改路径
	public function img_edit(){
		$id=input("id");
		$value=input("value");
		$arr_str=["flag"=>"false"];
		if($id==null||$value==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$img=new Img_carousel;
			$flag=$img->edit($id,$value);
			if($flag){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="服务器错误";
				echo json_encode($arr_str);
			}
		}
	}
	//首页轮播删除
	public function img_del(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$img=new Img_carousel;
			$flag=$img->del($id);
			if($flag){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="服务器错误";
				echo json_encode($arr_str);
			}
		}
	}
	//首页轮播上传
	public function wheel_img_upload(){
		if(is_uploaded_file($_FILES['file']['tmp_name'])){
			$img=new Img_carousel;
			$id=$img->sel_max_id()+1;
			$type=substr($_FILES['file']['type'],strripos($_FILES['file']['type'],"/")+1);
			$catalog="./static/public/img/wheel/";//路径
			$file_name=$catalog.$id.".".$type;
			$img_falg=move_uploaded_file($_FILES['file']['tmp_name'],$file_name);
			$data_flag=$img->add($id,$id.".".$type);
			if($img_falg&&$data_flag){
				echo '{"code": 0,"msg": "true","data": {"src": "'.$file_name.'"}}';
			}else{
				echo '{"code": 1,"msg": "false","data": {"src": "Null"}}';
			}
		}else{
			echo '{"code": 1,"msg": "false","data": {"src": "Null"}}';
		}
	}
	//修改关于我们的图片
	public function about_img(){
		if(is_uploaded_file($_FILES['file']['tmp_name'])){
			$catalog="./static/public/img/about/";
			$type=substr($_FILES['file']['type'],strripos($_FILES['file']['type'],"/")+1);
			$file_name=$catalog.time().".".$type;
			$flag=move_uploaded_file($_FILES['file']['tmp_name'],$file_name);

			if($flag){
				echo '{"code": 0,"msg": "true","data": {"src": "'.ltrim($file_name, ".").'"}}';
			}else{
				echo '{"code": 1,"msg": "false","data": {"src": "Null"}}';
			}
		}else{
			echo '{"code": 1,"msg": "false","data": {"src": "Null"}}';
		}
	}
	//关于我们修改
	public function about_edit(){
		$content=input("content");
		$arr_str=["flag"=>"false"];
		if($content==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$flag=db("company")->where("Id","1")->update(["about"=>$content]);
			if($flag){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="服务器错误";
				echo json_encode($arr_str);
			}
		}
	}
	//帮助添加
	public function help_add(){
		$title=input("title");
		$content=input("content");
		$type=input("type");
		$f_id=input("f_id");
		$arr_str=["flag"=>"false"];
		if($title==null&&$type==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$help=new Help;
			$data=["title"=>$title,"type"=>$type,"content"=>$content];
			if($f_id!=null){
				$data["f_id"]=$f_id;
			}
			if($help->add($data)){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="服务器错误";
				echo json_encode($arr_str);
			}
		}
	}
	//删除帮助
	public function help_del(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$help=new Help;
			if($help->del($id)){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="服务器错误";
				echo json_encode($arr_str);
			}
		}
	}
	//帮助详情
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
	//修改help内容
	public function help_edit(){
		$id=input("id");
		$title=input("title");
		$content=input("content");
		$arr_str=["flag"=>"false"];
		if($id==null&&$title==null){
			$arr_str["msg"]="传输错误";
			echo json_encode($arr_str);
		}else{
			$help=new Help;
			$data=["title"=>$title,"content"=>$content];
			if($help->edit($id,$data)){
				$arr_str["flag"]="true";
				echo json_encode($arr_str);
			}else{
				$arr_str["msg"]="服务器错误";
				echo json_encode($arr_str);
			}
		}
	}
	//帮助页面的img修改
	public function help_img_edit(){
		if(is_uploaded_file($_FILES['file']['tmp_name'])){
			$file_url='/static/public/img/help/';
			$type=substr($_FILES['file']['type'],strripos($_FILES['file']['type'],"/")+1);
			$file_name=$file_url.time().".".$type;
			$falg=move_uploaded_file($_FILES['file']['tmp_name'],'.'.$file_name);
			if($falg){
				echo '{"code": 0,"msg": "true","data": {"src": "'.config('url.')['ht'].$file_name.'"}}';
			}else{
				echo '{"code": 1,"msg": "false","data": {"src": "Null"}}';
			}
		}else{
			echo '{"code": 1,"msg": "false","data": {"src": "Null"}}';
		}
	}
	//绑定账号
	public function binding_state_ok(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id!=null){
			$real=new Log_real;
			$user=new User;
			$info=$real->sel_id($id);
			if(!$user->is_binding($info["address"])){
				$user->binding($info["user"],$info["address"]);
				//$user_money=balance($info["user"]);
				//$user_money[$this->coin_sel($info["coin"],true)]["frozen"]-=$info["number"];
				//$user->set_money($info["user"],json_encode($user_money));
				$real->remarks($id,"绑定账号");
				$real->state_ok($id);
				$user->binding($info["user"],$info["address"]);
				$arr_str["flag"]="true";
				user_inspect($info["user"],true);
			}else{
				$real->state_no($id);
				$real->remarks($id,"账号已绑定");
				$arr_str["msg"]="账号已绑定";
			}
		}
		echo json_encode($arr_str);
	}
	//充币成功
	public function recharge_state_ok(){
		$id=input("id");
		$arr_str=["flag"=>"false"];
		if($id!=null){
			$real=new Log_real;
			$user=new User;
			$info=$real->sel_id($id);
			$user_money=balance($info["user"]);
			if($user_money[$this->coin_sel($info["coin"],true)]["frozen"]<$info["number"]){
				$user_money[$this->coin_sel($info["coin"],true)]["frozen"]=0;
			}else{
				$user_money[$this->coin_sel($info["coin"],true)]["frozen"]-=$info["number"];
			}
			$user_money[$this->coin_sel($info["coin"],true)]["money"]+=$info["number"];
			if($real->state_ok($id)){
				$user->set_money($info["user"],json_encode($user_money));
				$real->remarks($id,"充值");
				$arr_str["flag"]="true";
			}
		}
		echo json_encode($arr_str);
	}
	//提币成功
	public function cash_state_ok(){
		$id=input("id");
		$value=input("value");
		//没有验证txid的真实性
		$arr_str=["flag"=>"false"];
		if($id!=null&&$value!=null){
			$real=new Log_real;
			$user=new User;
			$info=$real->sel_id($id);
			$user_money=balance($info["user"]);
			$user_money[$this->coin_sel($info["coin"],true)]["frozen"]-=$info["number"];
			$user->set_money($info["user"],json_encode($user_money));
			$real->remarks($id,"提币");
			if($real->state_ok($id,$value)){
				$arr_str["flag"]="true";
			}
		}
		echo json_encode($arr_str);
	}
	//改变超级代理人记录状态
	public function super_apply(){
	    $id=input('id');
	    $state=input('type')=="1"?'2':input('type')=="2"?'3':null;
        $arr_str=["flag"=>"false"];
	    if($id!=null&&$state!=null){
            $super_apply=new Super_apply();
            $info=$super_apply->getInfo($id);
            if($info['state']=="1"){
                $super_apply->up($id,['state'=>$state]);
                $arr_str["flag"]="true";
            }else{
                $arr_str["msg"]="当前状态不能修改";
            }
        }
        echo json_encode($arr_str);
    }
	//货币id与名字转换
	private function coin_sel($name,$id=false){
		$coin=new Coin;
		$coin_list=$coin->coin_list();
		foreach ($coin_list as $key => $value) {
			if($id){
				if($value["id"]==$name){
					return $value["name"];
				}
			}else{
				if($value["name"]==$name){
					return $value["id"];
				}
			}
		}
	}
	//检测自动投标
	private function auto_up_bid($bid_id){
		$bid=db("bid_issuing")->alias("b")->join("coin c","b.need_coin_id=c.Id")->join("coin c1","b.interest_coin_id=c1.Id")->where("b.Id",$bid_id)->field("c.name coin,b.need_coin_id coin_id,b.repayment_time repay,annual_profit,c1.smallest_unit unit,b.max_eos,total,b.proportion,b.min_eos")->find();
		$total=$bid["total"];
		$user_sum=db("user")->where("state","2")->count();
		for ($i=0; $i < $user_sum/20; $i++) {
			$user_list=db("user")->field("Id,auto_bidding,money")->where("state",'2')->page($i+1,20)->order("Id")->select();
			for ($i=0; $i < count($user_list); $i++) { 
				$value=$user_list[$i];
				$auto=json_decode($value["auto_bidding"],true);
				if($auto==null||$auto[$bid["coin"]]==false){
					continue;
				}
				$user_money=json_decode($value["money"],true);
				if(floor($user_money[$bid["coin"]]["money"])>$bid["min_eos"]&&$total>0){
					if (floor($user_money[$bid["coin"]]["money"])>$bid["max_eos"]) {
						$use_money=floor($bid["max_eos"]);
					}else{
						$use_money=floor($user_money[$bid["coin"]]["money"]);
					}
					if($use_money>$total){
						$use_money=$total;
					}
					$user_money[$bid["coin"]]["money"]-=$use_money;
					db('user')->where("Id",$value["Id"])->update(["money"=>json_encode($user_money)]);
					db("log_transaction")->insert(["type"=>"1","user"=>$value["Id"],"remarks"=>"自动投标","time"=>time(),"money"=>$use_money,"coin"=>$bid["coin_id"]]);
					db("tender_record")->insert(["user_id"=>$value["Id"],"bid_id"=>$bid_id,"time"=>time(),"money"=>$use_money,"coin_id"=>$bid["coin_id"],"state"=>'1']);
					$total-=$use_money;
					if($total==0){
						db("bid_issuing")->where("Id",$bid_id)->update(["state"=>"7"]);
						$time=strtotime(date('Ymd',strtotime("+1 day")));
						$pdata=[];
			            for($i=1; $i*30<$bid["repay"] ; $i++) { 
			            	$pdata[]=["phase"=>$i,"bid"=>$bid_id,"time"=>$time+30*$i*24*60*60,"money"=>num_point($bid["total"]*30*($bid["annual_profit"]/360/100)*$bid["proportion"],$bid['unit']),"day"=>'30',"state"=>"3"];
			            };
			            if($bid["repay"]%30!=0){
			            	$pdata[]=["phase"=>ceil($bid["repay"]/30),"bid"=>$bid_id,"time"=>$time+$bid["repay"]*24*60*60,"money"=>num_point($bid["total"]*ceil($bid["repay"]%30)*($bid["annual_profit"]/360/100)*$bid["proportion"],$bid['unit']),"day"=>ceil($bid["repay"]%30),"state"=>"3"];
			            }
			            foreach ($pdata as $keys => $values) {
							db("periods")->insert($values);
			            }
			            //超级受益人收益
			            $this->super_inv($bid_id);
						return;
					}
				}
			}
		}
	}
	//超级受益人吃利息
	private function super_inv($bid){
	    $user_list=db("tender_record")->alias("t")->join("user u","t.user_id=u.Id")->join("super_agent sa","u.inviter=sa.user_id")->join("bid_issuing b","t.bid_id=b.Id")->join("coin c","b.interest_coin_id=c.Id")->field("u.Id u_id,u.inviter u_inv_id,sum(t.money) sum,b.interest_coin_id coin,b.proportion propor,u.reg_time,t.time,repayment_time repay,sa.proportion s_pro,b.annual_profit profit,c.smallest_unit unit")->where("t.bid_id",$bid)->select();
	    foreach ($user_list as $key => $value){
	    	if($value['u_id']==null){
	    		return;
	    	}
	        if($value["time"]-$value["reg_time"]<30*24*60*60){
	            $money=num_point($value["sum"]*$value["propor"]*$value["profit"]/100/360*$value["repay"]*$value["s_pro"]/100,$value["unit"]);
	            db("bonus")->insert(["user"=>$value["u_inv_id"],"in_user"=>$value["u_id"],"cause"=>"3","money"=>$money,"bid"=>$bid,"time"=>time(),"coin"=>$value["coin"]]);
	            $user_money=balance($value["u_inv_id"]);
	            $user_money[coin_transformation($value["coin"],true)]["money"]+=$money;
	            db("user")->where("Id",$value["u_inv_id"])->update(["money"=>json_encode($user_money)]);
	            db("log_transaction")->insert(["type"=>"4","remarks"=>"标分利","time"=>time(),"money"=>$money,"coin"=>$value["coin"],"user"=>$value["u_inv_id"]]);
	        }
	    }
	}
	//分发利息
	private function interest($bid,$day){
		$user_list=db("tender_record")->field("user_id,time,sum(money) money")->where("bid_id",$bid)->group("user_id")->select();
		$bid_info=db("bid_issuing")->alias("b")->join("coin c","b.interest_coin_id=c.Id")->field("c.Id id,c.name name,b.annual_profit annual,b.proportion propor,c.smallest_unit")->where("b.Id",$bid)->find();
		//受益人的收益比例
        $income=db("agent_income")->find(1);
        foreach ($user_list as $key => $value) {
            $user_money=balance($value["user_id"]);
            $add_money=num_point($value["money"]*$bid_info["propor"]*$bid_info["annual"]/100/360*$day,$bid_info["smallest_unit"]);
            $user_money[$bid_info["name"]]["money"]+=$add_money;
            db("user")->where("Id",$value["user_id"])->update(["money"=>json_encode($user_money)]);
            db("log_transaction")->insert(["type"=>"3","remarks"=>"标分利","time"=>time(),"money"=>$add_money,"coin"=>$bid_info["id"],"user"=>$value["user_id"]]);
            //邀请人分利

            //当前人
			$inv_in_user=db("user")->where("Id",$value["user_id"])->find();
			if($inv_in_user["inviter"]!=null){
				//邀请人
				//一级受益人邀请收益
				$inv_user=db("user")->where("Id",$inv_in_user["inviter"])->find();
				$inv_is_super=db('super_agent')->where('user_id',$inv_user['Id'])->find();
				if($inv_is_super!=null){
					if($inv_in_user['reg_time']+30*24*60*60>$value['time']){
						continue;
					}
				}
				//没有产生过收益及不是超级邀请人收益
				$first_money=$add_money*$income["first"]/100;
				db("bonus")->insert(["user"=>$inv_user["Id"],"in_user"=>$inv_in_user["Id"],'cause'=>"1","money"=>$first_money,"bid"=>$bid,"time"=>time(),"coin"=>$bid_info["id"]]);
				db("log_transaction")->insert(["type"=>"4","remarks"=>"好友分利","time"=>time(),"money"=>$first_money,"coin"=>$bid_info["id"],"user"=>$inv_user["Id"]]);
				$first_user_money=balance($inv_user["Id"]);
				$first_user_money[$bid_info["name"]]["money"]+=num_point($first_money,$bid_info["smallest_unit"]);
				db("user")->where("Id",$inv_user["Id"])->update(["money"=>json_encode($first_user_money)]);
				if($inv_user["inviter"]!=null){
					//二级邀请人收益
					$second_user=db("user")->where("Id",$inv_user["inviter"])->find();
					$second_money=$first_money*$income["second"]/100;
					db("bonus")->insert(["user"=>$second_user["Id"],"in_user"=>$inv_user["Id"],'cause'=>"2","money"=>$second_money,"bid"=>$bid,"time"=>time(),"coin"=>$bid_info["id"]]);
					db("log_transaction")->insert(["type"=>"4","remarks"=>"好友分利","time"=>time(),"money"=>$second_money,"coin"=>$bid_info["id"],"user"=>$second_user["Id"]]);
					$second_user_money=balance($second_user["Id"]);
					$second_user_money[$bid_info["name"]]["money"]+=num_point($second_money,$bid_info["smallest_unit"]);
					db("user")->where("Id",$second_user["Id"])->update(["money"=>json_encode($second_user_money)]);
				}
			}
		}
	}
	//返还标的投资金额
	private function repay_money($bid){
		$user_list=db("tender_record")->alias("t")->join("coin c","t.coin_id=c.Id")->field("user_id,sum(money) money,t.coin_id coin_id,c.name coin")->where("bid_id",$bid)->group("user_id")->select();
		foreach ($user_list as $key => $value) {
			$user_money=balance($value["user_id"]);
			$user_money[$value["coin"]]["money"]+=$value["money"];
			db("user")->where("Id",$value["user_id"])->update(["money"=>json_encode($user_money)]);
			db("log_transaction")->insert(["type"=>"2","remarks"=>"返还投资金额","time"=>time(),"money"=>$value["money"],"coin"=>$value["coin_id"],"user"=>$value["user_id"]]);
			db('tender_record')->where('Id',$bid)->update(['state'=>'3']);
		}
	}
	public function super_propor(){
		$id=input("id");
		$value=input("value");
		$arr_str=["flag"=>"false"];
		if($id!=null&&$value!=null){
			$super=new Super_agend;
			if($super->proportion_edit($id,num_point($value,4))){
				$arr_str["flag"]="true";
			}
		}
		echo json_encode($arr_str);
	}
}
?>