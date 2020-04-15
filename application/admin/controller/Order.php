<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\MerchantModel;
use app\common\model\ShopModel;
use app\common\model\TaskdetailModel;
use app\common\model\TaskModel;
use app\common\model\ConfigModel;
use app\common\model\UserModel;
use app\common\model\AdminModel;
use app\common\model\WithinModel;
use app\common\model\MerchantwithoutModel;
use app\common\model\OrderModel;//本model放最后方便看清是否弄错
use think\cache\driver\Redis;
use think\Db;
use think\Cache;
class Order extends Controller
{
    /*
     * 订单汇总
     */
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['abc'] = gett('abc');
//        $_GET['merchant_id'] = gett('merchant_id');
        $_GET['shop_name'] = gett('shop_name');
        $_GET['mobile'] = gett('mobile');
        $_GET['task_id'] = gett('task_id');
        $_GET['type'] = gett('type');
        $_GET['mobile'] = gett('mobile');
        $_GET['user_id'] = gett('user_id');
        $_GET['wangwang'] = gett('wangwang');
        $_GET['verifycode'] = gett('verifycode');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        $_GET['startprice'] = gett('startprice');
        $_GET['lastprice'] = gett('lastprice');
        $_GET['aid'] = gett('aid');
        $_GET['tid'] = gett('tid');
        $_GET['limit'] = gett('limit');
        //查出有订单截图的数据
//        if($_SESSION['role_id']>ConfigModel::enumroleid1 && empty($_GET['abc'])){
        if( empty($_GET['abc'])){
//            $where .= " and payimage!=''";//淘宝订单截图
            $where .=" and status1=4";
        }
//        if(!empty($_GET['merchant_id'])){
//            $where .= " and merchant_id = '".$_GET['merchant_id']."'";
//        }
        if(!empty($_GET['mobile'])){
            $_GET['merchant_id']=$merchant_id=MerchantModel::getMerchantID($_GET['mobile']);
            $where .= " and merchant_id = '".$merchant_id."'";
        }elsE{
            $_GET['merchant_id']='';
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
//        if(!empty($_GET['mobile'])){
//            $where .= " and mobile = '".$_GET['mobile']."'";
//        }
        if(!empty($_GET['user_id'])){
            $where .= " and user_id = '".$_GET['user_id']."'";
        }
        if(!empty($_GET['wangwang'])){
//            $where .= " and wangwang = '".$_GET['wangwang']."'";
            $where .= " and wangwang like '%".$_GET['wangwang']."%'";
        }
        if(!empty($_GET['verifycode'])){
            $where .= " and verifycode = '".$_GET['verifycode']."'";
        }
        if(empty($_GET['abc'])) {
            $_GET['starttime'] = !empty($_GET['starttime']) ? $_GET['starttime'] : date("Y-m-d");
            $_GET['lasttime'] = !empty($_GET['lasttime']) ? $_GET['lasttime'] : date("Y-m-d");
        }
        if(!empty($_GET['starttime'])){
            $where .= " and finishtime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
        }
        if(!empty($_GET['lasttime'])){
            $where .= " and finishtime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
        } 
        if(!empty($_GET['startprice'])){
            $where .= " and payprice >= '".$_GET['startprice']."'";
        }
        if(!empty($_GET['lastprice'])){
            $where .= " and payprice <= '".$_GET['lastprice']."'";
        }
        //非超级管理员 客服
        if($_SESSION['role_id'] ==2){
            $_GET['aid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['aid'])){
            $where .= " and aid='".$_GET['aid']."'";
        }
        //非超级管理员 业务员
        if ($_SESSION['role_id']==5){
            $_GET['tid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['tid'])){
            $strId = MerchantModel::getStrId($_GET['tid']);
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
                'task_id'=>$_GET['task_id'],
                'type'=>$_GET['type'],
//                'mobile'=>$_GET['mobile'],
                'user_id'=>$_GET['user_id'],
                'wangwang'=>$_GET['wangwang'],
                'verifycode'=>$_GET['verifycode'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime'],
                'startprice'=>$_GET['startprice'],
                'lastprice'=>$_GET['lastprice'],
                'aid'=>$_GET['aid'],
                'tid'=>$_GET['tid'],
                'limit'=>$_GET['limit'],
            ]
        ];
        $query = OrderModel::tbname()->where("1=1 $where")->order('finishtime desc,id desc')->paginate($limit,false,$params);
//        echo OrderModel::tbname()->getLastsql();
//        die;
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);
        $this->assign('total',$query->total());

        $day = date("Y-m-d");
        $where1='';
        if(empty($_GET['starttime'])){
            $where1 .= " and finishtime >= '".strtotime($day." 00:00:00")."'";
        }
        if(empty($_GET['lasttime'])){
            $where1 .= " and finishtime <= '".strtotime($day." 23:59:59")."'";
        }
        $todypayprice=OrderModel::sum("$where $where1","payprice");//今日下单总金额
        $todywithout_price=OrderModel::sum("$where $where1","without_price");//今日总服务费
        $todywithin_price=OrderModel::sum("$where $where1","within_price");//今日总佣金费
        $todyrefund=OrderModel::sum("$where $where1 and type=2 and status2=3","payprice");//今日退款金额
        $todyrefundwithout_price=OrderModel::sum("$where $where1 and type=2 and status2=3","without_price");//今日退款服务费
        $todyrefundwithin_price=OrderModel::sum("$where $where1 and type=2 and status2=3","within_price");//今日退款佣金
        $this->assign('todypayprice',$todypayprice);
        $this->assign('todywithout_price',$todywithout_price);
        $this->assign('todywithin_price',$todywithin_price);
        $this->assign('todyrefund',$todyrefund);
        $this->assign('todyrefundwithout_price',$todyrefundwithout_price);
        $this->assign('todyrefundwithin_price',$todyrefundwithin_price);

        $this->assign('merchant_list',MerchantModel::select());
        $this->assign('shop_list',ShopModel::select());
        $this->assign('enum_type_arr',OrderModel::enum_type_arr());
        $this->assign('server_list',AdminModel::select("and role_id=2"));//所属客服
        $this->assign('teacher_list',AdminModel::select("and role_id=5"));//所属业务员
        $this->assign('limit',$_GET['limit']);

    	return $this->fetch();
    }
    /*
     * 订单导出
     */
    public function export()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        $where = '';
        $_GET['merchant_id'] = gett('merchant_id');
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
        $_GET['aid'] = gett('aid');
        $_GET['tid'] = gett('tid');
        $_GET['abc'] = gett('abc');
        if($_SESSION['role_id']>ConfigModel::enumroleid1 && empty($_GET['abc'])){
            $where .= " and payimage!=''";//淘宝订单截图
        }
        if(!empty($_GET['merchant_id'])){
            $where .= " and merchant_id = '".$_GET['merchant_id']."'";
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
        if(!empty($_GET['aid'])){
            $where .= " and aid='".$_GET['aid']."'";
        }
            $where .=" and status1=4";

        //非超级管理员 客服
        if($_SESSION['role_id'] ==2){
            $_GET['aid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['aid'])){
            $where .= " and aid='".$_GET['aid']."'";
        }
        //非超级管理员 业务员
        if ($_SESSION['role_id']==5){
            $_GET['tid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['tid'])){
            $strId = MerchantModel::getStrId($_GET['tid']);
            $where .= " and merchant_id in({$strId})";
        }

        $query = OrderModel::tbname()->where("1=1 $where")->order('id desc')->limit(0,10000)->select();
        $this->assign('lists',$query);
        if(!empty($_GET['shop_name'])) {
            $shop_name='('.$_GET['shop_name'].')';
        }else{
            $shop_name='';
        }
        if(!empty($_GET['shopname'])){
            $shop_name='('.$_GET['shopname'].')';
        }else{
            $shop_name='';
        }
        $this->assign('shop_name', $shop_name);
        if(!empty($_GET['intime'])){
            $this->assign('intime', $_GET['intime']);
        }
        
        return $this->fetch();
    }
    /*
    * 订单导出
    */
    public function export2()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        $_GET['merchant_id'] = gett('merchant_id');
        $_GET['shop_name'] = gett('shop_name');
        $_GET['shopname'] = gett('shopname');
        $_GET['task_id'] = gett('task_id');
        $_GET['type'] = gett('type');
        $where='';
        $where .="a.task_id = {$_GET['task_id']}";
        $join = [
            ['task c', 'a.task_id=c.id'],
        ];
        $taskdetailorder = Db::table('taskdetail')->alias('a')->join($join)->field('a.*,c.shop_name,c.goodsurl,c.tasksn,c.id')->where($where)->order('a.id desc')->select();
        $find_task=TaskModel::find(" and id={$_GET['task_id']}");
        $this->assign('task_id',$_GET['task_id']);
        $this->assign('find_task',$find_task);
        $this->assign('taskdetailorder',$taskdetailorder);
        if(!empty($_GET['shop_name'])) {
            $shop_name='('.$_GET['shop_name'].')';
        }else{
            $shop_name='';
        }
        if(!empty($_GET['shopname'])){
            $shop_name='('.$_GET['shopname'].')';
        }else{
            $shop_name='';
        }
        $this->assign('shop_name', $shop_name);
        if(!empty($_GET['intime'])){
            $this->assign('intime', $_GET['intime']);
        }

        return $this->fetch();
    }
    /*
     * 订单结算
     */
    public function finishlist()
    {
        $this->isAuth();
        $where = "";
        //非超级管理员
        if($_SESSION['role_id']!=ConfigModel::enumroleid1){
            $where .= " and aid=".$_SESSION['admin_id'];
        }
        $where .= " and type=".OrderModel::enumtype1." and status1=".OrderModel::enumstatus1_3;
//        $where .= " and payimage!=''";//淘宝订单截图
        $_GET['merchant_id'] = gett('merchant_id');
        $_GET['shop_name'] = gett('shop_name');
        $_GET['task_id'] = gett('task_id');
        $_GET['mobile'] = gett('mobile');
        $_GET['user_id'] = gett('user_id');
        $_GET['wangwang'] = gett('wangwang');
        $_GET['verifycode'] = gett('verifycode');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        if(!empty($_GET['merchant_id'])){
            $where .= " and merchant_id = '".$_GET['merchant_id']."'";
        }
        if(!empty($_GET['shop_name'])){
            $where .= " and shop_name like '%".$_GET['shop_name']."%'";
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
                'merchant_id'=>$_GET['merchant_id'],
                'shop_name'=>$_GET['shop_name'],
                'mobile'=>$_GET['mobile'],
                'user_id'=>$_GET['user_id'],
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

        $this->assign('merchant_list',MerchantModel::select());
        $this->assign('shop_list',ShopModel::select());
        $this->assign('enum_type_arr',OrderModel::enum_type_arr());
        
    	return $this->fetch();
    }
    /*
     * 任务领取
     */
    public function bindlist()
    {
        $this->isAuth();
        $where = "";
        $day = date("Y-m-d",strtotime("+1 day"));
        $where .= " and worktime >= '".strtotime(date("Y-m-d")." 00:00:00")."'";
        $where .= " and worktime <= '".strtotime($day." 23:59:59")."'";
        $count4 = OrderModel::sumspareorder();//当日剩余单数
        //超级管理员
        if($_SESSION['role_id']==ConfigModel::enumroleid1){
            $where .= " and aid>=0";
        }else{
            $where .= " and aid=".$_SESSION['admin_id'];
        }

        $count1 = OrderModel::count($where." and type=1 and status1 in(2,3)");//领取单数
        $count2 = OrderModel::count($where." and type=1 and status1 in(4)");//完成单数
        $count3 = OrderModel::count($where." and type in(3)");//异常单数
        $where2='';
        $where2 .= " and finishtime >= '".strtotime(date("Y-m-d")." 00:00:00")."'";
        $where2 .= " and finishtime <= '".strtotime($day." 23:59:59")."'";
        if($_SESSION['role_id']==ConfigModel::enumroleid1){
            $where2 .= " and aid>=0";
        }else{
            $where2 .= " and aid=".$_SESSION['admin_id'];
        }
        $tkcount = OrderModel::count("$where2 and type=2 and status1=4");//结算后退款单数


        $this->assign('count1',$count1);
        $this->assign('count2',$count2);
        $this->assign('count3',$count3);
        $this->assign('count4',$count4);
        $this->assign('tkcount',$tkcount);

        $where .= " and type=1 and status1 in(1,2,3,6)";

        $_GET['shop_name'] = gett('shop_name');
        $_GET['mobile'] = gett('mobile');
        $_GET['wangwang'] = gett('wangwang');
        $_GET['verifycode'] = gett('verifycode');
        $_GET['status1'] = gett('status1');
        $_GET['aid'] = gett('aid');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        if(!empty($_GET['shop_name'])){
            $where .= " and shop_name like '%".$_GET['shop_name']."%'";
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
        if(!empty($_GET['status1'])){
            $where .= " and status1 = '".$_GET['status1']."'";
        }
        if(!empty($_GET['aid'])){
            $where .= " and aid = '".$_GET['aid']."'";
        }

        if(!empty($_GET['starttime'])){
            $where .= " and aidtime >= '".strtotime($_GET['starttime'])."'";
        }
        if(!empty($_GET['lasttime'])){
            $where .= " and aidtime <= '".strtotime($_GET['lasttime'])."'";
        }
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'shop_name'=>$_GET['shop_name'],
                'mobile'=>$_GET['mobile'],
                'wangwang'=>$_GET['wangwang'],
                'verifycode'=>$_GET['verifycode'],
                'aid'=>$_GET['aid'],
                'status1'=>$_GET['status1'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime']
            ]
        ];
        $query = OrderModel::tbname()->where("1=1 $where")->order('status1 desc,aidtime asc')->paginate($limit,false,$params);
//        echo OrderModel::tbname()->getLastsql();
//        die;

        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);
        $this->assign('server_list',AdminModel::select("and role_id=2"));//所属客服

//        领取订单总数
        $zongshu=$query->total();
//        var_dump($zongshu);
//        die;
//        客服做单数排名
        // 关联商家表进行查询
        $paiming=OrderModel::paiming();
//     当前小时剩余单数
       $redis= new Redis(config('cache.redis'));
//        $hourordernum=Cache::get('hourordernum');
        $info = $redis->lrange('orderlist', 0, -1);
        $hourordernum =count($info);
        $this->assign('kefu',Cache::get('kefu'.$_SESSION['admin_id']));
        $this->assign('hourordernum',$hourordernum);
        $this->assign('paiming',$paiming);
        $this->assign('zongshu',$zongshu);
    	return $this->fetch();
    }
    /*
     * 客服领今日单
     */
    public function act_pull()
    {
        $auth = $this->control.'-bindlist';
        $this->isAuth($auth);
        
        $day = date("Y-m-d");
        $result = OrderModel::do_pull($day);
        echo $result;
    }
    /*
     * 计算领取单数
     */
//    public function act_jisuan()
//    {
//        $day = date("Y-m-d");
//        $result = OrderModel::do_jisuan($day);
//        echo $result;
//    }
    /*
     * 客服领明日单
     */
    public function act_pull2()
    {
        $auth = $this->control.'-bindlist';
        $this->isAuth($auth);
        
        $day = date("Y-m-d",strtotime("+1 day"));
        $result = OrderModel::do_pull($day);
        echo $result;
    }
    /*
     * 客服领单退回
     */
    public function act_edit_aid()
    {
        $auth = $this->control.'-bindlist';
        $this->isAuth($auth);
        
        $find = OrderModel::find("and id='".postt('id')."'");
        $redis = new Redis(config('cache.redis'));
        $redis->lpush('orderlist',postt('id'));

        if(!empty($find['wangwang'])){
            echo '已派单不可退回';exit;
        }
        
        $data['aid'] = 0;
        $data['wangwang'] = '';
        $data['uptime'] = time();
        $data['aidtime'] = '';
        $data['status1'] = 1;

        $result = OrderModel::edit("and id='".$find['id']."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    /*
     * 退款订单
     */
    public function lists2()
    {
        $this->isAuth();
        $where = " and exceptioninfo !='申请下架'";
        $_GET['merchant_id'] = gett('merchant_id');
        $_GET['shop_name'] = gett('shop_name');
        $_GET['status2'] = gett('status2');
        $_GET['mobile'] = gett('mobile');
        $_GET['wangwang'] = gett('wangwang');
        $_GET['verifycode'] = gett('verifycode');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        $_GET['aid'] = gett('aid');
        $_GET['tid'] = gett('tid');
        if(!empty($_GET['merchant_id'])){
            $where .= " and merchant_id = '".$_GET['merchant_id']."'";
        }
        if(!empty($_GET['shop_name'])){
            $where .= " and shop_name like '%".$_GET['shop_name']."%'";
        }
        if(!empty($_GET['status2'])){
            $where .= " and status2 = '".$_GET['status2']."'";
        }else{
            $where .="and type=2";
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
        //非超级管理员
        if($_SESSION['role_id']==2){
            $_GET['aid']=$_SESSION['admin_id'];
        }
        if($_SESSION['role_id']==5){
            $_GET['tid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['aid'])){
            $where .= " and aid='".$_GET['aid']."'";
        }
        if(!empty($_GET['tid'])){
            $strId = MerchantModel::getStrId($_GET['tid']);
            $where .= " and merchant_id in({$strId})";
        }
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'merchant_id'=>$_GET['merchant_id'],
                'shop_name'=>$_GET['shop_name'],
                'status2'=>$_GET['status2'],
                'mobile'=>$_GET['mobile'],
                'wangwang'=>$_GET['wangwang'],
                'verifycode'=>$_GET['verifycode'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime'],
                'aid'=>$_GET['aid'],
                'tid'=>$_GET['tid'],
            ]
        ];
        $query = OrderModel::tbname()->where("1=1 $where")->order('finishtime desc,id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('merchant_list',MerchantModel::select());
        $this->assign('shop_list',ShopModel::select());
        $this->assign('enum_status2_arr',OrderModel::enum_status2_arr());
        $this->assign('enum_tuikuansh_arr',OrderModel::enum_tuikuansh_arr());
        $this->assign('server_list',AdminModel::select("and role_id=2"));//所属客服
        $this->assign('teacher_list',AdminModel::select("and role_id=5"));//所属业务员
        
    	return $this->fetch();
    }
    /*
     * 下架订单
     */
    public function offlists2()
    {
        $this->isAuth();
        $where = "and type=2 and exceptioninfo='申请下架'";
        $_GET['merchant_id'] = gett('merchant_id');
        $_GET['shop_name'] = gett('shop_name');
        if (gett('status2')){
            $_GET['status2'] = gett('status2');
        }else{
            $_GET['status2']=1;
        }
        $status2=$_GET['status2'];
        $_GET['mobile'] = gett('mobile');
        $_GET['wangwang'] = gett('wangwang');
        $_GET['verifycode'] = gett('verifycode');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        $_GET['aid'] = gett('aid');
        $_GET['tid'] = gett('tid');
        if(!empty($_GET['merchant_id'])){
            $where .= " and merchant_id = '".$_GET['merchant_id']."'";
        }
        if(!empty($_GET['shop_name'])){
            $where .= " and shop_name like '%".$_GET['shop_name']."%'";
        }
        if(!empty($_GET['status2'])){
            $where .= " and status2 = '".$_GET['status2']."'";
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
        //非超级管理员
        if($_SESSION['role_id']==2){
            $_GET['aid']=$_SESSION['admin_id'];
        }
        if($_SESSION['role_id']==5){
            $_GET['tid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['aid'])){
            $where .= " and aid='".$_GET['aid']."'";
        }
        if(!empty($_GET['tid'])){
            $strId = MerchantModel::getStrId($_GET['tid']);
            $where .= " and merchant_id in({$strId})";
        }
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'merchant_id'=>$_GET['merchant_id'],
                'shop_name'=>$_GET['shop_name'],
                'status2'=>$_GET['status2'],
                'mobile'=>$_GET['mobile'],
                'wangwang'=>$_GET['wangwang'],
                'verifycode'=>$_GET['verifycode'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime'],
                'aid'=>$_GET['aid'],
                'tid'=>$_GET['tid'],
            ]
        ];
        $query = OrderModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);


        $this->assign('status2',$status2);
        $this->assign('merchant_list',MerchantModel::select());
        $this->assign('shop_list',ShopModel::select());
        $this->assign('enum_status2_arr',OrderModel::enum_status2_arr());
        $this->assign('server_list',AdminModel::select("and role_id=2"));//所属客服
        $this->assign('teacher_list',AdminModel::select("and role_id=5"));//所属业务员
        
    	return $this->fetch();
    }
    /*
     * 异常订单
     */
    public function lists3()
    {
        $this->isAuth();
        $where = "";
        $_GET['merchant_id'] = gett('merchant_id');
        $_GET['shop_name'] = gett('shop_name');
        $_GET['status3'] = gett('status3');
        $_GET['mobile'] = gett('mobile');
        $_GET['wangwang'] = gett('wangwang');
        $_GET['verifycode'] = gett('verifycode');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        $_GET['aid'] = gett('aid');
        $_GET['tid'] = gett('tid');
        if(!empty($_GET['merchant_id'])){
            $where .= " and merchant_id = '".$_GET['merchant_id']."'";
        }
        if(!empty($_GET['shop_name'])){
            $where .= " and shop_name like '%".$_GET['shop_name']."%'";
        }
        if(!empty($_GET['status3'])){
            $where .= " and status3 = '".$_GET['status3']."'";
        }else{
            $where .= "and type=3";
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
        
        //非超级管理员
        if($_SESSION['role_id']==2){
            $_GET['aid']=$_SESSION['admin_id'];
        }elseif ($_SESSION['role_id']==5){
            $_GET['tid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['aid'])){
            $where .= " and aid='".$_GET['aid']."'";
        }
        if(!empty($_GET['tid'])){
            $strId = MerchantModel::getStrId($_GET['tid']);
            $where .= " and merchant_id in({$strId})";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'merchant_id'=>$_GET['merchant_id'],
                'shop_name'=>$_GET['shop_name'],
                'status3'=>$_GET['status3'],
                'mobile'=>$_GET['mobile'],
                'wangwang'=>$_GET['wangwang'],
                'verifycode'=>$_GET['verifycode'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime'],
                'aid'=>$_GET['aid'],
                'tid'=>$_GET['tid'],
            ]
        ];

        $query = OrderModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('merchant_list',MerchantModel::select());
        $this->assign('shop_list',ShopModel::select());
        $this->assign('enum_status3_arr',OrderModel::enum_status3_arr());
        $this->assign('server_list',AdminModel::select("and role_id=2"));//所属客服
        $this->assign('teacher_list',AdminModel::select("and role_id=5"));//所属业务员
        
    	return $this->fetch();
    }
     /*
     * 订单评价
     */
    public function comment()
    {
        $this->isAuth();
        $where='';
        $_GET['merchant_id'] = gett('merchant_id');
        $_GET['shop_name'] = gett('shop_name');
        $_GET['iscomment'] = gett('iscomment');
        $_GET['mobile'] = gett('mobile');
        $_GET['wangwang'] = gett('wangwang');
        $_GET['verifycode'] = gett('verifycode');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        $_GET['aid'] = gett('aid');
        $_GET['tid'] = gett('tid');
        if(!empty($_GET['merchant_id'])){
            $where .= " and merchant_id = '".$_GET['merchant_id']."'";
        }
        if(!empty($_GET['shop_name'])){
            $where .= " and shop_name like '%".$_GET['shop_name']."%'";
        }
        if(!empty($_GET['iscomment'])){
            $where .= " and iscomment = '".$_GET['iscomment']."'";
        }else{
            $where .= " and iscomment =2";
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
        
        //非超级管理员
        if($_SESSION['role_id']==2){
            $_GET['aid']=$_SESSION['admin_id'];
        }elseif ($_SESSION['role_id']==5){
            $_GET['tid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['aid'])){
            $where .= " and aid='".$_GET['aid']."'";
        }
        if(!empty($_GET['tid'])){
            $strId = MerchantModel::getStrId($_GET['tid']);
            $where .= " and merchant_id in({$strId})";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'merchant_id'=>$_GET['merchant_id'],
                'shop_name'=>$_GET['shop_name'],
                'iscomment'=>$_GET['iscomment'],
                'mobile'=>$_GET['mobile'],
                'wangwang'=>$_GET['wangwang'],
                'verifycode'=>$_GET['verifycode'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime'],
                'aid'=>$_GET['aid'],
                'tid'=>$_GET['tid'],
            ]
        ];
        $query = OrderModel::tbname()->where("1=1 $where")->order('iscomment ASC,comtime DESC,intime desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('merchant_list',MerchantModel::select());
        $this->assign('shop_list',ShopModel::select());
        $this->assign('enum_status3_arr',OrderModel::enum_comment_arr());
        $this->assign('server_list',AdminModel::select("and role_id=2"));//所属客服
        $this->assign('teacher_list',AdminModel::select("and role_id=5"));//所属业务员
        
    	return $this->fetch();
    }
    
    /*
     * 订单详情
     */
    public function view()
    {
        $this->isAuth();
        $find = OrderModel::find("and id=".gett('id'));
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
    /*
     * 旺旺绑定
     */
    public function act_edit_wangwang()
    {
        $auth = $this->control . '-lists';
        $this->isAuth($auth);
        $find = OrderModel::find("and id=" . postt('id'));
//        if($find['status1']==OrderModel::enumstatus1_4){
//            echo '状态不符合';exit;
//        }
        if ($find['status2'] > 0) {
            echo '状态不符合';
            exit;
        }
        if ($find['status3'] > 0) {
            echo '状态不符合';
            exit;
        }
        $wangwang = postt('wangwang');
        if (empty($wangwang)) {
            $data['wangwang'] = '';
            $data['uptime'] = time();
            $data['status1'] = OrderModel::enumstatus1_2;
            OrderModel::edit("and id='" . postt('id') . "'", $data);
            echo "请输入旺旺号";
            exit;
        } else {
            $where = '';
            $where .= " and wangwang='" . $wangwang . "'";
            $where .= " and shop_id='" . $find['shop_id'] . "'";
            $find_order = OrderModel::find($where);
            if ($find_order) {
                echo "此旺旺号已做过此店铺订单";
                exit;
            }
            $shop = ShopModel::find("and id={$find['shop_id']}");
            if($shop['category_id'] >0) {
                $ldata = '';
                $ldata .= " and a.wangwang='" . $wangwang . "'";
                $ldata .= " and b.category_id='" . $shop['category_id'] . "'";
//            $ldata .= " and b.category_id='" . $shop['category_id'] . "'";
                // 关联商家表进行查询
                $join = [
                    ['shop b', 'b.id=a.shop_id'],
                ];
                $count_shop = OrderModel::tbname()->alias('a')->join($join)->field('a.*,b.category_id')->where("1=1 $ldata")->count();
                if ($count_shop > 0) {
                    echo "此旺旺号已做过同类型店铺订单";
                    exit;
                }
            }
            if ($find['status1'] == 2 || $find['status1'] == 6) {
                $data['status1'] = OrderModel::enumstatus1_3;
            }
            $data['wangwang'] = $wangwang;
            $data['uptime'] = time();
            $data['finishtime'] = time();
            $result = OrderModel::edit("and id='" . postt('id') . "'", $data);
            if ($result) {
                echo 'success';
            } else {
                echo '操作失败';
            }
        }
    }
    /*
    * 客服点击打标
    */
    public  function act_edit_dabiao()
    {
        $find = OrderModel::find("and id=" . postt('id'));
        $find_admin = AdminModel::find("and id=" . $_SESSION['admin_id']);
        if (!empty($find_admin['dbusername']) && !empty($find_admin['dbpassword'])) {
            $find_dabiao=Db::table('dabiao')->where("1=1 and order_id={$find['id']}")->find();
            if($find_dabiao['status']==2){
                echo '已打标，请勿重复打标！';
                exit;
            }
            // 关联任务表
            $find_taskdetail = TaskdetailModel::find("and id=" . $find['taskdetail_id']);
            $find_task = TaskModel::find("and id=" . $find['task_id']);
            if ($find_task['istag'] == 2) {
                $find_task['goodsurl'] = $find_task['tagurl'];
                $find_taskdetail['searchkeywords'] =$find_task['tagkeyworks'];
//                $find_taskdetail['searchkeywords'] = urlencode($find_task['tagkeyworks']);
            } else {
                $find_taskdetail['searchkeywords']= $find_taskdetail['searchkeywords'];
//                $find_taskdetail['searchkeywords'] = urlencode($find_taskdetail['searchkeywords']);
            }
            $goods_url = explode('?', $find_task['goodsurl']);
            parse_str($goods_url[1], $myArray);
            //打标网址访问
//            $cookie_file = dirname(__FILE__) . '/wenjian/cookie' . $_SESSION['admin_id'] . '.txt';
//            $url = 'http://bq.danqingseo.com/login.php?action=submit';
//            $data3 = array('mobile' => $find_admin['dbusername'], 'password' => $find_admin['dbpassword']);
//            $header = array();
//            OrderModel::curl_https($url, $data3, $header, 31536000, $cookie_file);
//            $curl = "http://bq.danqingseo.com/index.php?action=submit";
//            $data2 = array(
//                'url' => $myArray['id'],
//                'wangwang' => $find['wangwang'],
//                'keyword' => $find_taskdetail['searchkeywords']
//            );
//            $response2 = OrderModel::curl_https_get($curl, $data2, $header, 31536000, $cookie_file);
//            $res = json_decode($response2, true);
//             $url = "https://www.maoke1688.com/commang/index/dabiao?goodsid={$myArray['id']}&dbkeywords={$find_taskdetail['searchkeywords']}&wangwang={$find['wangwang']}";
            $url ="https://www.dianshangjiaofu.com/api/dabiao/push.html?token=7ab7f0d6c8f4480e67894d6eab8b3904&product_id={$myArray['id']}&keyword={$find_taskdetail['searchkeywords']}&nicks={$find['wangwang']}";
//            $res=json_decode(file_get_contents($url),true);
            $res=json_decode(OrderModel::https_require($url),true);
            if($res['state']=='ture') {
                $lata = [];
                $lata['status'] = 2;
                $lata['uptime'] = time();
                Db::table('dabiao')->where("order_id={$find['id']}")->update($lata);
                echo 'success';
            }else{
                echo '打标失败！';
            }
        }else{
            echo '请填写打标账号密码!';
            exit;
        }
    }
    /*
    * 列表页旺旺修改
    */
    public function act_edit_wangwang2()
    {
        $auth = $this->control . '-lists';
        $this->isAuth($auth);
        $find = OrderModel::find("and id=" . postt('id'));
//        if($find['status1']==OrderModel::enumstatus1_4){
//            echo '状态不符合';exit;
//        }
        if ($find['status2'] > 0) {
            echo '状态不符合';
            exit;
        }
        if ($find['status3'] > 0) {
            echo '状态不符合';
            exit;
        }
        $wangwang = postt('wangwang');
        if (empty($wangwang)) {
            echo "请输入旺旺号";
            exit;
        } else {
            $where = '';
            $where .= " and wangwang='" . $wangwang . "'";
            $where .= " and shop_id='" . $find['shop_id'] . "'";
            $find_order = OrderModel::find($where);
            if ($find_order) {
                echo "此旺旺号已做过此店铺订单";
                exit;
            }
//            $whe = '';
//            $whe .= " and wangwang='" . $wangwang . "'";
//            $whe .= " and status1=4 and type=1";
//            $findwangwang =  OrderModel::tbname()->where("1=1 $whe")->order('finishtime desc')->find();
//            $day = $findwangwang['finishtime']+86400;
//            if($day>time()){
//                $yxtime=date( "Y-m-d H:i:s",$day);
//                echo "此旺旺号做单间隔不超过一天，可在{$yxtime}给单";
//                exit;
//            }
        }
        if ($find['status1'] == 2 || $find['status1'] == 6) {
            $data['status1'] = OrderModel::enumstatus1_3;
        }
        $data['wangwang'] = $wangwang;
        $data['uptime'] = time();
        $data['finishtime'] = time();
        $result = OrderModel::edit("and id='" . postt('id') . "'", $data);
        if ($result) {
         echo 'success';
         } else {
        echo '操作失败';
        }
    }
    /*
     * 实际下单价修改
     */
    public function act_edit_payprice()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        $find = OrderModel::find("and id=".postt('id'));
        if($find['status1']==OrderModel::enumstatus1_4){
            echo '状态不符合';exit;
        }
        if($find['status2']>0){
            echo '状态不符合';exit;
        }
        if($find['status3']>0){
            echo '状态不符合';exit;
        }
        $payprice = postt('payprice');
        if(empty($payprice)){
//            $data['user_id'] = '';
//            $data['mobile'] = '';
            echo "请输入实际下单价";exit;
        }
        $data['payprice'] = $payprice;
        $data['within_price']= WithinModel::get_price($data['payprice']);
        $data['without_price']= MerchantwithoutModel::get_price($find['merchant_id'],$data['payprice']);
        $result = OrderModel::edit("and id='".postt('id')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    /*
     * 佣金修改
     */
    public function act_edit_within_price()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        $find = OrderModel::find("and id=".postt('id'));
        if($find['status1']==OrderModel::enumstatus1_4){
            echo '状态不符合';exit;
        }
        if($find['status2']>0){
            echo '状态不符合';exit;
        }
        if($find['status3']>0){
            echo '状态不符合';exit;
        }

        $within_price = postt('within_price');
        if(empty($within_price)){
//            $data['user_id'] = '';
//            $data['mobile'] = '';
            echo "请输入实际下单价";exit;
        }
        $data['within_price'] = $within_price;
        $result = OrderModel::edit("and id='".postt('id')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function act_find()
    {
        $find = OrderModel::find("and id=".postt('id'));
        echo json_encode($find,JSON_UNESCAPED_UNICODE);exit;
    }
    
    /*
     * 处理退款订单 通过或拒绝
     */
    public function act_edit2()
    {

        $auth = $this->control.'-act_edit_finish';
        $this->isAuth($auth);
        $result = OrderModel::do_edit2($_POST);
        echo $result;exit;
    }
    /*
     * 超级管理员审核退款订单
     */
      public  function  act_tuikuansh(){
          $auth = $this->control.'-act_edit_finish';
          $this->isAuth($auth);
          $result = OrderModel::do_tuikuansh($_POST);
          echo $result;exit;
      }
    /*
     * 异常订单添加
     */
    public function act_edit_yc()
    {
        $auth = $this->control.'-act_edit_finish';
        $this->isAuth($auth);
        $result = OrderModel::do_edit_yc($_POST);
        echo $result;exit;
    }  
    /*
     * 处理异常订单
     */
    public function act_edit3()
    {
        $auth = $this->control.'-act_edit_finish';
        $this->isAuth($auth);
        $result = OrderModel::do_edit3($_POST);
        echo $result;exit;
    } 
    /*
     * 处理后的异常转换为退款订单
     */
    public function act_tuikuan(){
        $auth = $this->control.'-act_edit_finish';
        $this->isAuth($auth);
        $result = OrderModel::do_tuikuan($_POST);
        echo $result;exit;
    }
    /*
     * 处理评论订单
     */
    public function act_comment()
    {
        $auth = $this->control.'-act_edit_finish';
        $this->isAuth($auth);
        $result = OrderModel::do_comment($_POST);
        echo $result;exit;
    }
    /*
     * 删除评价资料
     */
    public function act_del()
    {
        $auth = 'order-act_edit_finish';
        $this->isAuth($auth);
        $orderList = explode(',',postt('id') );
        foreach($orderList as $v){
            OrderModel::do_com_del($v);
        }
        echo 'success';
    }

    /*
     * 拒绝异常订单
     */
    public function act_edit4()
    {
        $auth = $this->control.'-act_edit_finish';
        $this->isAuth($auth);
        $result = OrderModel::do_edit4($_POST);
        echo $result;exit;
    }
    /*
     * 任务完成结算给粉丝 任务已领取未完成结算给商家
     * 正常订单结算 可批量结算
     */
    public function act_edit_finish()
    {
        $this->isAuth();
        $arrId = explode(',',postt('id'));
        $result = OrderModel::do_finish($arrId);
        echo $result;exit;
    } 
    /*
     * 任务完成驳回给粉丝重新修改 
     * 正常订单结算 可批量结算
     */
    public function act_edit_order()
    {
        $this->isAuth();
        $arrId = explode(',',postt('id'));
        $result = OrderModel::do_edit($arrId);
        echo $result;exit;
    }
    /*
     * 退款订单添加-下架
     */
//    public function act_edit_refundoff()
//    {
//        $auth = 'order-act_edit_refundoff';
//        $this->isAuth($auth);
//        $orderList = OrderModel::select("and id in(".postt('id').")");
//        foreach($orderList as $v){
//            $find_task = TaskModel::find("and id={$v['task_id']}");
//            if($v['type']==OrderModel::enumtype1 && in_array($v['status1'],[OrderModel::enumstatus1_1,OrderModel::enumstatus1_6]) && in_array($find_task['status'],[TaskModel::enumstatus2,TaskModel::enumstatus3])){
//                $data = [];
//                $data['type'] = OrderModel::enumtype2;
//                $data['status2'] = OrderModel::enumstatus2_1;
//                $data['exceptioninfo'] = '申请下架';
//                $data['uptime'] = time();
//                OrderModel::edit("and id='".$v['id']."'",$data);
//            }
//        }
//        
//        echo 'success';
//    }

    /*
        * 订单汇总
        */
    public function zuzhang()
    {
        $this->isAuth();
        $where = '';
        $_GET['abc'] = gett('abc');
        $_GET['merchant_id'] = gett('merchant_id');
        $_GET['shop_name'] = gett('shop_name');
        $_GET['task_id'] = gett('task_id');
        $_GET['type'] = gett('type');
        $_GET['mobile'] = gett('mobile');
        $_GET['user_id'] = gett('user_id');
        $_GET['wangwang'] = gett('wangwang');
        $_GET['verifycode'] = gett('verifycode');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        $_GET['aid'] = gett('aid');
        $_GET['tid'] = gett('tid');
        $_GET['limit'] = gett('limit');
        //查出有订单截图的数据
//        if($_SESSION['role_id']>ConfigModel::enumroleid1 && empty($_GET['abc'])){
        if( empty($_GET['abc'])){
//            $where .= " and payimage!=''";//淘宝订单截图
            $where .=" and status1=4";
        }
        if(!empty($_GET['merchant_id'])){
            $where .= " and merchant_id = '".$_GET['merchant_id']."'";
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
        if(empty($_GET['abc'])) {
            $_GET['starttime'] = !empty($_GET['starttime']) ? $_GET['starttime'] : date("Y-m-d");
            $_GET['lasttime'] = !empty($_GET['lasttime']) ? $_GET['lasttime'] : date("Y-m-d");
        }
        if(!empty($_GET['starttime'])){
            $where .= " and finishtime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
        }
        if(!empty($_GET['lasttime'])){
            $where .= " and finishtime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
        }
        if(!empty($_GET['aid'])){
            $where .= " and aid='".$_GET['aid']."'";
        }
        //非超级管理员 业务员
        if ($_SESSION['role_id']==5){
            $_GET['tid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['tid'])){
            $strId = MerchantModel::getStrId($_GET['tid']);
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
                'task_id'=>$_GET['task_id'],
                'type'=>$_GET['type'],
                'mobile'=>$_GET['mobile'],
                'user_id'=>$_GET['user_id'],
                'wangwang'=>$_GET['wangwang'],
                'verifycode'=>$_GET['verifycode'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime'],
                'aid'=>$_GET['aid'],
                'tid'=>$_GET['tid'],
                'limit'=>$_GET['limit'],
            ]
        ];
        $query = OrderModel::tbname()->where("1=1 $where")->order('finishtime desc,id desc')->paginate($limit,false,$params);
//        echo OrderModel::tbname()->getLastsql();
//        die;
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);
        $this->assign('total',$query->total());

        $day = date("Y-m-d");
        $where1='';
        if(empty($_GET['starttime'])){
            $where1 .= " and finishtime >= '".strtotime($day." 00:00:00")."'";
        }
        if(empty($_GET['lasttime'])){
            $where1 .= " and finishtime <= '".strtotime($day." 23:59:59")."'";
        }
        $todypayprice=OrderModel::sum("$where $where1","payprice");//今日下单总金额
        $todywithout_price=OrderModel::sum("$where $where1","without_price");//今日总服务费
        $todywithin_price=OrderModel::sum("$where $where1","within_price");//今日总佣金费
        $todyrefund=OrderModel::sum("$where $where1 and type=2 and status2=3","payprice");//今日退款金额
        $todyrefundwithout_price=OrderModel::sum("$where $where1 and type=2 and status2=3","without_price");//今日退款服务费
        $todyrefundwithin_price=OrderModel::sum("$where $where1 and type=2 and status2=3","within_price");//今日退款佣金
        $this->assign('todypayprice',$todypayprice);
        $this->assign('todywithout_price',$todywithout_price);
        $this->assign('todywithin_price',$todywithin_price);
        $this->assign('todyrefund',$todyrefund);
        $this->assign('todyrefundwithout_price',$todyrefundwithout_price);
        $this->assign('todyrefundwithin_price',$todyrefundwithin_price);

        $this->assign('merchant_list',MerchantModel::select());
        $this->assign('shop_list',ShopModel::select());
        $this->assign('enum_type_arr',OrderModel::enum_type_arr());
        $this->assign('server_list',AdminModel::select("and role_id=2"));//所属客服
        $this->assign('teacher_list',AdminModel::select("and role_id=5"));//所属业务员
        $this->assign('limit',$_GET['limit']);

        return $this->fetch();
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
