<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\BankModel;
use app\common\model\RoleModel;
use app\common\model\ConfigModel;
use app\common\model\TaskModel;
use app\common\model\OrderModel;
use app\common\model\UsermlogsModel;
use app\common\model\UsercashModel;
use app\common\model\MerchantcashModel;
use app\common\model\AdmincashModel;
use app\common\model\MerchantrechargeModel;
use app\common\model\UserModel;
use app\common\model\MerchantModel;
use app\common\model\AdminofflineModel;
use app\common\model\AdminbankModel;
use app\common\model\MerchantloansModel;
use app\common\model\HongbaoModel;
use app\common\model\AdminModel;//本model放最后方便看清是否弄错


class Admin extends Controller
{
    /*
     * 系统首页
     */
    public function index()
    {
//        if($_SESSION['role_id']==1) {
//            if (isMobile()) {
//                header("Location:/home.php/admin/Getinfo");
//                exit;
//            }
//        }
        $where = "";
        $_GET['starttime'] = !empty($_GET['starttime'])?$_GET['starttime']:date("Y-m-d");
        $_GET['lasttime'] = !empty($_GET['lasttime'])?$_GET['lasttime']:date("Y-m-d");
        if(!empty($_GET['starttime'])){
            $where .= " and intime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
        }
        if(!empty($_GET['lasttime'])){
            $where .= " and intime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
        }
         // 超级管理员
        if($_SESSION['role_id']==1 ) {

        }
            //待审核任务
            $countTask2 = TaskModel::count("$where and status=2");
            $this->assign('countTask2', $countTask2);

            //订单
            $countOrderToday = OrderModel::sumorder($_GET['starttime'],$_GET['lasttime']);//今日单
//            $countOrderToday = OrderModel::count("$where and type=1 and status1!=5 and status2=0");//今日单
            $countOrderTomorrow = OrderModel::count("$where and worktype=2");//明日单
            $countOrder14 = OrderModel::count("$where and type=1 and status1=4");//正常订单-已结算-已完成
            $countOrder12 = OrderModel::count("$where and type=1 and status1=2");//正常订单-已领取-待下单
            $countOrder13 = OrderModel::count("$where and type=1 and status1=3");//正常订单-已完成-待审核
            $countOrder23 = OrderModel::count("$where and type in(2,3)");//退款订单-异常订单-异常单

            $this->assign('countOrderToday', $countOrderToday);
            $this->assign('countOrderTomorrow', $countOrderTomorrow);
            $this->assign('countOrder14', $countOrder14);
            $this->assign('countOrder12', $countOrder12);
            $this->assign('countOrder13', $countOrder13);
            $this->assign('countOrder23', $countOrder23);
            if ($_SESSION['role_id']==1){
            //粉丝提现总金额
            $sumMoneyUserCash12 = UsercashModel::sum("$where and status =2", 'money');
            $this->assign('sumMoneyUserCash12', $sumMoneyUserCash12);

            //客服申请金额
            $sumMoneyAdminCash12 = AdmincashModel::sum("$where and status=2", 'money');
            $this->assign('sumMoneyAdminCash12', $sumMoneyAdminCash12);

            //今日客服红包使用金额总和
            $kefuhongbaoprice=HongbaoModel::sum("$where and status=2",'orprice');
            $this->assign('kefuhongbaoprice', $kefuhongbaoprice);

             //今日客服线下金额总和
             $kefu_offline_money=AdminofflineModel::sum("$where",'money');
             $this->assign('kefu_offline_money',$kefu_offline_money);

            //商家充值总额
                $where2 = "";
                $_GET['starttime'] = !empty($_GET['starttime'])?$_GET['starttime']:date("Y-m-d");
                $_GET['lasttime'] = !empty($_GET['lasttime'])?$_GET['lasttime']:date("Y-m-d");
                if(!empty($_GET['starttime'])){
                    $where2 .= " and uptime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
                }
                if(!empty($_GET['lasttime'])){
                    $where2 .= " and uptime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
                }
            $sumMoneyMerchantrecharge12 = MerchantrechargeModel::sum("$where2 and status=2", 'money');
            $this->assign('sumMoneyMerchantrecharge12', $sumMoneyMerchantrecharge12);

            //商家提现总金额
            $sumMoneyMerchantCash12 = MerchantcashModel::sum("$where and status=2", 'money');
            $this->assign('sumMoneyMerchantCash12', $sumMoneyMerchantCash12);

            //粉丝返本金、佣金
            $sumMoneyMlogs2 = UsermlogsModel::sum("$where and type=2", 'money');//返本金
            $sumMoneyMlogs3 = UsermlogsModel::sum("$where and type=3", 'money');//返佣金
            $this->assign('sumMoneyMlogs2', $sumMoneyMlogs2);
            $this->assign('sumMoneyMlogs3', $sumMoneyMlogs3);

            //今日新增粉丝
            $countUser = UserModel::count($where);
            $this->assign('countUser', $countUser);

            //今日新增商家
            $countMerchant = MerchantModel::count($where);
            $this->assign('countMerchant', $countMerchant);

            //今日商家贷款
            $sumMoneyMerchantloans12 = MerchantloansModel::sum("$where and status=2", 'money');
            $this->assign('sumMoneyMerchantloans12', $sumMoneyMerchantloans12);

       

            //客服相关信息实时监控
            // 关联商家表进行查询
            $join = [
                ['order b','b.aid=a.id'],
            ];
            $kefuarr=AdminModel::tbname()->where(" role_id=2")->order('money desc')->select();

                $admintotalmoney = 0;
                $zhanshi='';
                if($kefuarr) {
                    foreach ($kefuarr as $k => $v) {
                        $zhanshi[$k]['name'] = $v['name'];//客服名称
                        $zhanshi[$k]['bank'] = AdminbankModel::select(" and admin_id={$v['id']}");//绑定银行信息
                        $zhanshi[$k]['order_num'] = OrderModel::count("$where and aid={$v['id']} and status1=4  and type in(1,3) ");//完成单数
                        $zhanshi[$k]['order_aidnum'] = OrderModel::count("$where and aid={$v['id']} and status1 in(2,3)");//领取单数
                        $zhanshi[$k]['order_price'] = OrderModel::sum("$where and aid={$v['id']} and status1=4 and type in(1,3)", "payprice");//完成总金额
                        $zhanshi[$k]['order_within_price'] = OrderModel::sum("$where and aid={$v['id']} and status1=4 and type in(1,3) ", "within_price");//完成总佣金
                        $zhanshi[$k]['reply_money'] = AdmincashModel::sum(" $where and admin_id={$v['id']} and status=2", "money");//申请公款
                        $zhanshi[$k]['offline_money'] = AdminofflineModel::sum(" $where and admin_id={$v['id']} and status=2", "money");//线下金额
                        $zhanshi[$k]['hongbao_money'] = HongbaoModel::sum(" $where and admin_id={$v['id']} and status=2", "orprice");//线下金额
                        $zhanshi[$k]['money'] = $v['money'];//当前余额
                        $admintotalmoney += $v['money'];
                    }
                }
                    $this->assign('admintotalmoney', $admintotalmoney);
                    $this->assign('zhanshi', $zhanshi);


        }
         else if( $_SESSION['role_id']==5) {
             //今日新增商家
             $countMerchant = MerchantModel::count($where);
             $this->assign('countMerchant', $countMerchant);


             //非超级管理员 业务员
             $strId = MerchantModel::getStrId($_SESSION['admin_id']);
             $where .= " and merchant_id in({$strId})";
             
             //今日商家贷款
             $sumMoneyMerchantloans12 = MerchantloansModel::sum("$where and status=2", 'money');
             $this->assign('sumMoneyMerchantloans12', $sumMoneyMerchantloans12);

            //待审核任务
            $countTask2 = TaskModel::count("$where and status=2");
            $this->assign('countTask2', $countTask2);

            //订单
            $countOrderToday = OrderModel::sumorder($_GET['starttime'],$_GET['lasttime']);//今日单
//            $countOrderToday = OrderModel::count("$where and type=1 and status1!=5 and status2=0");//今日单
            $countOrderTomorrow = OrderModel::count("$where and worktype=2");//明日单
            $countOrder14 = OrderModel::count("$where and type=1 and status1=4");//正常订单-已结算-已完成
            $countOrder12 = OrderModel::count("$where and type=1 and status1=2");//正常订单-已领取-待下单
            $countOrder13 = OrderModel::count("$where and type=1 and status1=3");//正常订单-已完成-待审核
            $countOrder23 = OrderModel::count("$where and type in(2,3)");//退款订单-异常订单-异常单

            $this->assign('countOrderToday', $countOrderToday);
            $this->assign('countOrderTomorrow', $countOrderTomorrow);
            $this->assign('countOrder14', $countOrder14);
            $this->assign('countOrder12', $countOrder12);
            $this->assign('countOrder13', $countOrder13);
            $this->assign('countOrder23', $countOrder23);

            //商家充值总额
            $sumMoneyMerchantrecharge12 = MerchantrechargeModel::sum("$where and status=2", 'money');
            $this->assign('sumMoneyMerchantrecharge12', $sumMoneyMerchantrecharge12);

            //商家提现总金额
            $sumMoneyMerchantCash12 = MerchantcashModel::sum("$where and status=2", 'money');
            $this->assign('sumMoneyMerchantCash12', $sumMoneyMerchantCash12);
        }elseif($_SESSION['role_id'] == 2 ){
            //业务员或者客服订单显示情况
            // 业务员
            $orderwhere = '';
            if ($_SESSION['role_id'] == 5) {
                $mechantarr = MerchantModel::tbname()->where("1=1 and aid=". $_SESSION['admin_id'])->column('id');
                if($mechantarr) {
                    $mechantarr2=implode(',',$mechantarr );
                    $orderwhere .= " and merchant_id in({$mechantarr2})";
                }
            }elseif ($_SESSION['role_id']==2){
                $orderwhere .=" and aid={$_SESSION['admin_id']}";
            }
            // 当日完结单数
            $countMerchantorder = OrderModel::count("$where $orderwhere and type=1 and status1=4");//正常订单-已结算-已完成
            $this->assign('countMerchanorder', $countMerchantorder);
            // 当日异常订单数
            $countMerchantunorder = OrderModel::count("$where $orderwhere and type in(2,3)");//退款订单-异常订单-异常单
            $this->assign('countMerchantunorder',$countMerchantunorder);
            //当前账户信息
            $admin=AdminModel::find(" and id={$_SESSION['admin_id']}");
            $this->assign('admin',$admin);
        }
//        var_dump($admin);
//        die;
        return $this->fetch();
    }
    public function lists()
    {
        $this->isAuth();
        $where = "and isdel=".ConfigModel::enumIsnot1;
        $_GET['admin_name'] = gett('admin_name');
        $_GET['name'] = gett('name');
        if(!empty($_GET['admin_name'])){
            $where .= " and admin_name like '%".$_GET['admin_name']."%'";
        }
        if(!empty($_GET['name'])){
            $where .= " and name like '%".$_GET['name']."%'";
        }
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = ['page'=>$page,'query'=>['admin_name'=>$_GET['admin_name'],'name'=>$_GET['name']]];
        $query = AdminModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);
        $totalmoney=AdminModel::tbname()->order('id desc')->sum('money');
        $this->assign('totalmoney',$totalmoney);
        $this->assign('role_list',RoleModel::select());
        $this->assign('bank_list',BankModel::select());
        $this->assign('enum_iswork_arr',ConfigModel::enum_isnot_arr());
        
        return $this->fetch();
    }
    public function add()
    {
        $this->isAuth();
        
        return $this->fetch();
    }
    public function act_add()
    {
        $this->isAuth();
        $count = AdminModel::count("and admin_name='".postt('admin_name')."'");
        if($count>=1){
            echo "用户名已存在";exit;
        }
        
        $data = $_POST;
        $data['auths'] = isset($_POST['auths'])?json_encode($_POST['auths']):"";
        $data['password'] = md5(trim($_POST['password']));
        $data['intime'] = time();
        $data['uptime'] = time();
        $result = AdminModel::add($data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function edit()
    {
        $this->isAuth();
        $find = AdminModel::find("and id=".gett('id'));
        $this->assign('find',$find);
        
        $auths = !empty($find['auths'])?json_decode($find['auths'],true):[];
        $this->assign('auths',$auths);
        
        $this->assign('role_list',RoleModel::select());
        $this->assign('bank_list',BankModel::select());
        $this->assign('enum_iswork_arr',ConfigModel::enum_isnot_arr());
        
        return $this->fetch();
    }
    public function act_edit()
    {
        $this->isAuth();
        $count = AdminModel::count("and admin_name='".postt('admin_name')."'");
        if($count>=2){
            echo "用户名已存在";exit;
        }
        
        $data = $_POST;
        unset($data['id']);unset($data['password']);
        
        $data['auths'] = isset($_POST['auths'])?json_encode($_POST['auths']):"";
        if(!empty($_POST['password'])){
            $data['password'] = md5(trim($_POST['password']));
        }
        $data['uptime'] = time();
        $result = AdminModel::edit("and id='".postt('id')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function edit_self()
    {
        $find = AdminModel::find("and id='".$_SESSION['admin_id']."'");
        $this->assign('find',$find);
        
        $bank_list = BankModel::select();
        $this->assign('bank_list',$bank_list);
        
        return $this->fetch();
    }
    public function act_edit_self()
    {
        $data = [];
        if(!empty($_POST['password'])){
            $data['password'] = md5(trim($_POST['password']));
        }
        $data['name'] = postt('name');
        $data['qq'] = postt('qq');
        $data['weixin'] = postt('weixin');
        $data['qrcode'] = postt('qrcode');
        $data['realname'] = postt('realname');
        $data['weixinmoneycode'] = postt('weixinmoneycode');
        $data['zfbmoneycode'] = postt('zfbmoneycode');
        $data['bankname'] = postt('bankname');
        $data['bankcode'] = postt('bankcode');
        $data['remark'] = postt('remark');
        $data['uptime'] = time();
        $data['dbusername'] = postt('dbusername');
        $data['dbpassword'] = postt('dbpassword');
        $result = AdminModel::edit("and id='".$_SESSION['admin_id']."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function act_del()
    {

        $this->isAuth();
        $data['isdel'] = ConfigModel::enumIsnot2;
        $data['uptime'] = time();

        $result = AdminModel::edit("and id='".postt('id')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function edit_password()
    {
        $find = AdminModel::find("and id='".$_SESSION['admin_id']."'");
        $this->assign('find',$find);
        return $this->fetch();
    }
    public function act_edit_password()
    {
        header("Content-Type:text/html;charset=UTF-8");
        $where = "and id='".$_SESSION['admin_id']."'";
        $find = AdminModel::find($where);
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
        $result = AdminModel::edit($where,$data);
        if($result){
            echo '操作成功';
        }else{
            echo '操作失败';
        }
    }
    public function login()
    {
        $this->assign('module',$this->module);
        $this->assign('control',$this->control);
        return $this->fetch();
    }
    public function act_login()
    {
        $admin_name = postt('admin_name');
        $password = md5(trim($_POST['password']));
        $find = AdminModel::find("and admin_name='".$admin_name."' and password='".$password."'");
        if($find){
            if($find['isdel']==ConfigModel::enumIsnot2){
                echo '您的帐号已被删除';exit;
            }
                $sessionurl = session_save_path() . '\sess_' . $find['sessionid'];
                if (file_exists($sessionurl) && file_get_contents($sessionurl)&& $find['role_id']!=1) {
                    session_destroy();
                    $res = @unlink($sessionurl);
                    echo '此账号已在其它设备登录，请重新登陆！';
                    exit();
                } else {
                    $_SESSION['admin_name'] = $find['admin_name'];
                    $_SESSION['admin_id'] = $find['id'];
                    $_SESSION['role_id'] = $find['role_id'];
                    $_SESSION['auths'] = $find['auths'];
                    $_SESSION['acolor'] = '#f71b03';
                    unset($_SESSION['merchant_mobile']);
                    unset($_SESSION['merchant_id']);
//                    $where = '';
//                    $where['sessionid'] = session_id();
//                    AdminModel::edit("and id={$find['id']}", $where);
                    echo 'success';
                }
        }else{
            echo '用户名或密码错误';
        }
    }
    public function logout()
    {
//        $find = AdminModel::find(" and id={$_SESSION['admin_id']}");
//        $sessionurl=session_save_path().'\sess_' .$find['sessionid'];
//        session_destroy();
//        $res=@unlink($sessionurl);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_id']);
        unset($_SESSION['role_id']);
        unset($_SESSION['auths']);
        unset($_SESSION['acolor']);
        $this->redirect('/home.php/'.$this->module.'/'.$this->control.'/login.html');
    }
//    public function moneyclear(){
//        $kefuarr=AdminModel::select();
//        $data=[];
//       foreach ($kefuarr as $v){
//           $data['money']=0;
//           $data['freeze_money']=0;
//          AdminModel::edit(" and id={$v['id']}",$data);
//       }
//        echo '清楚完毕,请勿刷新！';
//    }
    

    public function find_code(){
        $order_id=$_POST['order_id'];
        $hbarr = HongbaoModel::find(" and order_id={$order_id}");
        $arr=[];
        if($hbarr['code']){
            $arr['code']=1;
            $arr['msg']=$hbarr['code'];
            return $arr;
        }else{
            $arr['code']=0;
            return $arr;
        }
    }
    public function find_hbcode(){
        $id=$_POST['id'];
        $hbarr = HongbaoModel::find(" and id={$id}");
        $arr=[];
        if($hbarr['code']){
            $arr['code']=1;
            $arr['msg']=$hbarr['code'];
            return $arr;
        }else{
            $arr['code']=0;
            return $arr;
        }
    }




    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
