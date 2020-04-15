<?php
namespace app\merchant\controller;

use think\Controller;
use app\common\model\MerchantModel;
use app\common\model\ShopModel;
use app\common\model\TaskdetailModel;
use app\common\model\TaskModel;
use app\common\model\ConfigModel;
use app\common\model\UserModel;
use think\Db;
use app\common\model\OrderModel;//本model放最后方便看清是否弄错

class Order extends Controller
{
    /*
     * 订单汇总
     */
    public function lists()
    {
        $where = "and merchant_id={$_SESSION['merchant_id']}";
        $_GET['abc'] = gett('abc');
        $_GET['shop_name'] = gett('shop_name');
        $_GET['task_id'] = gett('task_id');
        $_GET['type'] = gett('type');
        $_GET['mobile'] = gett('mobile');
        $_GET['user_id'] = gett('user_id');
        $_GET['wangwang'] = gett('tbcode');
        $_GET['verifycode'] = gett('verifycode');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        $_GET['limit'] = gett('limit');
        if(empty($_GET['abc'])){
//            $where .= " and payimage!=''";//淘宝订单截图
            $where .= " and status1 =4";
        }
        if(!empty($_GET['shop_name'])){
            $where .= " and shop_name like '%".$_GET['shop_name']."%'";
        }
        if(!empty($_GET['task_id'])){
            $where .= " and task_id = '".$_GET['task_id']."'";
        }
        if(!empty($_GET['type'])){
            $where .= " and type = '".$_GET['type']."'";
        }
        if(!empty($_GET['mobile'])){
            $where .= " and mobile = '".$_GET['mobile']."'";
        }
        if(!empty($_GET['user_id'])){
            $where .= " and user_id = '".$_GET['user_id']."'";
        }
        if(!empty($_GET['wangwang'])){
            $where .= " and wangwang = '".$_GET['wangwang']."'";
        }
        if(!empty($_GET['verifycode'])){
            $where .= " and verifycode = '".$_GET['verifycode']."'";
        }
        if(!empty($_GET['starttime'])){
            $where .= " and finishtime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
        }
        if(!empty($_GET['lasttime'])){
            $where .= " and finishtime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'abc'=>$_GET['abc'],
                'shop_name'=>$_GET['shop_name'],
                'task_id'=>$_GET['task_id'],
                'type'=>$_GET['type'],
                'mobile'=>$_GET['mobile'],
                'user_id'=>$_GET['user_id'],
                'wangwang'=>$_GET['wangwang'],
                'verifycode'=>$_GET['verifycode'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime']
            ]
        ];
        $query = OrderModel::tbname()->where("1=1 $where")->order('finishtime desc,id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $day = date("Y-m-d");
        $where1='';
        if(empty($_GET['starttime']) && empty($_GET['abc'])){
            $where1 .= " and finishtime >= '".strtotime($day." 00:00:00")."'";
        }
        if(empty($_GET['lasttime']) && empty($_GET['abc'])){
            $where1 .= " and finishtime <= '".strtotime($day." 23:59:59")."'";
        }
        $todypayprice=OrderModel::sum("$where $where1 and status1=4 ","payprice");//今日实际下单总金额
        $todyorder=OrderModel::count("$where $where1 and status1=4 ");//今日完成单数
        $todywithout_price=OrderModel::sum("$where $where1 and status1=4","without_price");//今日完成单数
        $todyrefund=OrderModel::sum("$where $where1 and type=2 and status2=3","payprice");//今日退款本金
        $todyrefundwithout_price=OrderModel::sum("$where $where1 and type=2 and status2=3","without_price");//今日完成单数
        $this->assign('todypayprice',$todypayprice);
        $this->assign('todyorder',$todyorder);
        $this->assign('todywithout_price',$todywithout_price);
        $this->assign('todyrefund',$todyrefund);
        $this->assign('todyrefundwithout_price',$todyrefundwithout_price);

        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('enum_type_arr',OrderModel::enum_type_arr());
        $this->assign('limit',$_GET['limit']);
        
    	return $this->fetch();
    }
    /*
     * 订单导出
     */
    public function export()
    {
        $where = "and merchant_id={$_SESSION['merchant_id']}";
        $_GET['shop_name'] = gett('shop_name');
        $_GET['shopname'] = gett('shopname');
        $_GET['task_id'] = gett('task_id');
        $_GET['type'] = gett('type');
        $_GET['mobile'] = gett('mobile');
        $_GET['user_id'] = gett('user_id');
        $_GET['wangwang'] = gett('wangwang');
        $_GET['verifycode'] = gett('verifycode');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        $_GET['intime'] = gett('intime');
        $_GET['abc'] = gett('abc');
        if(!empty($_GET['shop_name'])){
            $where .= " and shop_name like '%".$_GET['shop_name']."%'";
        }
        if(!empty($_GET['task_id'])){
            $where .= " and task_id = '".$_GET['task_id']."'";
        }
        if(!empty($_GET['type'])){
            $where .= " and type = '".$_GET['type']."'";
        }
        if(!empty($_GET['mobile'])){
            $where .= " and mobile = '".$_GET['mobile']."'";
        }
        if(!empty($_GET['user_id'])){
            $where .= " and user_id = '".$_GET['user_id']."'";
        }
        if(!empty($_GET['wangwang'])){
            $where .= " and wangwang = '".$_GET['wangwang']."'";
        }
        if(!empty($_GET['verifycode'])){
            $where .= " and verifycode = '".$_GET['verifycode']."'";
        }
        if(!empty($_GET['starttime'])){
            $where .= " and finishtime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
        }
        if(!empty($_GET['lasttime'])){
            $where .= " and finishtime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
        }
            $where .=" and status1=4";
        //商家导出自己的excel
        $where .=" and merchant_id={$_SESSION['merchant_id']}";
        $query = OrderModel::tbname()->where("1=1 $where")->order('finishtime desc,id desc')->limit(0,10000)->select();
        $this->assign('lists',$query);

        if(empty($_GET['shop_name'])){
            $shop_name1=ShopModel::find( "and merchant_id={$_SESSION['merchant_id']}");
            $shop_name='('.$shop_name1['name'].')';
        }else{
            $shop_name='('.$_GET['shop_name'].')';
        }
        if(!empty($_GET['shopname'])){
        $shop_name='('.$_GET['shopname'].')';
        }
        $this->assign('shop_name',$shop_name);
        if(!empty($_GET['intime'])){
            $this->assign('intime', $_GET['intime']);
        }
        return $this->fetch();
    }
    /*
     * 退款订单
     */
    public function lists2()
    {
        $where = "and merchant_id={$_SESSION['merchant_id']}";
        $_GET['shop_name'] = gett('shop_name');
        $_GET['status2'] = gett('status2');
        $_GET['mobile'] = gett('mobile');
        $_GET['wangwang'] = gett('wangwang');
        $_GET['verifycode'] = gett('verifycode');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        if(!empty($_GET['shop_name'])){
            $where .= " and shop_name like '%".$_GET['shop_name']."%'";
        }
        if(!empty($_GET['status2'])){
            $where .= " and status2 = '".$_GET['status2']."'";
        }else{
            $where .= " and type=2";
        }
        if(!empty($_GET['mobile'])){
            $where .= " and mobile = '".$_GET['mobile']."'";
        }
        if(!empty($_GET['wangwang'])){
            $where .= " and wangwang = '".$_GET['wangwang']."'";
        }
        if(!empty($_GET['verifycode'])){
            $where .= " and verifycode = '".$_GET['verifycode']."'";
        }
        if(!empty($_GET['starttime'])){
            $where .= " and finishtime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
        }
        if(!empty($_GET['lasttime'])){
            $where .= " and finishtime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'shop_name'=>$_GET['shop_name'],
                'status2'=>$_GET['status2'],
                'mobile'=>$_GET['mobile'],
                'wangwang'=>$_GET['wangwang'],
                'verifycode'=>$_GET['verifycode'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime']
            ]
        ];
        $query = OrderModel::tbname()->where("1=1 $where")->order('finishtime desc,id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('enum_status2_arr',OrderModel::enum_status2_arr());
        
    	return $this->fetch();
    }
    /*
     * 异常订单
     */
    public function lists3()
    {
        $where = "and merchant_id={$_SESSION['merchant_id']}";
        $_GET['shop_name'] = gett('shop_name');
        $_GET['status3'] = gett('status3');
        $_GET['mobile'] = gett('mobile');
        $_GET['wangwang'] = gett('wangwang');
        $_GET['verifycode'] = gett('verifycode');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        if(!empty($_GET['shop_name'])){
            $where .= " and shop_name like '%".$_GET['shop_name']."%'";
        }
        if(!empty($_GET['status3'])){
            $where .= " and status3 = '".$_GET['status3']."'";
        }else{
            $where .= " and type=3";

        }
        if(!empty($_GET['mobile'])){
            $where .= " and mobile = '".$_GET['mobile']."'";
        }
        if(!empty($_GET['wangwang'])){
            $where .= " and wangwang = '".$_GET['wangwang']."'";
        }
        if(!empty($_GET['verifycode'])){
            $where .= " and verifycode = '".$_GET['verifycode']."'";
        }
        if(!empty($_GET['starttime'])){
            $where .= " and finishtime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
        }
        if(!empty($_GET['lasttime'])){
            $where .= " and finishtime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'shop_name'=>$_GET['shop_name'],
                'status3'=>$_GET['status3'],
                'mobile'=>$_GET['mobile'],
                'wangwang'=>$_GET['wangwang'],
                'verifycode'=>$_GET['verifycode'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime']
            ]
        ];
        $query = OrderModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('enum_status3_arr',OrderModel::enum_status3_arr());
        
    	return $this->fetch();
    }
    /*
     * 订单评价判断
     */
    public function iscomment(){
        $id=$_POST['id'];
        $join = [
            ['merchant b','b.id=a.merchant_id'],
        ];
        $find=OrderModel::tbname()->alias('a')->join($join)->field('a.*,b.comrate')->where("1=1 and a.merchant_id={$_SESSION['merchant_id']} and a.id={$id}")->find();
//        $find = OrderModel::find("and merchant_id={$_SESSION['merchant_id']} and id={$id}");
        //此任务所有订单数量
        $ordernum=OrderModel::count("and merchant_id={$_SESSION['merchant_id']} and task_id={$find['task_id']}");
        //此任务已评价订单数量
        $comordernum=OrderModel::count("and merchant_id={$_SESSION['merchant_id']} and task_id={$find['task_id']} and iscomment in(2,3)");
        if($comordernum >= ceil($ordernum*$find['comrate'])){
            $rate=$find['comrate']*100;
            echo '评价数不能超过总单量的'.$rate.'%即（'.ceil($ordernum*$find['comrate']).'单），谢谢配合！';exit;
        }else{
            echo 'success';
        }
    }
    /*
    * 订单评价
    */
    public function comment()
    {
        $where = "and merchant_id={$_SESSION['merchant_id']} and iscomment in(2,3,4)";
        $_GET['shop_name'] = gett('shop_name');
        $_GET['iscomment'] = gett('iscomment');
        $_GET['mobile'] = gett('mobile');
        $_GET['wangwang'] = gett('wangwang');
        $_GET['verifycode'] = gett('verifycode');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        if(!empty($_GET['shop_name'])){
            $where .= " and shop_name like '%".$_GET['shop_name']."%'";
        }
        if(!empty($_GET['iscomment'])){
            $where .= " and iscomment = '".$_GET['iscomment']."'";
        }
        if(!empty($_GET['mobile'])){
            $where .= " and mobile = '".$_GET['mobile']."'";
        }
        if(!empty($_GET['wangwang'])){
            $where .= " and wangwang = '".$_GET['wangwang']."'";
        }
        if(!empty($_GET['verifycode'])){
            $where .= " and verifycode = '".$_GET['verifycode']."'";
        }
        if(!empty($_GET['starttime'])){
            $where .= " and comtime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
        }
        if(!empty($_GET['lasttime'])){
            $where .= " and comtime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
        }

        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'shop_name'=>$_GET['shop_name'],
                'iscomment'=>$_GET['iscomment'],
                'mobile'=>$_GET['mobile'],
                'wangwang'=>$_GET['wangwang'],
                'verifycode'=>$_GET['verifycode'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime']
            ]
        ];
        $query = OrderModel::tbname()->where("1=1 $where")->order('iscomment ASC,comtime DESC,intime DESC')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('enum_status3_arr',OrderModel::enum_comment_arr());

        return $this->fetch();
    }

    /*
     * 订单详情
     */
    public function view()
    {
        $find = OrderModel::find("and merchant_id={$_SESSION['merchant_id']} and id=".gett('id'));
        $this->assign('find',$find);
        
        $find_task = TaskModel::find("and id=".$find['task_id']);
        $this->assign('find_task',$find_task);
        
        $find_taskdetail = TaskdetailModel::find("and id=".$find['taskdetail_id']);
        $this->assign('find_taskdetail',$find_taskdetail);
        
        $find_merchant = MerchantModel::find("and id=".$find['merchant_id']);
        $this->assign('find_merchant',$find_merchant);
        
        $find_shop = ShopModel::find("and id=".$find['shop_id']);
        $this->assign('find_shop',$find_shop);
        
        $this->assign('type_name',OrderModel::enum_type_text($find['type']));
        $this->assign('status1_name',OrderModel::enum_status1_text($find['status1']));
        $this->assign('status2_name',OrderModel::enum_status2_text($find['status2']));
        $this->assign('status3_name',OrderModel::enum_status3_text($find['status3']));
        
        $this->assign('ishuabei_name',TaskModel::enum_ishuabei_text($find_task['ishuabei']));
        $this->assign('iscredit_name',TaskModel::enum_iscredit_text($find_task['iscredit']));
        
        $this->assign('cat_name',TaskModel::enum_cat_text($find_task['cat']));
        
        $this->assign('istalk_name',ConfigModel::enum_isnot_text($find_taskdetail['istalk']));
        $this->assign('isparity_name',ConfigModel::enum_isnot_text($find_taskdetail['isparity']));
        $this->assign('iscart_name',ConfigModel::enum_isnot_text($find_taskdetail['iscart']));
        $this->assign('iscollect_name',ConfigModel::enum_isnot_text($find_taskdetail['iscollect']));
        $this->assign('isfocus_name',ConfigModel::enum_isnot_text($find_taskdetail['isfocus']));
        $this->assign('isbrowseshop_name',ConfigModel::enum_isnot_text($find_taskdetail['isbrowseshop']));
        $this->assign('isbrowseinfo_name',ConfigModel::enum_isnot_text($find_taskdetail['isbrowseinfo']));
        
        return $this->fetch();
    }
    public function act_find()
    {
        $find = OrderModel::find("and merchant_id={$_SESSION['merchant_id']} and id=".postt('id'));
        echo json_encode($find,JSON_UNESCAPED_UNICODE);exit;
    }
    /*
     * 退款、异常订单添加
     */
    public function act_edit_exceptioninfo()
    {
        $find = OrderModel::find("and merchant_id={$_SESSION['merchant_id']} and id=".postt('id'));
        $find_task = TaskModel::find("and id={$find['task_id']}");
        if($find['type']!=1){
            echo '请勿重复操作';exit;
        }
        if(!in_array($find['status1'],[2,3,4,5,6])){
            echo '订单状态不符合';exit;
        }
        if(!in_array($find_task['status'],[TaskModel::enumstatus3,TaskModel::enumstatus6,TaskModel::enumstatus7])){
            echo '任务状态不符合';exit;
        }
        
        $data['type'] = postt('type');
        //退款订单
        if($data['type']==OrderModel::enumtype2){
            $data['status2'] = 1;
            $data['status3'] = 0;

        }
        //异常订单
        elseif($data['type']==OrderModel::enumtype3){
            $data['status3'] = 1;
            $data['status2'] = 0;
        }else{
            echo '订单类型不符合';exit;
        }
        $data['exceptioninfo'] = postt('exceptioninfo');
        $data['tkimage'] = postt('tkimage');
        $data['uptime'] = time();
        $data['yctime'] = time();
        $result = OrderModel::edit("and id='".postt('id')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
//    评价订单添加
        public function act_comment()
    {
        $find = OrderModel::find("and merchant_id={$_SESSION['merchant_id']} and id=".postt('id'));
        $find_task = TaskModel::find("and id={$find['task_id']}");
        if(!in_array($find['status1'],[2,3,4,5,6])){
            echo '订单状态不符合';exit;
        }
        if(!in_array($find_task['status'],[TaskModel::enumstatus3,TaskModel::enumstatus6,TaskModel::enumstatus7])){
            echo '任务状态不符合';exit;
        }
        $data['comment'] = postt('comment');
        $data['comvideo'] = postt('video');
        $data['comtime'] = time();
        $data['iscomment'] = 2;
        if(isset($_POST['pic'])) {
        $imgarr=$_POST['pic'];
        Db::table('ordercommentimg')->where("order_id={$find['id']}")->delete();
            $picarr = [];
            foreach ($imgarr as $v) {
                $picarr['order_id'] = $find['id'];
                $picarr['position'] = 'order';
                $picarr['picpath'] = $v;
                $picarr['intime'] = time();
                Db::table('ordercommentimg')->insertGetId($picarr);
            }
        }
        $result = OrderModel::edit("and id='".postt('id')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    //    评价订单图片删除
        public function act_comment_del()
    {
        $order_id=$_POST['order_id'];
            $pic = $_POST['pic'];
            $url = $_SERVER["DOCUMENT_ROOT"] . $pic;
            if(@unlink($url)) {
                $result = 1;
            }else{
                $result='';
            }
        if($result){
            echo 'success';
        }else{
            echo '删除失败';
        }
    }
    /*
     * 退款订单添加-下架
     */
    public function act_edit_refundoff()
    {
        $orderList = OrderModel::select("and merchant_id={$_SESSION['merchant_id']}  and  id in(".postt('id').")");
        foreach($orderList as $v){
            $find_task = TaskModel::find("and id={$v['task_id']}");
            
            if($v['type']==OrderModel::enumtype1 && in_array($v['status1'],[OrderModel::enumstatus1_1,OrderModel::enumstatus1_6]) && in_array($find_task['status'],[TaskModel::enumstatus3,TaskModel::enumstatus7])){
                $data = [];
                $data['type'] = OrderModel::enumtype2;
                $data['status2'] = OrderModel::enumstatus2_1;
                $data['exceptioninfo'] = '申请下架';
                $data['uptime'] = time();
                OrderModel::edit("and id='".$v['id']."'",$data);
            }
        }
        
        echo 'success';
    }
   





    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
