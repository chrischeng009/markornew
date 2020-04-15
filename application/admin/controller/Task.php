<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\MerchantModel;
use app\common\model\ShopModel;
use app\common\model\TaskdetailModel;
use app\common\model\AdminModel;
use app\common\model\OrderModel;
use app\common\model\TaskModel;//本model放最后方便看清是否弄错

class Task extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['abc'] = gett('abc');
//        $_GET['merchant_id'] = gett('merchant_id');
        $_GET['shop_name'] = gett('shop_name');
        $_GET['mobile'] = gett('mobile');
        $_GET['tasksn'] = gett('tasksn');
        $_GET['status'] = gett('status');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        $_GET['aid'] = gett('aid');
//        if(!empty($_GET['merchant_id'])){
//            $where .= " and merchant_id = '".$_GET['merchant_id']."'";
//        }
       if(!empty($_GET['mobile'])){
            $_GET['merchant_id']=$merchant_id=MerchantModel::getMerchantID($_GET['mobile']);
            $where .= " and merchant_id = '".$merchant_id."'";
        }else{
           $_GET['merchant_id']='';
       }
        if(!empty($_GET['shop_name'])){
            $where .= " and shop_name like '%".$_GET['shop_name']."%'";
        }
        if(!empty($_GET['tasksn'])){
            $where .= " and tasksn = '".$_GET['tasksn']."'";
        }
        if(!empty($_GET['status'])){
            $where .= " and status = '".$_GET['status']."'";
        }
        $_GET['starttime'] = !empty($_GET['starttime'])?$_GET['starttime']:date("Y-m-d");
        $_GET['lasttime'] = !empty($_GET['lasttime'])?$_GET['lasttime']:date("Y-m-d");
        if(!empty($_GET['starttime'])){
            $where .= " and intime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
        }
        if(!empty($_GET['lasttime'])){
            $where .= " and intime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
        }
        //非超级管理员
        if($_SESSION['role_id']!==1){
            $_GET['aid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['aid'])){
            $strId = MerchantModel::getStrId($_GET['aid']);
            $where .= " and merchant_id in({$strId})";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'abc'=>$_GET['abc'],
                'merchant_id'=>$_GET['merchant_id'],
                'shop_name'=>$_GET['shop_name'],
                'tasksn'=>$_GET['tasksn'],
                'status'=>$_GET['status'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime'],
                'aid'=>$_GET['aid'],
            ]
        ];
        $query = TaskModel::tbname()->where("1=1 $where")->order('status<>2,intime desc')->paginate($limit,false,$params);
//        echo TaskModel::tbname()->getLastsql();
//        die;

        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);
        $this->assign('merchant_list',MerchantModel::select());
        $this->assign('shop_list',ShopModel::select());
        $this->assign('enum_status_arr',TaskModel::enum_status_arr());
        $this->assign('teacher_list',AdminModel::select("and role_id=5"));//所属业务员
       // 所有任务
        $this->assign('counttask',TaskModel::count($where));//总任务数
        // 所有单数
        $orderwhere='';
        $taskarr = TaskModel::tbname()->where("1=1 $where")->column('id');
        if($taskarr) {
            $taskarr2 = implode(',', $taskarr);
            $orderwhere .= " and task_id in({$taskarr2})";
            $countMerchantorder = OrderModel::count("$orderwhere");//所有订单
            $this->assign('countorder', $countMerchantorder);
        }else{
            $this->assign('countorder',0);
        }

    	return $this->fetch();
    }
    public function view()
    {
        $this->isAuth();
        $find = TaskModel::find("and id=".gett('id'));
        $this->assign('find',$find);
        
        $taskdetail_list = TaskdetailModel::select("and task_id=".$find['id']);
        $this->assign('taskdetail_list',$taskdetail_list);
        
        $find_merchant = MerchantModel::find("and id=".$find['merchant_id']);
        $find_shop = ShopModel::find("and id=".$find['shop_id']);
        
        $this->assign('find_merchant',$find_merchant);
        $this->assign('find_shop',$find_shop);
        
        $this->assign('cat_name',TaskModel::enum_cat_text($find['cat']));
        $this->assign('ishuabei_name',TaskModel::enum_ishuabei_text($find['ishuabei']));
        $this->assign('iscredit_name',TaskModel::enum_iscredit_text($find['iscredit']));
        
        return $this->fetch();
    }
    //通过
    public function act_edit_status3()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        $result = TaskModel::do_edit_status3(postt('id'));
        echo $result;exit;
    }
    //通过链接传参通过
    public function act_editurl()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        $result = TaskModel::do_edit_status3(gett('id'));
        echo $result;exit;
    }
    //拒绝
    public function act_edit_status4()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        $result = TaskModel::do_edit_status4(postt('id'));
        echo $result;exit;
    }
//   // 复制当前任务的信息重新添加为新的任务
//    public function act_add_task()
//    {
//        $auth = $this->control.'-lists';
//        $this->isAuth($auth);
//        $find_task = TaskModel::find("and id=".postt('id'));
//        $parem='';
//        $parem['worktype']=$find_task['worktype'];
//        $parem['shop_id']=$find_task['shop_id'];
//        $parem['cat']=$find_task['cat'];
//        $parem['tags']=$find_task['tags'];
//        $parem['merchant_id']=$find_task['merchant_id'];
//        $parem['goodstitle']=$find_task['goodstitle'];
//        $parem['goodsurl']=$find_task['goodsurl'];
//        $parem['mainimage']=$find_task['mainimage'];
//        $parem['isattr']=$find_task['isattr'];
//        $parem['ishuabei']=$find_task['ishuabei'];
//        $parem['iscredit']=$find_task['iscredit'];
//        $parem['present']=$find_task['present'];
//        $orderarr=TaskdetailModel::select("and task_id=".postt('id'));
//        foreach ($orderarr as $k=>$v){
//            $parem['searchkeywords'][] = $v['searchkeywords'];
//            $parem['price'][] = $v['price'];
//            $parem['priceremark'][] = $v['priceremark'];
//            $parem['num'][] = $v['num'];
//            $parem['istalk'][] = $v['istalk'];
//            $parem['iscart'][] = $v['iscart'];
//            $parem['remark'][] = $v['remark'];
//            $parem['isparity'][] = $v['isparity'];
//            $parem['iscollect'][] = $v['iscollect'];
//            $parem['isfocus'][] = $v['isfocus'];
//            $parem['isbrowseshop'][] = $v['isbrowseshop'];
//            $parem['isbrowseinfo'][] = $v['isbrowseinfo'];
//        }
//        $result = TaskModel::do_add($parem);
//        echo $result;exit;
//    }
    // 检测为明日单任务转为今天发布
    public function act_edit_shelf()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        $result = TaskModel::do_edit_shelf(postt('id'));
        echo $result;exit;
    }
    /*
     * 退款订单添加-下架
     */
    public function act_edit_refundoff()
    {
        $auth = 'order-act_edit_refundoff';
        $this->isAuth($auth);
        $find_task = TaskModel::find("and id=".postt('id'));
        $strId = OrderModel::getStrId($find_task['id']);
        $orderList = OrderModel::select("and id in(".$strId.")");
        $result = false;
        foreach($orderList as $v){
            if($v['type']==OrderModel::enumtype1 && in_array($v['status1'],[OrderModel::enumstatus1_1,OrderModel::enumstatus1_2,OrderModel::enumstatus1_6]) && in_array($find_task['status'],[TaskModel::enumstatus3,TaskModel::enumstatus7])){
                $data = [];
                $data['type'] = OrderModel::enumtype2;
                $data['status2'] = OrderModel::enumstatus2_1;
                $data['exceptioninfo'] = '申请下架';
                $data['uptime'] = time();
                OrderModel::edit("and id='".$v['id']."'",$data);
                $result = true;
            }
        }
        
        if($result){
            $tdata = [];
            $tdata['status'] = TaskModel::enumstatus7;
            $tdata['uptime'] = time();
            TaskModel::edit("and id='".$find_task['id']."'",$tdata);
            echo 'success';
        }else{
            echo '没有满足条件的下架订单';
        }
    }
    








    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
