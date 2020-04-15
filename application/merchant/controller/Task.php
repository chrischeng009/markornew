<?php
namespace app\merchant\controller;

use think\Controller;
use app\common\model\MerchantModel;
use app\common\model\ShopModel;
use app\common\model\ConfigModel;
use app\common\model\MerchantwithoutModel;
use app\common\model\TaskdetailModel;
use app\common\model\OrderModel;
use app\common\model\TaskModel;//本model放最后方便看清是否弄错

class Task extends Controller
{
    public function lists()
    {
        $where = "and merchant_id={$_SESSION['merchant_id']}";
        $_GET['shop_name'] = gett('shop_name');
        $_GET['tasksn'] = gett('tasksn');
        $_GET['status'] = gett('status');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        $_GET['limit'] = gett('limit');
        if(!empty($_GET['shop_name'])){
            $where .= " and shop_name like '%".$_GET['shop_name']."%'";
        }
        if(!empty($_GET['tasksn'])){
            $where .= " and tasksn = '".$_GET['tasksn']."'";
        }
        if(!empty($_GET['status'])){
            $where .= " and status = '".$_GET['status']."'";
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
                'shop_name'=>$_GET['shop_name'],
                'tasksn'=>$_GET['tasksn'],
                'status'=>$_GET['status'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime']
            ]
        ];
        $query = TaskModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('shop_list',ShopModel::select());
        $this->assign('enum_status_arr',TaskModel::enum_status_arr());
        $this->assign('limit',$_GET['limit']);
    	return $this->fetch();
    }
    public function add()
    {
        $shop_list = ShopModel::select("and merchant_id={$_SESSION['merchant_id']} and status=".ShopModel::enumstatus2);
        $this->assign('shop_list',$shop_list);
        $this->assign('enum_cat_arr',TaskModel::enum_cat_arr());
        $this->assign('enum_worktype_arr',TaskModel::enum_worktype_arr());
        $this->assign('enum_ishuabei_arr',TaskModel::enum_ishuabei_arr());
        $this->assign('enum_iscredit_arr',TaskModel::enum_iscredit_arr());
        $this->assign('enum_isnot_arr',ConfigModel::enum_isnot_arr());
        $this->assign('enum_istime_arr',TaskModel::enum_istime_arr());
        $this->assign('enum_istag_arr',TaskModel::enum_istag_arr());

    	return $this->fetch();
    }
    public function act_price_html()
    {
        $params = $_POST;
        $params['merchant_id'] = $_SESSION['merchant_id'];
        if($params['istime']==2) {
            $numzs = array_sum($params['num']);
            for ($i = 8; $i <= 21; $i++) {
                $numsj[] = array_sum($params["time{$i}"]);
            }
            if (array_sum($numsj) != $numzs) {
                echo 'error';
                exit;
            }
        }
        $result = TaskModel::do_price_html($params);
        echo $result;exit;
    }
    public function act_add()
    {
        $params = $_POST;
        $params['merchant_id'] = $_SESSION['merchant_id'];
        $params['add_type']='fb';
        $result = TaskModel::do_add($params);
        echo $result;exit;
    }
    // 复制当前任务的信息重新添加为新的任务
    public function act_add_task()
    {
        $orderList = TaskModel::select("and merchant_id={$_SESSION['merchant_id']}  and  id in(".postt('id').")");
        foreach($orderList as $find_task) {
            $parem = [];
            $parem['worktype'] = $find_task['worktype'];
            $parem['shop_id'] = $find_task['shop_id'];
            $parem['cat'] = $find_task['cat'];
            $parem['tags'] = $find_task['tags'];
            $parem['merchant_id'] = $find_task['merchant_id'];
            $parem['goodstitle'] = $find_task['goodstitle'];  
            $parem['goodsurl'] = $find_task['goodsurl'];
            $parem['mainimage'] = $find_task['mainimage'];
            $parem['isattr'] = $find_task['isattr'];
            $parem['attrcolor'] = $find_task['attrcolor'];
            $parem['attrsize'] = $find_task['attrsize'];
            $parem['ishuabei'] = $find_task['ishuabei'];
            $parem['iscredit'] = $find_task['iscredit'];
            $parem['present'] = $find_task['present'];
            $parem['istime'] = $find_task['istime']; 
            $parem['istag'] = $find_task['istag'];
            $parem['tagurl'] = $find_task['tagurl'];
            $parem['tagkeyworks'] = $find_task['tagkeyworks'];
            $orderarr = TaskdetailModel::select("and task_id=" . $find_task['id']);
            $parem['add_type'] = 'fz';
            foreach ($orderarr as $k => $v) {
                $parem['searchkeywords'][] = $v['searchkeywords'];
                $parem['price'][] = $v['price'];
                $parem['priceremark'][] = $v['priceremark'];
                $parem['num'][] = $v['num'];
                $parem['without_price'][] = $v['without_price'];
                $parem['istalk'][] = $v['istalk'];
                $parem['isparity'][] = $v['isparity'];
                $parem['iscart'][] = $v['iscart'];
                $parem['remark'][] = $v['remark'];
                $parem['isparity'][] = $v['isparity'];
                $parem['iscollect'][] = $v['iscollect'];
                $parem['isfocus'][] = $v['isfocus'];
                $parem['isbrowseshop'][] = $v['isbrowseshop'];
                $parem['isbrowseinfo'][] = $v['isbrowseinfo'];
                $parem['remark'][] = $v['remark'];
                $parem['time8'][] = $v['time8'];
                $parem['time9'][] = $v['time9'];
                $parem['time10'][] = $v['time10'];
                $parem['time11'][] = $v['time11'];
                $parem['time12'][] = $v['time12'];
                $parem['time13'][] = $v['time13'];
                $parem['time14'][] = $v['time14'];
                $parem['time15'][] = $v['time15'];
                $parem['time16'][] = $v['time16'];
                $parem['time17'][] = $v['time17'];
                $parem['time18'][] = $v['time18'];
                $parem['time19'][] = $v['time19'];
                $parem['time20'][] = $v['time20'];
                $parem['time21'][] = $v['time21'];
            }
            $result = TaskModel::do_add($parem);
        }
        echo $result;exit;
    }
     // 修改当前任务
       public function edit(){
           $id=gett('id');
           $find_task = TaskModel::find("and id=".$id);
           $parem='';
           $parem['id']=$find_task['id'];
           $parem['worktype']=$find_task['worktype'];
           $parem['shop_id']=$find_task['shop_id'];
           $parem['cat']=$find_task['cat'];
           $parem['tags']=$find_task['tags'];
           $parem['merchant_id']=$find_task['merchant_id'];
           $parem['goodstitle']=$find_task['goodstitle'];
           $parem['goodsurl']=$find_task['goodsurl'];
           $parem['mainimage']=$find_task['mainimage'];
           $parem['isattr']=$find_task['isattr'];
           $parem['attrcolor']=$find_task['attrcolor'];
           $parem['attrsize']=$find_task['attrsize'];
           $parem['ishuabei']=$find_task['ishuabei'];
           $parem['iscredit']=$find_task['iscredit'];
           $parem['present']=$find_task['present'];
           $parem['istime']=$find_task['istime'];
           $parem['istag']=$find_task['istag'];
           $parem['tagurl']=$find_task['tagurl'];
           $parem['tagkeyworks']=$find_task['tagkeyworks'];
           $orderarr= TaskdetailModel::tbname()->field('id,searchkeywords,price,priceremark,num,without_price,istalk,isparity,isparity,iscart,remark,iscollect,isfocus,isbrowseshop,isbrowseinfo,
           time8,time9,time10,time11,time12,time13,time14,time15,time16,time17,time18,time19,time20,time21')
               ->where("1=1 and task_id=".$id)->order('id desc')->select();
           $this->assign('parem',$parem);
           $this->assign('orderarr',$orderarr);
           $shop_list = ShopModel::select("and merchant_id={$_SESSION['merchant_id']} and status=".ShopModel::enumstatus2);
           $this->assign('shop_list',$shop_list);
           $this->assign('enum_cat_arr',TaskModel::enum_cat_arr());
           $this->assign('enum_worktype_arr',TaskModel::enum_worktype_arr());
           $this->assign('enum_ishuabei_arr',TaskModel::enum_ishuabei_arr());
           $this->assign('enum_iscredit_arr',TaskModel::enum_iscredit_arr());
           $this->assign('enum_isnot_arr',ConfigModel::enum_isnot_arr());
           $this->assign('enum_istime_arr',TaskModel::enum_istime_arr());
           $this->assign('enum_istag_arr',TaskModel::enum_istag_arr());
           return $this->fetch();
       }
        //编辑后操作
        public function act_edit(){
           // 当前任务信息修改
            $params = $_POST;
            $params['merchant_id'] = $_SESSION['merchant_id'];
            $result = TaskModel::do_act_edit($params);
            echo $result;exit;


        }
    //付款
    public function act_edit_status2()
    {
        $orderList = TaskModel::select("and merchant_id={$_SESSION['merchant_id']} and status=1  and  id in(".postt('id').")");
        foreach($orderList as $find) {
            if (empty($find)) {
                echo "任务不存在";
                exit;
            }
            $result = TaskModel::do_edit_status2($find['id']);
        }
        echo $result;exit;
    }
    public function view()
    {
        $find = TaskModel::find("and merchant_id={$_SESSION['merchant_id']} and id=".gett('id'));
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
    public function act_without()
    {
        $merchant_id = $_SESSION['merchant_id'];
        $price = (float)postt('price');
        $num = (int)postt('num');
        
        $without_price = MerchantwithoutModel::get_price($merchant_id,$price);
        
        $return['code'] = '';
        $return['money'] = 0;
        $return['without_money'] = 0;
        if($without_price){
            $return['code'] = 'success';
            $return['money'] = $price*$num;
            $return['without_money'] = $without_price*$num;
        }
        echo json_encode($return);exit;
    }
    /*
   * 退款订单添加-下架
   */
    public function act_edit_refundoff()
    {
        $find_task = TaskModel::find("and id=".postt('id'));
        $strId = OrderModel::getStrId($find_task['id']);
        $orderList = OrderModel::select("and id in(".$strId.")");
        $result = false;
        foreach($orderList as $v){
            if($v['type']==OrderModel::enumtype1 && in_array($v['status1'],[OrderModel::enumstatus1_1,OrderModel::enumstatus1_2]) && in_array($find_task['status'],[TaskModel::enumstatus3])){
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
