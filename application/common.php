<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//查看ip
function ip() {
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $res =  preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    return $res;
}
//获取浮点为小数点后几位
function getFloatLength($num) {
    $count = 0;
    $temp = explode ( '.', $num );
    if (sizeof ( $temp ) > 1) {
    $decimal = end ( $temp );
    $count = strlen ( $decimal );
    }
    return $count;
}
//设置token
function set_token($user_id){
    $ase=new app\index\controller\Ase();
    $token=$ase->encrypt(json_encode(['user_id'=>$user_id,'token'=>md5(time())]));
    db("user")->where("Id",$user_id)->update(["token"=>$token]);
    return $token;
}
//验证token
function is_token($token){
    $ase=new app\index\controller\Ase();
    $token=json_decode($ase->decrypt($token),true);
    $yuan=db("user")->field("token")->where("Id",$token['user_id'])->find()['token'];
    $yuan=json_decode($ase->decrypt($yuan),true);
    if($yuan['token']==$token['token']){
        return true;
    }else{
        return false;
    }
}
//验证手机和邮箱
function mp_ver($name,$head=0){
    $preg=config('verification.');
    if(preg_match($preg["email"],$name)==0){
        if($head==0){
            return false;
        }
        if($preg["phone"][$head]==''){
            $ver='/^011(999|998|997|996|995|994|993|992|991|990|979|978|977|976|975|974|973|972|971|970|969|968|967|966|965|964|963|962|961|960|899|898|897|896|895|894|893|892|891|890|889|888|887|886|885|884|883|882|881|880|879|878|877|876|875|874|873|872|871|870|859|858|857|856|855|854|853|852|851|850|839|838|837|836|835|834|833|832|831|830|809|808|807|806|805|804|803|802|801|800|699|698|697|696|695|694|693|692|691|690|689|688|687|686|685|684|683|682|681|680|679|678|677|676|675|674|673|672|671|670|599|598|597|596|595|594|593|592|591|590|509|508|507|506|505|504|503|502|501|500|429|428|427|426|425|424|423|422|421|420|389|388|387|386|385|384|383|382|381|380|379|378|377|376|375|374|373|372|371|370|359|358|357|356|355|354|353|352|351|350|299|298|297|296|295|294|293|292|291|290|289|288|287|286|285|284|283|282|281|280|269|268|267|266|265|264|263|262|261|260|259|258|257|256|255|254|253|252|251|250|249|248|247|246|245|244|243|242|241|240|239|238|237|236|235|234|233|232|231|230|229|228|227|226|225|224|223|222|221|220|219|218|217|216|215|214|213|212|211|210|98|95|94|93|92|91|90|86|84|82|81|66|65|64|63|62|61|60|58|57|56|55|54|53|52|51|49|48|47|46|45|44|43|41|40|39|36|34|33|32|31|30|27|20|7|1)[0-9]{0, 14}$/';
            if(preg_match($ver,$head.$name)==0){
                return false;
            }
        }else{
            if(preg_match($preg["phone"][$head],$name)==0){
                return false;
            }
        }
        return "phone";
    }else{
        return "email";
    }
}
//验证邮箱格式是否正确
function email_ver($email){
    $preg_email='/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims';
    if(preg_match($preg_email,$email)==0){
        return false;
    }else{
        return true;
    }
}
//验证手机格式是否正确???????????????????????????????????
function phone_ver($phone,$phone_head){
    $preg_phone='/^1[34578]{1}\d{9}$/';
    if(preg_match($preg_phone,$phone)==0){
        return false;
    }else{
        return true;
    }
}
//屏蔽邮箱和手机
function hideStar($str) { //用户名、邮箱、手机账号中间字符串以*隐藏 
    if (strpos($str, '@')) { 
        $email_array = explode("@", $str); 
        $prevfix = (strlen($email_array[0]) < 4) ? "" : substr($str, 0, 3); //邮箱前缀 
        $count = 0; 
        $str = preg_replace('/([\d\w+_-]{0,100})@/', '***@', $str, -1, $count); 
        $rs = $prevfix . $str; 
    } else { 
        $pattern = '/(1[3458]{1}[0-9])[0-9]{4}([0-9]{4})/i'; 
        if (preg_match($pattern, $str)) { 
            $rs = preg_replace($pattern, '$1****$2', $str); // substr_replace($name,'****',3,4); 
        } else { 
            $rs = substr($str, 0, 3) . "***" . substr($str, -2); 
        } 
    } 
    return $rs; 
}
//给手机发验证码
function phone_code($phone,$head){
    if(cache($phone."code")!=null){
        $code=cache($phone."code");
    }else{
        $code=rand('100000','999999');
    }
    if(cache($phone)){
        return null;
    }
    //发送验证码
    // $msg=config("country.")[$head];
    // $msg=str_replace('{$code}',$code,$msg);
    // $apikey = "http://intapi.253.com/send/json";             //253国际短信验证码
    // $info=["account"=>"I0240764","password"=>"7xCdn4FT9W1c57","mobile"=>$head.$phone,"msg"=>$msg];//253国际短信验证码
    if($head=='86'){
        $msg='【eosinvestmentbank】您的验证码是：'.$code;
        $apikey='https://dx.ipyy.net/sms.aspx';
        $info=['userid'=>'','extno'=>'','sendtime'=>'',"account"=>"8T00319","password"=>strtoupper(md5("8T0031913")),'code'=>8,"mobile"=>$phone,"content"=>$msg,'action'=>'send'];
    }else{
        $msg='[eosinvestmentbank]Your verification code is：'.$code;
        $apikey='https://dx.ipyy.net/I18NSms.aspx';
        $msg=strtoupper(bin2hex(iconv('utf-8','UCS-2BE',$msg)));
        $info=['userid'=>'','extno'=>'','sendtime'=>'',"account"=>"8T00338","password"=>strtoupper(md5("8T0033807")),'code'=>0,"mobile"=>$head.$phone,"content"=>$msg,'action'=>'send'];
    }
    $result=request_post($apikey,$info);
    // var_dump($result);
    $xml = simplexml_load_string($result);
    if($xml->returnstatus=="Faild"){
        // var_dump($xml->message);
        return false;
    }else{
        cache($phone,time(),58);
        cache($phone."code",$code,1800);
        return true;
    }
}
//手机验证码发送post请求
function request_post($url = '', $param = '') {
    if (empty($url) || empty($param)) {
        return false;
    }
    $postUrl = $url;
    $curlPost = $param;
    $ch=curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
//发送邮箱验证码
function email_code($email){
    $preg_email='/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims';
    if(preg_match($preg_email,$email)!=0){
        if(cache($email)){
            return null;
        }
        if(cache($email."code")!=null){
            $code=cache($email."code");
        }else{
            $code=rand('100000','999999');
        }
        $sendmail = 'noreply@eosinvestmentbank.com'; //发件人邮箱
        $sendmailpswd = "EOSbankproof13101363888"; //客户端授权密码,而不是邮箱的登录密码，就是手机发送短信之后弹出来的一长串的密码
        $send_name = 'eosinvestmentbank';// 设置发件人信息，如邮件格式说明中的发件人，
        $toemail = $email;//定义收件人的邮箱
        $to_name = 'eosinvestmentbank新用户';//设置收件人信息，如邮件格式说明中的收件人
        $mail = new \phpmailer\PHPMailer();
        $mail->isSMTP();// 使用SMTP服务
        $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
        $mail->Host = "smtp.exmail.qq.com";// 发送方的SMTP服务器地址
        $mail->SMTPAuth = true;// 是否使用身份验证
        $mail->Username = $sendmail;//// 发送方的
        $mail->Password = $sendmailpswd;//客户端授权密码,而不是邮箱的登录密码！
        $mail->SMTPSecure = "ssl";// 使用ssl协议方式
        $mail->Port = 465;//  qq端口465或587）
        $mail->setFrom($sendmail, $send_name);// 设置发件人信息，如邮件格式说明中的发件人，
        $mail->addAddress($toemail, $to_name);// 设置收件人信息，如邮件格式说明中的收件人，
        $mail->addReplyTo($sendmail, $send_name);// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址
        $mail->Subject = "eosinvestmentbank验证码";// 邮件标题
        $mail->IsHTML(true);
        $mail->Body = "新用户你好您的验证码是：<b>$code</b>,请在30分钟之内填写您的验证码";// 邮件正文
        if (!$mail->send()) { // 发送邮件
            return "Message could not be sent.Mailer Error: " . $mail->ErrorInfo;
        } else {
            cache($email,time(),58);
            cache($email."code",$code,1800);
            return true;
        }
    }else{
        return false;
    }
}
//验证验证码
function ver_code($name,$code){
    if($code==cache($name."code")){
        cache($name."code",null);
        cache($name,null);
        return true;
     }
    return false;
}
//与时间相关
function time_inspect(){
    $time=time();
    $coin_list=db("coin")->field("Id,name,smallest_unit,proportion")->where("state","1")->select();
    //检查哪些标到结标时间了
    $end_bid=db("bid_issuing")->where("end_bid_time","<",$time)->where("state","1")->select();
    if($end_bid!=null){
        foreach ($end_bid as $key => $value){
            if($value["state"]=="1"){
                //标未满过期并退钱
                foreach ($coin_list as $keys => $values) {
                    if($values["Id"]==$value["need_coin_id"]){
                        $coin_name=$values["name"];
                        break;
                    }
                }
                db("bid_issuing")->where("Id",$value["Id"])->update(["state"=>"5"]);
                //退钱
                $user=db("tender_record")->field("user_id,money")->where("bid_id",$value["Id"])->where("state","1")->select();
                foreach ($user as $keys => $values) {
                    $user_money=balance($values["user_id"]);
                    $user_money[$coin_name]["money"]=$user_money[$coin_name]["money"]+$values["money"];
                    db("user")->where("Id",$values["user_id"])->update(["money"=>json_encode($user_money)]);
                }
                db("tender_record")->where("bid_id",$value["Id"])->update(["state"=>'3']);
            }
        }
    }
    //标吃利息   吧到时间的利息改为付款中
    $periods=db("periods")->where("state","3")->where("time",'<',$time)->select();
    foreach ($periods as $key => $value) {
        db("periods")->where("Id",$value["Id"])->update(["state"=>"1"]);
    }
    //标结束
    $repay_bid=db("bid_issuing")->where("state","7")->where("repayment_time*24*60*60+end_bid_time","<",$time)->select();
    foreach ($repay_bid as $key => $value) {
        //反本
        db("tender_record")->where("bid_id",$value["Id"])->update(["state"=>"2"]);
        db("bid_issuing")->where("Id",$value["Id"])->update(["state"=>"8"]);
    }
}
//去掉小数点后n位
function num_point($num,$n){
    return floor($num*pow(10,$n))/pow(10,$n);
}
//获取用户的余额
function balance($user_id){
    $money=db("user")->field("money")->where("Id",$user_id)->find()["money"];
    if($money==null){
        $money=[];
    }else{
        $money=json_decode($money,true);
    }
    $coin_list=db("coin")->field("name,smallest_unit")->where("state","1")->select();
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
    db("user")->where("Id",$user_id)->update(["money"=>json_encode($money)]);
    return $money;
}
//把null转为0
function null_0($str){
    if($str==null){
        return 0;
    }else{
        return $str;
    }
}
//去eospark抓数据
function eospark($page=1){
    $account=db("company")->field("account")->find(1)["account"];
    $info=file_get_contents('https://api.eospark.com/api?module=account&action=get_account_related_trx_info&apikey=b775cc21c752db60065cb6a71bc01c36&account='.$account.'&page='.$page.'&size=20');
    $info_arr=json_decode($info,true);
    if($info_arr["errno"]!='0'){
        return;       
    }
    $sum=$info_arr["data"]["trace_count"];
    //抓取没有抓过的东西
    $yes=0;
    if($info_arr["data"]["trace_list"]!=[]){
        foreach ($info_arr["data"]["trace_list"] as $key => $value) {
            $before=db("eospark")->where("trx_id",$value["trx_id"])->find();
            if($before==null){
                $time=strtotime("+8 hours",strtotime($value["timestamp"]));
                db("eospark")->insert(["trx_id"=>$value["trx_id"],"time"=>$time,"sender"=>$value["sender"],"receiver"=>$value["receiver"],"quantity"=>$value["quantity"],"memo"=>$value["memo"],"symbol"=>$value["symbol"],"status"=>$value["status"]]);
                if($value["sender"]==$account){
                    //提现
                }else if($value["receiver"]==$account){
                    //充币
                    $user=db("user")->field("Id,money,EOS_account")->where("tag",$value["memo"])->find();
                    if($user){
                        $is_coin=db("coin")->where("name",$value["symbol"])->where("state","1")->find();
                        //判断我们是否有这个币种
                        if($is_coin==null){
                            break;
                        }
                        if($is_coin["min_recharge"]>$value["quantity"]||($is_coin["max_recharge"]<$value["quantity"]&&$is_coin["max_recharge"]!='-1')){
                            //充值不在范围内
                            break;
                        }
                        $coin_id=coin_transformation($value["symbol"]);//币种id
                        //验证他是否绑定了eos
                        if($user["EOS_account"]!=null){
                            //绑定了就直接到冻结
                            $user_money=balance($user["Id"]);
                            $user_money[$value["symbol"]]["frozen"]+=$value["quantity"];
                            db("user")->where("Id",$user["Id"])->update(["money"=>json_encode($user_money)]);
                        }
                        //没绑定就只有记录
                        db("log_real")->insert(["type"=>'1','user'=>$user["Id"],'address'=>$value["sender"],'time'=>$time,'number'=>$value["quantity"],'coin'=>$coin_id,"fee"=>'0','txid'=>$value["trx_id"],'state'=>"1"]);
                    }else{
                        $user=db("user")->field("Id,money,EOS_account")->where("tag",substr($value["memo"],0,5))->find();
                        if($user&&(int)$value['quantity']>=300){
                            db('super_apply')->where('user',$user['Id'])->update(['money'=>'1']);
                        }
                    }

                }
                $yes++;
            }
        }
    }else{
        return;
    }
    if($yes==20){
        eospark($page+1);
    }
}
//币名转ID
function coin_transformation($name,$id=false){
    $coin_list=db("coin")->field("Id,name")->select();
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
    return false;
}
//用户检查
function user_inspect($id,$set=false){
    $user=db('user')->field("EOS_account,phone,email")->where("Id",$id)->find();
    if($user){
        if($user["EOS_account"]!=null&&$user["phone"]!=null&&$user["email"]!=null){
            if($set){
                db('user')->where("Id",$id)->update(["state"=>'2']);
            }
            return true;
        }
    }
    return false;
}
//抓EOS的比例
function EOS_to_RMB($coin='CNY'){
    if(cache('eos_money')!=null){
        $money=cache('eos_money');
    }else{
        $get=file_get_contents('https://api.mytokenapi.com/ticker/search?category=currency&keyword=EOS&timestamp=1560496899463&code=c996bf91fa388504a214a04334998e2b&platform=web_pc&v=1.0.0&language=zh_CN&legal_currency='.$coin);
        $usd=json_decode($get,true);
        $money=num_point($usd['data'][0]['price_display_cny'],4);
        cache('eos_money',$money,600);
    }
        
    return($money);
}