<?php
namespace app\merchant\controller;

use app\admin\controller\Article;
use app\common\model\ArticleModel;
use think\Controller;
use app\common\model\ConfigModel;
use app\common\model\AdminModel;
use app\common\model\BannerModel;
use app\common\model\BankModel;
use app\common\model\OrderModel;
use app\common\model\MerchantModel;//本model放最后方便看清是否弄错

class Merchant extends Controller
{
    /*
     * 商家首页
     */
    public function index()
    {
        $find = MerchantModel::find("and id='".$_SESSION['merchant_id']."'");
        $this->assign('find',$find);
        
        $this->assign('type_name',MerchantModel::enum_type_text($find['type']));
        $this->assign('status_name',MerchantModel::enum_status_text($find['status']));
        
        $findTeacher = AdminModel::find("and id={$find['aid']}");
        //已发任务
        $hastask=OrderModel::count(" and merchant_id={$_SESSION['merchant_id']} and status1 !=5");
        $finishtask=OrderModel::count(" and merchant_id={$_SESSION['merchant_id']} and status1=4");
        $this->assign('hastask',$hastask);
        $this->assign('finishtask',$finishtask);
        $this->assign('findTeacher',$findTeacher);
        $banner=BannerModel::select(" and category_id=1");
        $this->assign('banner',$banner);

//        最新动态
        $articlearr=ArticleModel::select(" and category_id=1 and flag=5");
        $announcearr=ArticleModel::select(" and category_id=1 and flag in(1,2,3,4)");
        $this->assign('articlearr',$articlearr);
        $this->assign('announcearr',$announcearr);
        return $this->fetch();
    }
//    获取公告的详细资料
    public function article(){
        $id = postt('id');
        $article=ArticleModel::find(" and id={$id}");
        echo json_encode($article,true);
        exit;
    }
    public function register()
    {
        $this->assign('module',$this->module);
        $this->assign('control',$this->control);
        $this->assign('teacher_list',AdminModel::select("and role_id=5"));//负责导师-业务员
        
    	return $this->fetch();
    }
    public function act_send_register()
    {
        $mobile = postt('mobile');
        $count = MerchantModel::count("and mobile='".$mobile."'");
        if($count>=1){
            echo "手机号已存在！";exit;
        }
        
        $ip = getClientIP();
        $ip = str_replace('.','_',$ip);
        $_SESSION['smscode'] = rand(100000,999999);
        $smsmobile = "sms{$mobile}";
        $smsip = "sms{$ip}";
        
        if(!isset($_SESSION[$smsip])){
            $_SESSION[$smsip] = 0;
        }
        $_SESSION[$smsip]++;
        if($_SESSION[$smsip]>50){
            echo "您今日发送短信超过50条，请24小时后再试！";exit;
        }
        
        if(!isset($_SESSION[$smsmobile])){
            $_SESSION[$smsmobile] = 0;
        }
        $_SESSION[$smsmobile]++;
        if($_SESSION[$smsmobile]>20){
            echo "您今日发送短信超过20条，请24小时后再试！";exit;
        }
        
        //短信验证码10分钟有效期
        $_SESSION['smsexptime'] = time()+600;
        $result = sendsms($mobile,$_SESSION['smscode']);
        if($result){
            echo 'success';
        }else{
            echo '发送失败！';
            unset($_SESSION[$smsmobile]);
        }
    }
    public function act_sendsms()
    {
        $mobile = gett('mobile');
        $code = gett('code');
        $result = send_sms($mobile,$code);
        echo $result;
    }
    public function act_register()
    {
        $count = MerchantModel::count("and mobile='".postt('mobile')."'");
        if($count>0){
            echo "手机号已存在";exit;
        }
        
        if(empty($_SESSION['smscode']) || $_SESSION['smscode']!=postt('smscode')){
            echo '短信验证码错误！';exit;
        }
        if(empty($_SESSION['smsexptime']) || time()>$_SESSION['smsexptime']){
            echo "短信验证码已过期！";exit;
        }

        $data['mobile'] = postt('mobile');
        $data['mobile'] = postt('mobile');
        $data['password'] = md5(trim($_POST['password']));
        $data['weixin'] = postt('weixin');
        $data['aid'] = postt('aid');
        $data['status'] = MerchantModel::enumStatus1;
        $data['type'] = MerchantModel::enumType1;
        $data['intime'] = time();
        $data['uptime'] = time();
        $data['logintime'] = time();
        $find_config = ConfigModel::find();
        $regcode=postt('regcode');
        if(empty($regcode)||$regcode!=$find_config['regcode']){
            echo "入驻码错误，请联系管理员获取！";exit;
        }
        $data['worktime'] = $find_config['worktime'];
        $data['worknum'] = $find_config['worknum'];
        $result = MerchantModel::do_add($data);
        if($result){
            echo 'success';
            unset($_SESSION['smscode']);
            unset($_SESSION['smsexptime']);
        }else{
            echo '注册失败';
        }
    }
    public function findpass()
    {
        $this->assign('module',$this->module);
        $this->assign('control',$this->control);
        return $this->fetch();
    }
    public function act_findpass()
    {
        if(empty($_SESSION['smscode']) || $_SESSION['smscode']!=postt('smscode')){
            echo '短信验证码错误！';exit;
        }
        if(empty($_SESSION['smsexptime']) || time()>$_SESSION['smsexptime']){
            echo "短信验证码已过期！";exit;
        }
        $data['password'] = md5(trim($_POST['password']));
        $data['uptime'] = time();
        $result = MerchantModel::edit("and mobile='".postt('mobile')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    //找回密码
    public function act_send_findpass()
    {
        $mobile = postt('mobile');
        $ip = getClientIP();
        $ip = str_replace('.','_',$ip);
        $_SESSION['smscode'] = rand(100000,999999);
        $smsmobile = "sms{$mobile}";
        $smsip = "sms{$ip}";
        
        if(!isset($_SESSION[$smsip])){
            $_SESSION[$smsip] = 0;
        }
        $_SESSION[$smsip]++;
        if($_SESSION[$smsip]>50){
            echo "您今日的IP发送短信超过50条，请24小时后再试！";exit;
        }
        
        if(!isset($_SESSION[$smsmobile])){
            $_SESSION[$smsmobile] = 0;
        }
        $_SESSION[$smsmobile]++;
        if($_SESSION[$smsmobile]>20){
            echo "您今日发送短信超过20条，请24小时后再试！";exit;
        }
        
        //短信验证码10分钟有效期
        $_SESSION['smsexptime'] = time()+600;
        $result = sendsms($mobile,$_SESSION['smscode']);
        if($result){
            echo 'success';
        }else{
            echo '发送失败！';
            unset($_SESSION[$smsmobile]);
        }
    }
    public function edit_self()
    {
        $find = MerchantModel::find("and id='".$_SESSION['merchant_id']."'");
        $this->assign('find',$find);
        
        $bank_list = BankModel::select();
        $this->assign('bank_list',$bank_list);
        
        $this->assign('enum_acolor_arr',ConfigModel::enum_acolor_arr());
        
        return $this->fetch();
    }
    public function act_edit_self()
    {
        $data = [];
        $data['qq'] = postt('qq');
        $data['weixin'] = postt('weixin');
        $data['realname'] = postt('realname');
        $data['bankname'] = postt('bankname');
        $data['bankcode'] = postt('bankcode');
        $data['uptime'] = time();
        $result = MerchantModel::edit("and id='".$_SESSION['merchant_id']."'",$data);
        if($result){
            $_SESSION['acolor'] = postt('acolor');
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function edit_password()
    {
        $find = MerchantModel::find("and id='".$_SESSION['merchant_id']."'");
        $this->assign('find',$find);
        return $this->fetch();
    }
    public function act_edit_password()
    {
        $where = "and id='".$_SESSION['merchant_id']."'";
        $find = MerchantModel::find($where);
        $password = md5(trim($_POST['password']));
        if($find['password']!=$password){
            echo '旧密码输入不正确'; exit;
        }
        if(empty($_POST['comfirm_password'])){
            echo '新密码不能为空'; exit;
        }
        if(empty($_POST['comfirm_password2'])){
            echo '确认新密码不能为空'; exit;
        }
        if($_POST['comfirm_password']!=$_POST['comfirm_password2']){
            echo '两次密码输入不一至'; exit;
        }
        $data['password'] = md5(trim($_POST['comfirm_password']));
        $data['uptime'] = time();
        $result = MerchantModel::edit($where,$data);
        if($result){
            echo '操作成功';
        }else{
            echo '操作失败';
        }
    }
    public function login()
    {
//        if (isMobile()) {
//            header("Location:/home.php/admin/Getinfo");
//            exit;
//        }
        $this->assign('module',$this->module);
        $this->assign('control',$this->control);
        return $this->fetch();
    }
    public function act_login()
    {
        $mobile = postt('mobile');
        $password = md5(trim($_POST['password']));
        $find = MerchantModel::find("and mobile='".$mobile."' and password='".$password."'");
        if($find){
            if($find['status']==MerchantModel::enumStatus3){
                echo '您的帐号已拉黑，请联系客服。';exit;
            }
            $data['logintime'] = time();
            MerchantModel::edit("and id={$find['id']}",$data);
            $_SESSION['merchant_mobile'] = $find['mobile'];
            $_SESSION['merchant_id'] = $find['id'];
            $_SESSION['auths'] = $find['auths'];
            $_SESSION['acolor'] = '#399bff';
            unset($_SESSION['admin_name']);
            unset($_SESSION['admin_id']);
            echo 'success';
        }else{
            echo '用户名或密码错误';
        }
    }
    public function logout()
    {
        unset($_SESSION['merchant_mobile']);
        unset($_SESSION['merchant_id']);
        unset($_SESSION['auths']);
        unset($_SESSION['acolor']);
        $this->redirect('/home.php/'.$this->module.'/'.$this->control.'/login.html');
    }
    
    
    










    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
