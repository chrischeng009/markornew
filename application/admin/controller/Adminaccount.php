<?php
namespace app\admin\controller;

use app\common\model\HongbaoModel;
use think\Controller;
use app\common\model\AdminModel;
use app\common\model\AdminofflineModel;
use app\common\model\AdmincashModel;
use app\common\model\AdminmlogsModel;
use app\common\model\OrderModel;
use app\common\model\AdminaccountModel;//本model放最后方便看清是否弄错

class Adminaccount extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['admin_id'] = gett('admin_id');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        //非超级管理员以及财务
        if($_SESSION['role_id']==2){
            $_GET['admin_id']=$_SESSION['admin_id'];
            //查询出前一天客服的余额
            $day=date("Y-m-d");
            $start_time = strtotime($day." 00:00:00");
            $last_time = strtotime($day." 23:59:59");
             $yestmoneyarr=AdminModel::tbname()->where("id={$_SESSION['admin_id']}")->find();
             if($yestmoneyarr['freeze_money'] >0){
                 $yestmoney=$yestmoneyarr['freeze_money'];
             }else{
                 $yestmoney=$yestmoneyarr['money'];
             }

            $sum = AdmincashModel::summoney2($_SESSION['admin_id']);
            $this->assign('yestmoney',$yestmoney);
            $this->assign('sum',$sum);
            $orderarr = OrderModel::select(" and finishtime>={$start_time} and finishtime<={$last_time} and aid={$_SESSION['admin_id']} and type in(1,3) and status1=4");
            $order_num=0;
            $order_price2=array();
            $order_within2=array();
            foreach ($orderarr as $v){
                $order_num++;
                $order_price2[]=$v['payprice'];
                $order_within2[]=$v['within_price'];
            }
            $order_price=array_sum($order_price2);
            $order_within=array_sum($order_within2);
            $refundorder= OrderModel::select(" and finishtime>={$start_time} and finishtime<={$last_time} and aid={$_SESSION['admin_id']} and type=2 and status1=4");
            $refund_order_num=0;
            $refund_order_price2=array();
            $refund_order_within2=array();
            foreach ($refundorder as $v){
                $refund_order_num++;
                $refund_order_price2[]=$v['payprice'];
                $refund_order_within2[]=$v['within_price'];
            }
            $refund_order_price=array_sum($refund_order_price2);
            $refund_order_within=array_sum($refund_order_within2);
            $this->assign('order_num',$order_num);//完成单数
            $this->assign('order_price',$order_price);//支出本金
            $this->assign('order_within',$order_within);//支出佣金
            $this->assign('refund_order_num',$refund_order_num);//退款订单数
            $this->assign('refund_order_price',$refund_order_price);//退款总本金
            $this->assign('refund_order_within',$refund_order_within);//退款总佣金
            $offline_money = AdminofflineModel::summoney($_SESSION['admin_id']);
            $this->assign('offline_money',$offline_money);//线下金额支出
            $hongbao= HongbaoModel::tbname()->where("intime>={$start_time} and intime<={$last_time} and admin_id={$_SESSION['admin_id']} and status=2")->sum('orprice');
            $this->assign('money',AdminModel::tbname()->where("id={$_SESSION['admin_id']}")->value('money')+$hongbao);
            $this->assign('hongbao',$hongbao);
        }
        if(!empty($_GET['admin_id'])){
            $where .= " and admin_id = '".$_GET['admin_id']."'";
        }
        if(!empty($_GET['starttime'])){
            $where .= " and intime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
        }
        if(!empty($_GET['lasttime'])){
            $where .= " and intime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
        }
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'admin_id'=>$_GET['admin_id'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime'],
            ]];
        $query = AdminaccountModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);
        $this->assign('admin_list',AdminModel::select("and role_id=2"));//所属客服
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
        $params = $_POST;
        if(!empty($_POST['remark'])){
            $params['remark']=$_POST['remark'];
        }else{
            $params['remark']='';
        }
        $data=[];
        $data['money']=$params['offline_money'];
        $data['admin_id'] = $_SESSION['admin_id'];
        $data['remark']='线下金额';
        if($data['money']>0) {
            AdminofflineModel::do_add($data);
        }
        $result = AdminaccountModel::do_add($params);
        echo $result;
    }
    public function del()
    {
        $this->isAuth();
        $params=$_POST;
        $result = AdminaccountModel::do_del($params);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
//    public function edit()
//    {
//        $this->isAuth();
//        $find = AdminofflineModel::find("and id=".gett('id'));
//        $this->assign('find',$find);
//        
//        $this->assign('enum_status_arr',AdminofflineModel::enum_status_arr());
//        $this->assign('find_admin',AdminModel::find("and id=".$find['admin_id']));
//        return $this->fetch();
//    }
//    public function act_edit()
//    {
//        $this->isAuth();
//        $params = $_POST;
//        
//        $result = AdminofflineModel::do_edit(postt('id'),$params);
//        echo $result;
//    }
    









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
