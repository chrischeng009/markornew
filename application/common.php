<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 77469350@qq.com
// +----------------------------------------------------------------------

// 应用公共文件
use think\Db;
use app\common\model\ConfigModel;

function atitle()
{
    $find_config = ConfigModel::find();
    $find_config['atitle'] = isset($find_config['atitle'])?$find_config['atitle']:'';
    return $find_config['atitle'];
}
function qtitle()
{
    $find_config = ConfigModel::find();
    $find_config['title'] = isset($find_config['title'])?$find_config['title']:'';
    return $find_config['title'];
}
function sendsms($mobile,$code)
{
    //帮跳转到内地服务器
    $url = "http://".$_SERVER['HTTP_HOST']."/home.php/merchant/merchant/act_sendsms.html?mobile={$mobile}&code={$code}";
    $result = httpGet($url,false);
    return $result;
}
function send_sms($mobile,$code)
{
    header('Content-Type: text/plain; charset=utf-8');
    $find_config = ConfigModel::find();
    vendor('aliyunsms.api_demo.SmsDemo');
    $sms = new \SmsDemo();
    //$response = $sms->sendSmsContent($mobile,$name,$find_config);//可配置的内容变量
    $response = $sms->sendSms($mobile,$code,$find_config);
    if($response->Code=='OK'){
        return true;
    }else{
        return false;
    }
}
function send_mail($address,$title,$content)
{
    header('Content-Type: text/plain; charset=utf-8');
    $find_config = ConfigModel::find();
    vendor('phpmailer.PHPMailerAutoload');
//    vendor('PHPMailer.class#PHPMailer');
    $mail = new \PHPMailer();          
     // 设置PHPMailer使用SMTP服务器发送Email
    $mail->IsSMTP();   
//    // 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
//    $mail->SMTPDebug = 0;
    $mail->SMTPDebug  = 0; // 关闭SMTP调试功能
    $mail->SMTPSecure = 'ssl';  // 使用安全协议
    // 设置邮件的字符编码，若不指定，则为'UTF-8'
    $mail->CharSet='UTF-8';         
    // 添加收件人地址，可以多次使用来添加多个收件人
    $mail->AddAddress($find_config['rece_email']);
//    $mail->AddAddress('2317497017@qq.com');
    // 设置邮件正文
    $mail->Body=$content;           
    // 设置邮件头的From字段。
    $mail->From=$find_config['email_from'];
    // 设置发件人名字
    $mail->FromName=$find_config['email_name'];
    // 设置邮件标题
    $mail->Subject=$title;          
    // 设置SMTP服务器。
    $mail->Host=$find_config['smtp_host'];
    $mail->Port=$find_config['smtp_port'];
    // 设置为"需要验证" ThinkPHP 的config方法读取配置文件
    $mail->SMTPAuth=true;
    //设置html发送格式
    $mail->isHTML(true);           
    // 设置用户名和密码。
    $mail->Username=$find_config['email_user'];
    $mail->Password=$find_config['email_password']; 
    // 发送邮件。
    return($mail->Send());
}

function printr($arr){
    header("Content-Type:text/html;charset=UTF-8");
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
    exit;
}

/*
 * query只做查询
 * execute可做插入更新删除
 */
function querysql($sql){
   return Db::query($sql);
}

function httpPost($url,$data,$isJosn=true){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($curl);
    curl_close($curl);
    if($isJosn){
        return json_decode($res,true);
    }
    return $res;
}

function httpGet($url,$isJosn=true) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    curl_close($curl);
    if($isJosn){
        return json_decode($res,true);
    }
    return $res;
}
function dostr($str){
    $str = preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','',$str); 
    $str = str_replace(array("\0","%00","\r"),'',$str); 
    $str = preg_replace("/&(?!(#[0-9]+|[a-z]+);)/si",'&',$str); 
    $str = str_replace(array("%3C",'<'),'<',$str); 
    $str = str_replace(array("%3E",'>'),'>',$str); 
    $str = str_replace(array('"',"'","\t",' '),array('"',"'",' ',' '),$str); 
    $str = trim($str); 
    return htmlspecialchars($str, ENT_QUOTES);
}
function gett($key){
    $str = isset($_GET[$key])?$_GET[$key]:'';
    return dostr($str);
}
function postt($key){
    $str = isset($_POST[$key])?$_POST[$key]:'';
    return dostr($str);
}
//获取客户端IP地址
function getClientIP() {
    if (getenv("HTTP_X_FORWARDED_FOR")) {
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    } elseif (getenv("HTTP_CLIENT_IP")) {
        $ip = getenv("HTTP_CLIENT_IP");
    } elseif (getenv("REMOTE_ADDR")) {
        $ip = getenv("REMOTE_ADDR");
    } else {
        $ip = "";
    }
    return $ip;
}
function mbstar($str){
    if(preg_match("/[\x{4e00}-\x{9fa5}]+/u", $str)){
        //按照中文字符计算长度
        $len = mb_strlen($str, 'UTF-8');
        if($len >= 3){
            //三个字符或三个字符以上掐头取尾，中间用*代替
            $str = mb_substr($str, 0, 1, 'UTF-8') . '***' . mb_substr($str, -1, 1, 'UTF-8');
        }elseif($len == 2){
            //两个字符
            $str = mb_substr($str, 0, 1, 'UTF-8') . '***';
        }
    }else{
        //按照英文字串计算长度
        $len = strlen($str);
        if($len >= 3){
            //三个字符或三个字符以上掐头取尾，中间用*代替
            $str = substr($str, 0, 1) . '***' . substr($str, -1);
        }elseif($len == 2){
            //两个字符
            $str = substr($str, 0, 1) . '***';
        }
    }
    return $str;
}
/*
 * 中文或英文
 * 截取前面3个字
 */
function mb_substr_three($str=''){
   if(preg_match("/[\x{4e00}-\x{9fa5}]+/u", $str)){
       //按照中文字符计算长度
       $len = mb_strlen($str, 'UTF-8');
       if($len >= 3){
           //三个字符或三个字符以上
           $str = mb_substr($str, 0, 3, 'UTF-8');
       }
   }else{
       //按照英文字串计算长度
       $len = strlen($str);
       if($len >= 3){
           //三个字符或三个字符以上
           $str = substr($str, 0, 3);
       }
   }
   return $str;
}
function submobile($mobile)
{
    $mobile = substr($mobile, 0, 3) . '****' . substr($mobile, -4);
    return $mobile;
}
function subbank($bankcode)
{
    $bankcode = '**** **** **** ' . substr($bankcode, -4);
    return $bankcode;
}
/*
public static function do_demo(){
    Db::startTrans();
    try{
        ////数据处理
        //exception("导入失败，会员帐号：{$user_name}，已重复");//抛异常并让catch抛出
        //提交
        Db::commit();
        //返回
        return "success";
    }catch(\Exception $e){
        Db::rollback();
        //exception($e);//直接报调试模式错误信息
        //$this->error($e->getMessage());//把异常消息捕获并抛出 控制器使用此行
        return $e->getMessage();
    }
}
*/
//判断是否为手机端
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
    {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
    {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array ('nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
        {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT']))
    {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            return true;
        }
    }
    return false;
}
//客服相关信息
function kefumoney($id){
    //客服余额显示
    $money=Db::name('admin')->where("id={$id}")->value('money');
    return $money;
}
//客服红包使用总额显示
function kefuhongbao($id){
    //客服余额显示
    $day = date("Y-m-d");
    $where = '';
    $where .= " and intime >= '" . strtotime($day . " 00:00:00") . "'";
    $where .= " and intime <= '" . strtotime($day . " 23:59:59") . "'";
    $money=Db::name('hongbao')->where("admin_id={$id} $where")->sum('orprice');
    return $money;
}
//生成微信二维码
  function create_qrcode($data,$size,$code){
    vendor("phpqrcode.phpqrcode");
    $data =$data;
    $fileurl="/qrcode/{$code}.jpg";
    $outfile=ROOT_PATH."/qrcode/{$code}.jpg";
    $level = 'L';
    $size =$size;
    $QRcode = new \QRcode();
    ob_start();
    $QRcode->png($data,$outfile,$level,$size,2);
    ob_end_clean();
    return $fileurl;
}