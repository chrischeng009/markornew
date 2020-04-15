<?php
namespace app\common\model;

use think\Model;
use think\Db;
use think\cache\driver\Redis;
use think\Cache;
class OrderModel extends Model{
    public static function tbname(){
        return db('order');
    }
    //添加一条记录
    public static function add($data){
        return self::tbname()->insertGetId($data);
    }
    //查询一条记录
    public static function find($where=''){
        return self::tbname()->where("1=1 $where")->order('id desc')->find();
    }
    //查询多条记录
    public static function select($where='',$page=0,$limit=10000){
        return self::tbname()->where("1=1 $where")->order('id desc')->limit($page,$limit)->select();
    }
    //删除一条记录或多条记录
    public static function del($where){
        return self::tbname()->where("1=1 $where")->delete();
    }
    //修改一条记录
    public static function edit($where,$data){
        return self::tbname()->where("1=1 $where")->update($data);
    }
    //统计表的记录条数
    public static function count($where=''){
        return self::tbname()->where("1=1 $where")->count();
    }
    public static function sum($where='',$field){
        return self::tbname()->where("1=1 $where")->sum($field);
    }
    public static function group($where='',$field=''){
        return self::tbname()->where("1=1 $where")->group($field)->order('id asc')->select();
    }
    const enumtype1 = 1;
    const enumtype2 = 2;
    const enumtype3 = 3;
    public static function enum_type_arr()
    {
        $arr[1] = '正常订单';
        $arr[2] = '退款订单';
        $arr[3] = '异常订单';
        return $arr;
    }
    public static function enum_type_text($key)
    {
        $arr = self::enum_type_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    const enumstatus1_1 = 1;
    const enumstatus1_2 = 2;
    const enumstatus1_3 = 3;
    const enumstatus1_4 = 4;
    const enumstatus1_5 = 5;
    const enumstatus1_6 = 6;
    public static function enum_status1_arr()
    {
        $arr[1] = '待领取';
        $arr[2] = '已领取';
        $arr[3] = '已完成';
        $arr[4] = '已结算';
        $arr[5] = '已拒绝';
        $arr[6] = '已取消';
        return $arr;
    }
    public static function enum_status1_text($key)
    {
        $arr = self::enum_status1_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    const enumstatus2_1 = 1;
    const enumstatus2_2 = 2;
    const enumstatus2_3 = 3;
    public static function enum_status2_arr()
    {
        $arr[1] = '待处理';
        $arr[2] = '已拒绝';
        $arr[3] = '已返款';
        $arr[4] = '退款驳回';
        return $arr;
    }
    public static function enum_status2_text($key)
    {
        $arr = self::enum_status2_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    const enumstatus3_1 = 1;
    const enumstatus3_2 = 2;
    public static function enum_status3_arr()
    {
        $arr[1] = '待处理';
        $arr[2] = '已处理';
        $arr[3] = '异常驳回';
        return $arr;
    }
    public static function enum_status3_text($key)
    {
        $arr = self::enum_status3_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    public static function enum_comment_arr()
    {
        $arr[1] = '未评价';
        $arr[2] = '待评价';
        $arr[3] = '已评价';
        $arr[4] = '无法评价';
        $arr[5] = '已删除';
        return $arr;
    }
    public static function enum_comment_text($key)
    {
        $arr = self::enum_comment_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    public static function enum_tuikuansh_arr()
    {
        $arr[1] = '符合';
        $arr[2] = '不符合';
        return $arr;
    }
    public static function enum_tuikuansh_text($key)
    {
        $arr = self::enum_tuikuansh_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    const enumverifytype1 = 1;//核对商品标题
    const enumverifytype2 = 2;//核对店铺标题
    public static function get_verifycode()
    {
        $verifycode = rand(100000,999999);
        return $verifycode;
    }
    //完成金额/数量/已扣服务费/其它
    public static function get_total($task_id)
    {
        $order_list = self::select("and task_id={$task_id} and type in(1,2,3) and status2!=3 and status1=".self::enumstatus1_4);
        $money = 0;
        $num = 0;
        $without_money = 0;
        $within_money = 0;
        foreach($order_list as $v){
            $money = $money+$v['payprice'];
            $num = $num+1;
            $without_money = $without_money+$v['without_price'];
            $within_money = $within_money+$v['within_price'];
        }
        $arr['money'] = $money;
        $arr['num'] = $num;
        $arr['without_money'] = $without_money;
        $arr['within_money'] = $within_money;
        return $arr;
    }
    //结算
    public static function do_finish($arrId){
            foreach($arrId as $id){
                $find = [];
                $find = self::find("and id={$id}");
                if($find['type']!=self::enumtype1){
                    exception("非正常订单不可结算！");
                }
                if(!in_array($find['status1'],[self::enumstatus1_2,self::enumstatus1_3])){
                    exception("已领取或已完成才可结算！");
                }
                //返款给商家
                if($find['status1']==self::enumstatus1_2){
                    self::doFinishMerchant($find);
                }
                //结算给粉丝
                if($find['status1']==self::enumstatus1_3){
                    //扣除客服相应的金额
                    self::doFinishAdminspare($find);
//                    self::doFinishUser($find);
                    self::doFinishMerchantspare($find);

                }
//                //红包状态修改
//                $hongbao = HongbaoModel::find("and order_id={$id}");
//                if($hongbao['status']==1){
//                    $ldata=[];
//                    $ldata['status']=3;
//                    HongbaoModel::edit("and id={$hongbao['id']}",$ldata);
//                }
            }
            //返回
            return "success";
    }
    //驳回
    public static function do_edit($arrId){
            foreach($arrId as $id){
                $find = [];
                $find = self::find("and id={$id}");
                if(!in_array($find['status1'],[self::enumstatus1_2,self::enumstatus1_3])){
                    exception("已领取或已完成才可驳回！");
                }
                $data = [];
                $data['status1'] = self::enumstatus1_2;
                $data['finishtime'] = '';
                self::edit("and id={$find['id']}",$data);
            }
            //返回
            return "success";
    }
    //粉丝任务已完成结算给粉丝
    public static function doFinishUser($find)
    {
        $data = [];
        $data['status1'] = self::enumstatus1_4;
        $data['finishtime'] = time();
        self::edit("and id={$find['id']}",$data);

        $find_shop = ShopModel::find("and id=".$find['shop_id']);
        if(empty($find['user_id'])){
            exception("粉丝不存在");
        }
        $find_user = UserModel::find("and id=".$find['user_id']);
        $find_ordershop = OrdershopModel::find("and user_id={$find['user_id']} and shop_id={$find['shop_id']}");
        if(empty($find_ordershop)){
            $ordsp = [];
            $ordsp['user_id'] = $find['user_id'];
            $ordsp['shop_id'] = $find['shop_id'];
            $ordsp['shop_name'] = $find_shop['name'];
            $ordsp['shop_wangwang'] = $find_shop['wangwang'];
            $ordsp['intime'] = time();
            $ordsp['uptime'] = time();
            OrdershopModel::add($ordsp);
        }

        //粉丝余额变动
        $udata = [];
//        $udata['money'] = $find_user['money']+$find['price']+$find['within_price'];
        $udata['money'] = $find_user['money']+$find['payprice']+$find['within_price'];
        $udata['ordertime'] = time();
        $ordershopname = $find_user['ordershopname'];
        if(empty($find_ordershop)){
            $ordershopname .= $find['shop_name']."（{$find_shop['wangwang']}）";
        }
        $udata['ordershopname'] = $ordershopname;
        UserModel::edit("and id={$find['user_id']}",$udata);

        //记录粉丝财务明细
        $ldata = [];
        $ldata['algorithm'] = 1;//加法
        $ldata['type'] = 2;//本金
        $ldata['user_id'] = $find['user_id'];//粉丝Id
        $ldata['startmoney'] = $find_user['money'];//操作前余额
        $ldata['money'] = $find['price'];//变动金额
        $ldata['endmoney'] = $find_user['money']+$find['payprice'];//操作后金额
        $ldata['order_code'] = $find['verifycode'];//任务编号
        $ldata['tradeno'] = UsermlogsModel::get_tradeno();//流水号
        $ldata['remark'] = "正常结算";//详情
        $ldata['intime'] = time();
        $ldata['uptime'] = time();
        UsermlogsModel::add($ldata);

        //记录粉丝财务明细
        $ldata = [];
        $ldata['algorithm'] = 1;//加法
        $ldata['type'] = 3;//佣金
        $ldata['user_id'] = $find['user_id'];//粉丝Id
        $ldata['startmoney'] = $find_user['money']+$find['payprice'];//操作前余额
        $ldata['money'] = $find['within_price'];//变动金额
        $ldata['endmoney'] = $find_user['money']+$find['price']+$find['within_price'];//操作后金额
        $ldata['order_code'] = $find['verifycode'];//任务编号
        $ldata['tradeno'] = UsermlogsModel::get_tradeno();//流水号
        $ldata['remark'] = "正常结算";//详情
        $ldata['intime'] = time();
        $ldata['uptime'] = time();
        UsermlogsModel::add($ldata);

        $find_task = TaskModel::find("and id={$find['task_id']}");
        $count = self::count("and task_id={$find['task_id']} and status1=".self::enumstatus1_4);
        if($find_task['num']==$count){
            $tdata = [];
            $tdata['status'] = TaskModel::enumstatus6;
            $tdata['uptime'] = time();
            TaskModel::edit("and id='".$find_task['id']."'",$tdata);
        }
    }
    //粉丝任务已领取或未领取结算给商家（返款给商家）
    public static function doFinishMerchant($find)
    {
        $data = [];
        $data['status1'] = self::enumstatus1_4;
        $data['finishtime'] = time();
        self::edit("and id={$find['id']}",$data);

        $find_merchant = MerchantModel::find("and id={$find['merchant_id']}");

        //更新商家余额
        $mdata = [];
        $mdata['money'] = $find_merchant['money']+$find['price']+$find['without_price'];
        MerchantModel::edit("and id={$find['merchant_id']}",$mdata);

        //记录商家财务明细
        $ldata = [];
        $ldata['algorithm'] = 1;//加法
        $ldata['type'] = 8;//退单本金
        $ldata['merchant_id'] = $find['merchant_id'];//商家Id
        $ldata['shop_id'] = $find['shop_id'];//店铺Id
        $ldata['startmoney'] = $find_merchant['money'];//操作前余额
        $ldata['money'] = $find['price'];//变动金额
        $ldata['endmoney'] = $find_merchant['money']+$find['price'];//操作后金额
        $ldata['order_code'] = $find['verifycode'];//任务编号
        $ldata['tradeno'] = "";//流水号
        $ldata['remark'] = "任务编号：{$find['verifycode']}，已领取未完成";//详情
        $ldata['intime'] = time();
        $ldata['uptime'] = time();
        MerchantmlogsModel::add($ldata);

        //记录商家财务明细
        $ldata = [];
        $ldata['algorithm'] = 1;//加法
        $ldata['type'] = 9;//退单服务费
        $ldata['merchant_id'] = $find['merchant_id'];//商家Id
        $ldata['shop_id'] = $find['shop_id'];//店铺Id
        $ldata['startmoney'] = $find_merchant['money']+$find['price'];//操作前余额
        $ldata['money'] = $find['without_price'];//变动金额
        $ldata['endmoney'] = $find_merchant['money']+$find['price']+$find['without_price'];//操作后金额
        $ldata['order_code'] = $find['verifycode'];//任务编号
        $ldata['tradeno'] = "";//流水号
        $ldata['remark'] = "任务编号：{$find['verifycode']}，已领取未完成";//详情
        $ldata['intime'] = time();
        $ldata['uptime'] = time();
        MerchantmlogsModel::add($ldata);
    }
    //已拒绝-已返款
    public static function do_edit2($params){
            $strId = dostr($params['id']);
            $arrId = explode(',',$strId);
            foreach($arrId as $id){
                $find = self::find("and id={$id}");
                if($find['type']!=self::enumtype2){
                    exception("该订单非退款订单！");
                }
                if($find['status2']!=self::enumstatus2_1){
                    exception("该退款状态不符合！");
                }

                //已拒绝
                if($params['status2']==self::enumstatus2_2){
                    $data = [];
                    if($find['status1']==4) {
                        $data['type'] = self::enumtype1;
                        $data['status2'] = 4;
                        $data['exceptioninfo'] = $params['exceptioninfo'];
                    }else{
                        $data['type'] = self::enumtype1;
                        $data['status1'] = self::enumstatus1_1;
                        $data['status2'] = '';
                        $data['exceptioninfo'] = '';
                    }
                    self::edit("and id={$find['id']}",$data);
                    $taskarr=[];
                    $taskarr['status']=3;
                    $taskarr['uptime']=time();
                    TaskModel::edit("and id={$find['task_id']}",$taskarr);

                }

                //已返款
                if($params['status2']==self::enumstatus2_3){
                    $data = [];
                    $data['finishtime'] = time();
                    $data['status2'] = self::enumstatus2_3;
                    if(!empty($params['tuikuansh']) && $params['tuikuansh']>0) {
                        $data['tuikuansh'] = $params['tuikuansh'];
                    }
                    $data['exceptioninfo'] = isset($params['exceptioninfo'])?$params['exceptioninfo']:$find['exceptioninfo'];
                    self::edit("and id={$find['id']}",$data);
                    $find_merchant = MerchantModel::find("and id={$find['merchant_id']}");
                    $find_admin = AdminModel::find("and id={$find['aid']}");
                    //更新商家余额与客服余额
                    $mdata = [];
                    $mdata2 = [];
//                    判断退款订单是否已结算
                    if($find['status1']==4){
                        $mdata['money'] = $find_merchant['money'] + $find['payprice'] + $find['without_price'];
                        //客服余额
                        $mdata2['money'] = $find_admin['money'] + $find['payprice'] + $find['within_price'];
                        AdminModel::edit("and id={$find['aid']}",$mdata2);
                    }else {
                        $mdata['money'] = $find_merchant['money'] + $find['price'] + $find['without_price'];
                    }
                    MerchantModel::edit("and id={$find['merchant_id']}",$mdata);
                    //记录商家财务明细
                    $ldata = [];
                    $ldata['algorithm'] = MerchantmlogsModel::enumalgorithm1;//加法
                    $ldata['type'] = MerchantmlogsModel::enumtype8;//退单本金
                    $ldata['merchant_id'] = $find['merchant_id'];//商家Id
                    $ldata['shop_id'] = $find['shop_id'];//店铺Id
                    $ldata['startmoney'] = $find_merchant['money'];//操作前余额
                    //                    判断退款订单是否已结算
                    if($find['status1']==4) {
                        $ldata['money'] = $find['payprice'];//变动金额
                        $ldata['endmoney'] = $find_merchant['money'] + $find['payprice'];//操作后金额
                    }else{
                        $ldata['money'] = $find['price'];//变动金额
                        $ldata['endmoney'] = $find_merchant['money'] + $find['price'];//操作后金额
                    }
                    $ldata['order_code'] = $find['verifycode'];//任务编号
                    $ldata['tradeno'] = "";//流水号
                    $ldata['remark'] = "任务编号：{$find['verifycode']}";//详情
                    $ldata['intime'] = time();
                    $ldata['uptime'] = time();
                    MerchantmlogsModel::add($ldata);

                    //记录商家财务明细
                    $ldata = [];
                    $ldata['algorithm'] = MerchantmlogsModel::enumalgorithm1;//加法
                    $ldata['type'] = MerchantmlogsModel::enumtype9;//退单服务费
                    $ldata['merchant_id'] = $find['merchant_id'];//商家Id
                    $ldata['shop_id'] = $find['shop_id'];//店铺Id
                    //判断退款订单是否已结算
                    if($find['status1']==4) {
                        $ldata['startmoney'] = $find_merchant['money'] + $find['payprice'];//操作前余额
                        $ldata['endmoney'] = $find_merchant['money'] + $find['payprice'] + $find['without_price'];//操作后金额
                    }else{
                        $ldata['startmoney'] = $find_merchant['money'] + $find['price'];//操作前余额
                        $ldata['endmoney'] = $find_merchant['money'] + $find['price'] + $find['without_price'];//操作后金额
                    }
                    $ldata['money'] = $find['without_price'];//变动金额
                    $ldata['order_code'] = $find['verifycode'];//任务编号
                    $ldata['tradeno'] = "";//流水号
                    $ldata['remark'] = "任务编号：{$find['verifycode']}";//详情
                    $ldata['intime'] = time();
                    $ldata['uptime'] = time();
                    MerchantmlogsModel::add($ldata);
                    //  判断退款订单是否已结算
                    //记录客服财务明细（实际下单价）
                    if($find['status1']==4) {
                        $ldata = [];
                        $ldata['algorithm'] = AdminmlogsModel::enumalgorithm1;//加法
                        $ldata['type'] = AdminmlogsModel::enumtype4;//退单本金
                        $ldata['admin_id'] = $find['aid'];//商家Id
                        $ldata['shop_id'] = $find['shop_id'];//店铺Id
                        $ldata['startmoney'] = $find_admin['money'];//操作前余额
                        $ldata['money'] = $find['payprice'];//变动金额
                        $ldata['endmoney'] = $find_admin['money'] + $find['payprice'];//操作后金额
                        $ldata['order_code'] = $find['verifycode'];//任务编号
                        $ldata['tradeno'] = "";//流水号
                        $ldata['remark'] = "任务编号：{$find['verifycode']}";//详情
                        $ldata['intime'] = time();
                        $ldata['uptime'] = time();
                        AdminmlogsModel::add($ldata);
                        //记录客服财务明细（佣金）
                        $ldata = [];
                        $ldata['algorithm'] = MerchantmlogsModel::enumalgorithm1;//加法
                        $ldata['type'] = MerchantmlogsModel::enumtype5;//退单佣金
                        $ldata['admin_id'] = $find['aid'];//客服Id
                        $ldata['shop_id'] = $find['shop_id'];//店铺Id
                        $ldata['startmoney'] = $find_admin['money'] + $find['payprice'];//操作前余额
                        $ldata['endmoney'] = $find_admin['money'] + $find['payprice'] + $find['within_price'];//操作后金额
                        $ldata['money'] = $find['within_price'];//变动金额
                        $ldata['order_code'] = $find['verifycode'];//任务编号
                        $ldata['tradeno'] = "";//流水号
                        $ldata['remark'] = "任务编号：{$find['verifycode']}";//详情
                        $ldata['intime'] = time();
                        $ldata['uptime'] = time();
                        AdminmlogsModel::add($ldata);
                    }
//
                }
            }
            //返回
            return "success";
    }
    /*
     * 管理员审核退款订单
     */
    public static function  do_tuikuansh($params)
    {
            $id = dostr($params['id']);
            $find = self::find("and id={$id}");
            //异常订单
            $data['tuikuansh'] = $params['tuikuansh'];
            $data['tuikuanshremark'] = $params['tuikuanshremark'];
            $data['uptime'] = time();
            OrderModel::edit("and id='".postt('id')."'",$data);
            Db::commit();
            //返回
            return "success";
    }
//    异常订单添加
    /*
     * 退款、异常订单添加
     */
    public static function  do_edit_yc($params)
    {
        Db::startTrans();
        try{
            $id = dostr($params['id']);
            $find = self::find("and id={$id}");
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
            //异常订单
            $data['status3'] = 1;
            $data['exceptioninfo'] = '发起异常';
            $data['uptime'] = time();
            $data['type'] = 3;
            OrderModel::edit("and id='".postt('id')."'",$data);
            Db::commit();
            //返回
            return "success";
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            return $e->getMessage();//把异常消息捕获并抛出
        }
    }
    //异常订单
    public static function do_edit3($params){
            $id = dostr($params['id']);
            $find = self::find("and id={$id}");
            if($find['type']!=self::enumtype3){
                exception("该订单非异常订单！");
            }
            if($find['status3']!=self::enumstatus3_1){
                exception("状态不符合！");
            }
            $find_merchant = MerchantModel::find("and id={$find['merchant_id']}");
            //客服
            $find_admin = AdminModel::find("and id={$find['aid']}");
            //更新商家余额
            // 订单实付金额小于单价
            $yessparemoney=$find['price']-$find['payprice'];//原本差价
            //服务费减差
            // 差价服务费
            $yes_without_price=MerchantwithoutModel::get_price($find['merchant_id'],$find['price']);
            $pay_without_price=MerchantwithoutModel::get_price($find['merchant_id'],$find['payprice']);
            $without_price=$yes_without_price-$pay_without_price;
            //yuanban
            $mdata1=[];
            $mdata2=[];
            $mdata3=[];
            $mdata4=[];
            if($yessparemoney < 0) {
                // 原本扣除商家的金额 先返还金额
                $mdata1['money'] = $find_merchant['money'] + abs($yessparemoney);
                MerchantModel::edit("and id={$find['merchant_id']}", $mdata1);
                $ldata = [];
                $ldata['algorithm'] = 1;//加法
                $ldata['type'] = 10;//退还多余本金
                $ldata['merchant_id'] = $find['merchant_id'];//商家Id
                $ldata['shop_id'] = $find['shop_id'];//店铺Id
                $ldata['startmoney'] = $find_merchant['money'];//操作前余额
                $ldata['money'] = abs($yessparemoney);//变动金额
                $ldata['endmoney'] = $mdata1['money'];//操作后金额
                $ldata['order_code'] = $find['verifycode'];//任务编号
                $ldata['tradeno'] = "";//流水号
                $ldata['remark'] = "任务编号：{$find['verifycode']}，异常处理前退还金额{$ldata['money']}元";//详情
                $ldata['intime'] = time();
                $ldata['uptime'] = time();
                MerchantmlogsModel::add($ldata);
            }elseif($yessparemoney > 0){
                // 原本返还到商家的金额 先扣除金额
                $mdata1['money'] = $find_merchant['money'] - abs($yessparemoney);
                MerchantModel::edit("and id={$find['merchant_id']}",$mdata1);
                //记录商家财务明细 ;
                $ldata = [];
                $ldata['algorithm'] = 2;//减法
                $ldata['type'] = 11;//减差本金
                $ldata['merchant_id'] = $find['merchant_id'];//商家Id
                $ldata['shop_id'] = $find['shop_id'];//店铺Id
                $ldata['money'] = abs($yessparemoney);//变动金额
                $ldata['order_code'] = $find['verifycode'];//任务编号
                $ldata['tradeno'] = "";//流水号
                $ldata['startmoney'] = $find_merchant['money'];//操作前余额
                $ldata['endmoney'] = $mdata1['money'];//操作后金额
                $ldata['remark'] = "任务编号：{$find['verifycode']}，异常处理前扣除金额{$ldata['money']}元";//详情
                $ldata['intime'] = time();
                $ldata['uptime'] = time();
                MerchantmlogsModel::add($ldata);
            }else{
                $mdata1['money']=$find_merchant['money'];
            }
            if($without_price < 0) {
                //  原本扣除商家的服务费 先返还相应的服务费
                $mdata2['money'] = $mdata1['money'] + abs($without_price);
                MerchantModel::edit("and id={$find['merchant_id']}", $mdata2);
                $ldata = [];
                $ldata['algorithm'] = 1;//加法
                $ldata['type'] = 13;//退还多余服务费
                $ldata['merchant_id'] = $find['merchant_id'];//商家Id
                $ldata['shop_id'] = $find['shop_id'];//店铺Id
                $ldata['startmoney'] = $mdata1['money'];//操作前余额
                $ldata['money'] = abs($without_price);//变动金额
                $ldata['endmoney'] = $mdata2['money'];//操作后金额
                $ldata['order_code'] = $find['verifycode'];//任务编号
                $ldata['tradeno'] = "";//流水号
                $ldata['remark'] = "任务编号：{$find['verifycode']}，异常处理前退还服务费{$ldata['money']}元";//详情
                $ldata['intime'] = time();
                $ldata['uptime'] = time();
                MerchantmlogsModel::add($ldata);
            }elseif($without_price > 0){
                // 原本返还到商家的服务费 先扣除相应的服务费
                $mdata2['money'] = $find_merchant['money'] - abs($without_price);
                MerchantModel::edit("and id={$find['merchant_id']}",$mdata2);
                //记录商家财务明细 ;
                $ldata = [];
                $ldata['algorithm'] = 2;//减法
                $ldata['type'] = 14;//减差服务费
                $ldata['merchant_id'] = $find['merchant_id'];//商家Id
                $ldata['shop_id'] = $find['shop_id'];//店铺Id
                $ldata['money'] = abs($without_price);//变动金额
                $ldata['order_code'] = $find['verifycode'];//任务编号
                $ldata['tradeno'] = "";//流水号
                $ldata['startmoney'] = $mdata1['money'];//操作前余额
                $ldata['endmoney'] = $mdata2['money'];//操作后金额
                $ldata['remark'] = "任务编号：{$find['verifycode']}，异常处理前扣除金额{$ldata['money']}元";//详情
                $ldata['intime'] = time();
                $ldata['uptime'] = time();
                MerchantmlogsModel::add($ldata);
            }else{
                $mdata2['money']=$mdata1['money'];
            }

            $nowsparemoney=$find['price']-$params['payprice'];//修改异常后商家端差价
            $nowadminsparemoney=$find['payprice']-$params['payprice'];//修改异常后客服端差价
            // 差价服务费
            $yes_without_price2=MerchantwithoutModel::get_price($find['merchant_id'],$find['price']);
            $pay_without_price2=MerchantwithoutModel::get_price($find['merchant_id'],$params['payprice']);
            $without_price2=$yes_without_price2-$pay_without_price2;
            //差价佣金
            $yes_within_price=WithinModel::get_price($find['payprice']);
            $pay_within_price=WithinModel::get_price($params['payprice']);
            $within_price=$yes_within_price-$pay_within_price;
            if($nowsparemoney < 0) {//  扣除差价
                $mdata3['money'] = $mdata2['money']+$nowsparemoney+$without_price2;
                $no_without_price=$mdata2['money']+$nowsparemoney;
                MerchantModel::edit("and id={$find['merchant_id']}",$mdata3);
                //记录商家财务明细=》本金
                $ldata = [];
                $ldata['algorithm'] = 2;//减法
                $ldata['type'] = 11;//减差本金
                $ldata['merchant_id'] = $find['merchant_id'];//商家Id
                $ldata['shop_id'] = $find['shop_id'];//店铺Id
                $ldata['money'] = abs($nowsparemoney);//变动金额
                $ldata['order_code'] = $find['verifycode'];//任务编号
                $ldata['tradeno'] = "";//流水号
                $ldata['startmoney'] = $mdata2['money'];//操作前余额
                $ldata['endmoney'] = $no_without_price;//操作后金额
                $ldata['remark'] = "任务编号：{$find['verifycode']}，异常处理后差金额{$ldata['money']}元";//详情
                $ldata['intime'] = time();
                $ldata['uptime'] = time();
                MerchantmlogsModel::add($ldata);
                //记录商家财务明细=》服务费
                if($without_price2 !=0) {
                    $ldata = [];
                    $ldata['algorithm'] = 2;//减法
                    $ldata['type'] = 13;//减差服务费
                    $ldata['merchant_id'] = $find['merchant_id'];//商家Id
                    $ldata['shop_id'] = $find['shop_id'];//店铺Id
                    $ldata['money'] = abs($without_price2);//变动金额
                    $ldata['order_code'] = $find['verifycode'];//任务编号
                    $ldata['tradeno'] = "";//流水号
                    $ldata['startmoney'] = $no_without_price;//操作前余额
                    $ldata['endmoney'] = $mdata3['money'];//操作后金额
                    $ldata['remark'] = "任务编号：{$find['verifycode']}，异常处理后服务费{$ldata['money']}元";//详情
                    $ldata['intime'] = time();
                    $ldata['uptime'] = time();
                    MerchantmlogsModel::add($ldata);
                }
            }elseif($nowsparemoney > 0){//返回差价
                $mdata3['money'] = $mdata2['money']+$nowsparemoney+$without_price2;
                $no_without_price=$mdata2['money']+$nowsparemoney;
                MerchantModel::edit("and id={$find['merchant_id']}",$mdata3);
                //商家端=》本金
                $ldata = [];
                $ldata['algorithm'] = 1;//加法
                $ldata['type'] = 10;//退还多余本金
                $ldata['merchant_id'] = $find['merchant_id'];//商家Id
                $ldata['shop_id'] = $find['shop_id'];//店铺Id
                $ldata['startmoney'] = $mdata2['money'];//操作前余额
                $ldata['money'] = abs($nowsparemoney);//变动金额
                $ldata['endmoney'] =  $no_without_price;//操作后金额
                $ldata['order_code'] = $find['verifycode'];//任务编号
                $ldata['tradeno'] = "";//流水号
                $ldata['remark'] = "任务编号：{$find['verifycode']}，异常处理后退还金额{$ldata['money']}元";//详情
                $ldata['intime'] = time();
                $ldata['uptime'] = time();
                MerchantmlogsModel::add($ldata);
                if($without_price2 !=0) {
                    //商家端=》服务费
                    $ldata = [];
                    $ldata['algorithm'] = 1;//加法
                    $ldata['type'] = 13;//退还多余服务费
                    $ldata['merchant_id'] = $find['merchant_id'];//商家Id
                    $ldata['shop_id'] = $find['shop_id'];//店铺Id
                    $ldata['startmoney'] = $no_without_price;//操作前余额
                    $ldata['money'] = abs($without_price2);//变动金额
                    $ldata['endmoney'] = $mdata3['money'];//操作后金额
                    $ldata['order_code'] = $find['verifycode'];//任务编号
                    $ldata['tradeno'] = "";//流水号
                    $ldata['remark'] = "任务编号：{$find['verifycode']}，异常处理后退还服务费{$ldata['money']}元";//详情
                    $ldata['intime'] = time();
                    $ldata['uptime'] = time();
                    MerchantmlogsModel::add($ldata);
                }
            }
            if($nowadminsparemoney < 0) {//  扣除差价
                //客服余额
                $mdata4['money'] = $find_admin['money']+ $nowadminsparemoney+$within_price;
                $no_within_price=$find_admin['money']+ $nowadminsparemoney;
                AdminModel::edit("and id={$find['aid']}",$mdata4);
                //记录客服财务明细 =》本金;
                $ldata = [];
                $ldata['algorithm'] = 2;//减法
                $ldata['type'] = 6;//减差本金
                $ldata['admin_id'] = $find['aid'];//客服Id
                $ldata['shop_id'] = $find['shop_id'];//店铺Id
                $ldata['money'] = abs($nowadminsparemoney);//变动金额
                $ldata['order_code'] = $find['verifycode'];//任务编号
                $ldata['tradeno'] = "";//流水号
                $ldata['startmoney'] = $find_admin['money'];//操作前余额
                $ldata['endmoney'] = $no_within_price;//操作后金额
                $ldata['remark'] = "任务编号：{$find['verifycode']}，异常处理后差金额{$ldata['money']}元";//详情
                $ldata['intime'] = time();
                $ldata['uptime'] = time();
                AdminmlogsModel::add($ldata);
                if($within_price !=0) {
                    //记录客服财务明细 =》佣金;
                    $ldata = [];
                    $ldata['algorithm'] = 2;//减法
                    $ldata['type'] = 9;//减差佣金
                    $ldata['admin_id'] = $find['aid'];//客服Id
                    $ldata['shop_id'] = $find['shop_id'];//店铺Id
                    $ldata['money'] = abs($within_price);//变动金额
                    $ldata['order_code'] = $find['verifycode'];//任务编号
                    $ldata['tradeno'] = "";//流水号
                    $ldata['startmoney'] = $no_within_price;//操作前余额
                    $ldata['endmoney'] = $mdata4['money'];//操作后金额
                    $ldata['remark'] = "任务编号：{$find['verifycode']}，异常处理后差佣金{$ldata['money']}元";//详情
                    $ldata['intime'] = time();
                    $ldata['uptime'] = time();
                    AdminmlogsModel::add($ldata);
                }
            }elseif($nowadminsparemoney > 0){//返回差价
                //客服余额
                $mdata4['money'] = $find_admin['money']+ $nowadminsparemoney+$within_price;
                $no_within_price=$find_admin['money']+ $nowadminsparemoney;
                AdminModel::edit("and id={$find['aid']}",$mdata4);
                //客服=>本金
                $ldata = [];
                $ldata['algorithm'] = 1;//加法
                $ldata['type'] = 6;//退还多余本金
                $ldata['admin_id'] = $find['aid'];//客服Id
                $ldata['shop_id'] = $find['shop_id'];//店铺Id
                $ldata['startmoney'] = $find_admin['money'];//操作前余额
                $ldata['money'] = $nowadminsparemoney;//变动金额
                $ldata['endmoney'] =  $no_within_price;//操作后金额
                $ldata['order_code'] = $find['verifycode'];//任务编号
                $ldata['tradeno'] = "";//流水号
                $ldata['remark'] = "任务编号：{$find['verifycode']}，异常处理后退还金额{$ldata['money']}元";//详情
                $ldata['intime'] = time();
                $ldata['uptime'] = time();
                AdminmlogsModel::add($ldata);
                if($within_price !=0) {
                    //客服=》佣金
                    $ldata = [];
                    $ldata['algorithm'] = 1;//加法
                    $ldata['type'] = 10;//退还多余佣
                    $ldata['admin_id'] = $find['aid'];//客服Id
                    $ldata['shop_id'] = $find['shop_id'];//店铺Id
                    $ldata['startmoney'] = $no_within_price;//操作前余额
                    $ldata['money'] = abs($within_price);//变动金额
                    $ldata['endmoney'] =  $mdata4['money'];//操作后金额
                    $ldata['order_code'] = $find['verifycode'];//任务编号
                    $ldata['tradeno'] = "";//流水号
                    $ldata['remark'] = "任务编号：{$find['verifycode']}，异常处理后退还佣金{$ldata['money']}元";//详情
                    $ldata['intime'] = time();
                    $ldata['uptime'] = time();
                    AdminmlogsModel::add($ldata);
                }
            }

            $data['finishtime'] = time();
            $data['status3'] = self::enumstatus3_2;
            $data['payprice'] = $params['payprice'];
            $data['wangwang'] = $params['wangwang'];
            $data['without_price'] = $pay_without_price2;
            $data['uptime'] = time();
            $data['exceptioninfo'] = $params['exceptioninfo'];
            self::edit("and id={$id}",$data);
//            Db::commit();
            //返回
            return "success";
//        }catch(\Exception $e){
//            Db::rollback();
//            //exception($e);//直接报调试模式错误信息
//            return $e->getMessage();//把异常消息捕获并抛出
//        }
    }
    //处理后的异常订单转换为退款订单
    public static function do_tuikuan($params){
        Db::startTrans();
        try{
            $id = dostr($params['id']);
            $data['uptime'] = time();
            $data['finishtime'] = time();
            $data['status3'] = 0;
            $data['type'] = 2;
            $data['status2'] = 1;
            $data['exceptioninfo'] = '异常单转退款单';
            self::edit("and id={$id}",$data);
            Db::commit();
            //返回
            return "success";
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            return $e->getMessage();//把异常消息捕获并抛出
        }
    }

    //评价订单--拒绝操作
    public static function do_comment($params){
        Db::startTrans();
        try{
            $id = dostr($params['id']);
            $find = self::find("and id={$id}");
            $data['comtime'] = time();
            if($params['iscomment']==1){
                $data['comment']='';
                $picarr=Db::table('adminaccountimg')->where("account_id={$params['id']}")->select();
                foreach ($picarr as $v){
                    $url=$_SERVER["DOCUMENT_ROOT"].$v['picpath'];
                    unlink($url);
                    Db::table('adminaccountimg')->delete("id={$v['id']}");
                }
                if(!empty($params['video'])) {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $find['comvideo']);
                    $data['comvideo'] = '';
                }
            }
            $data['iscomment'] = $params['iscomment'];
            if(!empty($params['commentremark'])) {
                $data['commentremark'] = $params['commentremark'];
            }
            self::edit("and id={$id}",$data);
            Db::commit();
            //返回
            return "success";
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            return $e->getMessage();//把异常消息捕获并抛出
        }
    }
//    评价图片删除
    public static function do_com_del($orderid){
        $data=[];
        $find = self::find("and id=$orderid");
        $picarr=Db::table('ordercommentimg')->where("order_id={$orderid}")->select();
        foreach ($picarr as $v){
            $url=$_SERVER["DOCUMENT_ROOT"].$v['picpath'];
            @unlink($url);
            $res=Db::table('ordercommentimg')->where("id={$v['id']} ")->delete();
        }
        if(!empty($find['comvideo'])) {
            @unlink($_SERVER["DOCUMENT_ROOT"] . $find['comvideo']);
            $data['comvideo'] = '';
        }
        self::edit("and id={$orderid}",$data);
    }
    //异常订单--拒绝操作
    public static function do_edit4($params){
        Db::startTrans();
        try{
            $id = dostr($params['id']);
            $find = self::find("and id={$id}");
            if($find['type']!=self::enumtype3){
                exception("该订单非异常订单！");
            }
            if($find['status3']!=self::enumstatus3_1){
                exception("状态不符合！");
            }
            $data['status3'] = 3;
            $data['type'] = 1;
            $data['uptime'] = time();
            $data['exceptioninfo'] = $params['exceptioninfo'];
            self::edit("and id={$id}",$data);
            Db::commit();
            //返回
            return "success";
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            return $e->getMessage();//把异常消息捕获并抛出
        }
    }
    //粉丝端异常确认
    public static function do_exceptask($find){
        Db::startTrans();
        try{
            //退款订单
            if($find['type']==self::enumtype2){
                $data = [];
                $data['finishtime'] = time();
                $data['status2'] = self::enumstatus2_3;
                self::edit("and id={$find['id']}",$data);
            }

            //异常订单
            if($find['type']==self::enumtype3){
                $data['finishtime'] = time();
                $data['status3'] = self::enumstatus3_2;
                $data['uptime'] = time();
                self::edit("and id={$find['id']}",$data);
            }

            //查询商家
            $find_merchant = MerchantModel::find("and id={$find['merchant_id']}");

            //更新商家余额
            $mdata = [];
            $mdata['money'] = $find_merchant['money']+$find['price']+$find['without_price'];
            MerchantModel::edit("and id={$find['merchant_id']}",$mdata);

            //记录商家财务明细
            $ldata = [];
            $ldata['algorithm'] = 1;//加法
            $ldata['type'] = 8;//退单本金
            $ldata['merchant_id'] = $find['merchant_id'];//商家Id
            $ldata['shop_id'] = $find['shop_id'];//店铺Id
            $ldata['startmoney'] = $find_merchant['money'];//操作前余额
            $ldata['money'] = $find['price'];//变动金额
            $ldata['endmoney'] = $find_merchant['money']+$find['price'];//操作后金额
            $ldata['order_code'] = $find['verifycode'];//任务编号
            $ldata['tradeno'] = "";//流水号
            $ldata['remark'] = "任务编号：{$find['verifycode']}";//详情
            $ldata['intime'] = time();
            $ldata['uptime'] = time();
            MerchantmlogsModel::add($ldata);

            //记录商家财务明细
            $ldata = [];
            $ldata['algorithm'] = 1;//加法
            $ldata['type'] = 9;//退单服务费
            $ldata['merchant_id'] = $find['merchant_id'];//商家Id
            $ldata['shop_id'] = $find['shop_id'];//店铺Id
            $ldata['startmoney'] = $find_merchant['money']+$find['price'];//操作前余额
            $ldata['money'] = $find['without_price'];//变动金额
            $ldata['endmoney'] = $find_merchant['money']+$find['price']+$find['without_price'];//操作后金额
            $ldata['order_code'] = $find['verifycode'];//任务编号
            $ldata['tradeno'] = "";//流水号
            $ldata['remark'] = "任务编号：{$find['verifycode']}";//详情
            $ldata['intime'] = time();
            $ldata['uptime'] = time();
            MerchantmlogsModel::add($ldata);

            Db::commit();
            //返回
            return "success";
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            return $e->getMessage();//把异常消息捕获并抛出
        }
    }
    //任务领取今日单或明日单
    public static function do_pull($day){
//        Db::startTrans();
//        try{
            $t1 = microtime(true);
            $adminArr = AdminModel::select("and iswork=".ConfigModel::enumIsnot2);
            if(empty($adminArr)){
                exception("客服任务领取未开启！");
            }
            $aidArr = array_column($adminArr,'id');
            if(!in_array($_SESSION['admin_id'],$aidArr)){
                exception("您今天暂未开启任务领取！");
            }
            if(empty($_COOKIE['order'])) {
                if (empty(Cache::get('adminid2'))) {
                    $redis = new Redis(config('cache.redis'));
                    Cache::set('adminid2','666',60);
                    $info = $redis->lrange('orderlist', 0, -1);
                    $num = 0;
                    $suc = 0;
                    $once = 8;
                    $lqwhere = '';
                    $lqwhere .= " and aidtime >= '" . strtotime($day . " 00:00:00") . "'";
                    $lqwhere .= " and aidtime <= '" . strtotime($day . " 23:59:59") . "'";
                    $lqsl = self::count(" and aid={$_SESSION['admin_id']} and type=1 and status1 in(2,3) $lqwhere");
                    $shu = 80 - $lqsl;
                    $hourordernum=count($info);
                    if ($shu <= 0) {
                        echo "预留单不能超过80单";
                        exit;
                    }
                    if ($shu <= $once) {
                        $once = $shu;
                    }
                    foreach ($info as $order_id) {
                        $num++;
                        if ($num <= $once) {
                            $suc++;
                            $findorder = self::find("and id={$order_id}");
                            $within_price = WithinModel::get_price($findorder['price']);
                            $orres=self::edit("and id={$order_id}", ['aid' => $_SESSION['admin_id'], 'aidtime' => time(), 'status1' => 2, 'payprice' => $findorder['price'], 'within_price' => $within_price]);
//                    self::tbname()->where("1=1 and id={$order_id}")->lock('for update')->update(['aid' => $_SESSION['admin_id'], 'aidtime' => time(), 'status1' => 2, 'payprice' => $findorder['price'], 'within_price' => $within_price]);
                            if($orres){
                                $redis->lpop('orderlist');
                            }
                        }
                    }
                    //            $redis->set('hourordernum',$hourordernum-$suc,600);
                    Cache::set('hourordernum',$hourordernum-$suc,600);
//                    Db::commit();
                    Cache::rm('adminid2');
//            $t2 = microtime(true);
//            echo '耗时'.round($t2-$t1,3).'秒<br>';
                    //返回
                    if($suc>0){
                        setcookie("order", "1232", time()+5);
                        Cache::set('kefu'.$_SESSION['admin_id'],'123',5);
                        return "成功领取任务{$suc}单";
                    }else{
//                setcookie("order", "1232", time()+60);
                        echo  "暂无任务领取";
                        exit;
                    }
                }
            else {
                echo "您暂时无法领取任务，请稍等一伙！";
                exit;
            }
        }else{
                echo "您已领取任务，请5秒后领取！";
                exit;
        }
//        }catch(\Exception $e){
//            Db::rollback();
//            //exception($e);//直接报调试模式错误信息
////             file_put_contents($txturl,'');
////            $redis->rm('adminid');
//            Cache::set('adminid2','',5);
//            return $e->getMessage();//把异常消息捕获并抛出
//        }
    }
    //任务领取今日单或明日单
    public static function do_jisuan(){
//        Db::startTrans();
//        try{
            $t1 = microtime(true);
            //已设置任务领取的管理员
            $day = date("Y-m-d");
            $redis= new Redis(config('cache.redis'));
//            $txturl= dirname(__FILE__).'/order.txt';

            if(empty($_COOKIE['order'])) {
                if (empty(Cache::get('adminid'))){
//                $_SESSION['order_rece']='suc';
//                    file_put_contents($txturl, 'order');
//                    $redis->set('adminid','lzfcjs',30);
                    // Cache::set('adminid','lzfcjs',30);
                    $where = " and status=" . TaskModel::enumstatus3;
                    $where .= " and worktime >= '" . strtotime($day . " 00:00:00") . "'";
                    $where .= " and worktime <= '" . strtotime($day . " 23:59:59") . "'";
                    $where .= " and worktype=1";
                    $task_list = TaskModel::tbname()->where("1=1 $where")->order(" istime desc,id desc")->lock('lock in share mode')->select();
                    $hour = date("H");
                    $orderIdArr = [];
                    foreach ($task_list as $v) {
//                判断任务是否按照时间段派单
                        if ($v['istime'] == 2) {
                            $workTimeArr = MerchantModel::getWorkTime($v['merchant_id']);//取商家上班下班时间
                            $taskdetailarr = TaskdetailModel::select(" and task_id={$v['id']}");
                            foreach ($taskdetailarr as $k2 => $v2) {
                                if ($hour < 10) {
                                    $hour2 = substr($hour, 1);
                                } else {
                                    $hour2 = $hour;
                                }

                                if(isset($v2["time$hour2"]) && $v2["time$hour2"] > 0) {
                                    //时间段有应该领取单数
                                    // 查询这个时间段领取的任务数量
                                    $odwhere = '';
                                    $odwhere .= " and taskdetail_id={$v2['id']}";
                                    $odwhere .= " and aidtime >= '" . strtotime($day . " $hour:00:00") . "'";
                                    $odwhere .= " and aidtime <= '" . strtotime($day . " $hour:59:59") . "'";
                                    $odwhere .= " and task_id={$v['id']}";
                                    $orderYesArr2 = OrderModel::count("$odwhere  and aid >0");//已领取订单

                                    if ($v2["time$hour2"] > $orderYesArr2) {
                                        if ($orderYesArr2 == 0) {
                                            $limit = $v2["time$hour2"];
                                        } else {
                                            $limit = $v2["time$hour2"] - $orderYesArr2;
                                        }

                                        if ($limit > 0) {
                                            $order_list = self::tbname()->where("1=1 and task_id={$v['id']} and taskdetail_id={$v2['id']} and aid=0")->limit(0, $limit)->lock('lock in share mode')->select();
                                            foreach ($order_list as $vv) {
                                                $orderIdArr[] = $vv['id'];
                                            }
                                        }
                                    }
                                    $find_config = ConfigModel::find();
                                    $temp = explode('-', $find_config['worktime']);
                                    $time = substr($temp[1], 0, -3);
                                    if ($hour >= $time || $hour >=$workTimeArr[1]) {
                                        $limit = 100;
                                    }else{
                                        $limit='';
                                    }
                                    if ($limit > 0) {
                                        $order_list = self::tbname()->where("1=1 and task_id={$v['id']} and taskdetail_id={$v2['id']} and aid=0")->limit(0, $limit)->lock('lock in share mode')->select();
                                        foreach ($order_list as $vv) {
                                            $orderIdArr[] = $vv['id'];
                                        }
                                    }
                                } else {

                                    $find_config = ConfigModel::find();
                                    $temp = explode('-', $find_config['worktime']);
                                    $time = substr($temp[1], 0, -3);
                                    if ($hour >= $time || $hour >=$workTimeArr[1]) {
                                        $limit = 100;
                                    } else {
                                        $limit = '';
                                    }
                                    if ($limit > 0) {
//                    $order_list = OrderModel::select("and task_id={$v['id']} and aid=0 ",0,$limit);
                                        $order_list = self::tbname()->where("1=1 and task_id={$v['id']} and taskdetail_id={$v2['id']} and aid=0")->order("rand()")->limit(0, $limit)->lock('lock in share mode')->select();
                                        foreach ($order_list as $vv) {
                                            $orderIdArr[] = $vv['id'];
                                        }
                                    }
                                }
                            }

                        } else {
                            $odwhere = '';
//                    $mtime = date('H:i', $v['intime']);//商家发布任务通过的时间
//                    $newhour = date("H:29");
                            $workTimeArr = MerchantModel::getWorkTime($v['merchant_id']);//取商家上班下班时间
//                $inHour = date('H',$v['intime']);//商家发布任务的时间
                            $inHour = date('H', $v['worktime']);//商家发布任务的时间
//                    //判断商家发布任务时间是否大于当前时间超过30分钟后
//                    if($mtime>$newhour && date('H', $v['intime'])== $inHour){
//                        $inHour = date('H', $v['worktime'])+1;
//                        $newtasktime['worktime']=strtotime($day." $inHour:05:00");
//                        TaskModel::edit(" and id={$v['id']}",$newtasktime);
//                    }

                            if ($inHour < $workTimeArr[0]) {
//                if($inHour<$workTimeArr[0] || $v['worktype']==TaskModel::enumworktype2){
                                $inHour = $workTimeArr[0];
                            }
                            $workHour = MerchantModel::getWorkHour($v['merchant_id'], $inHour);//取商家一天工作时间并返回小时计算
                            $orderNoArr = OrderModel::select("and task_id={$v['id']} and aid=0");//未领取订单
                            $orderYesArr = OrderModel::select("and task_id={$v['id']} and aid>0");//已领取订单
                            $orderCount = count($orderNoArr) + count($orderYesArr);//该任务总订单数
                            $orderHour = round($orderCount / $workHour);//四舍五入取整-计算一个小时应领订单数
                            if ($orderHour == 0) {
                                $orderHour = 1;
                            }
//                    //判断商家发布任务时间是否大于当前时间超过30分钟后
//                    if($mtime>$newhour){
//                        $orderHour = 0;
//                    }
                            // $orderHour = ceil($orderCount/$workHour);//四舍五入取整-计算一个小时应领订单数
                            //$mustNum = ($hour-$workTimeArr[0]+1)*$orderHour;//些个时间段应领取的总订单数
                            $mustNum = ($hour - $inHour + 1) * $orderHour;//这个时间段应领取的总订单数
                            $limit = $mustNum - count($orderYesArr);//剩余需要领取的单数
                            $find_config = ConfigModel::find();
                            $temp = explode('-', $find_config['worktime']);
                            $time = substr($temp[1], 0, -3);
                            if ($hour >= $time || $hour >=$workTimeArr[1]) {
                                $limit = 100;
                            }
                            if ($limit > 0) {
//                    $order_list = OrderModel::select("and task_id={$v['id']} and aid=0 ",0,$limit);
                                $order_list = self::tbname()->where("1=1 and task_id={$v['id']} and aid=0 ")->limit(0, $limit)->order("rand()")->lock('lock in share mode')->select();
                                foreach ($order_list as $vv) {
                                    $orderIdArr[] = $vv['id'];
                                }
                            }
                   var_dump($v['id'].'+'.$inHour.'+'.$mustNum.'+'.$limit);
                        }
                    }
                    $redis->rm('orderlist');
                    foreach ($orderIdArr as $or) {
                        if(!in_array($or,$redis->lrange('list', 0, -1))) {
                            $redis->lpush('orderlist', $or);
                        }
                    }
                    $hourordernum=count($orderIdArr);
                    Cache::set('hourordernum',$hourordernum,600);
//                    Db::commit();
                    $t2 = microtime(true);
                    echo '耗时'.round($t2-$t1,3).'秒<br>';
                    return 'success';
//            die;
//                    $num = 0;
//                    $suc = 0;
//                    $once = 10;
//                    $lqwhere = '';
//                    $lqwhere .= " and aidtime >= '" . strtotime($day . " 00:00:00") . "'";
//                    $lqwhere .= " and aidtime <= '" . strtotime($day . " 23:59:59") . "'";
//                    $lqsl = self::count(" and aid={$_SESSION['admin_id']} and type=1 and status1 in(2,3) $lqwhere");
//                    $shu = 50 - $lqsl;

//                    if ($shu <= 0) {
//                        return "预留单不能超过50单";
//                    }
//                    if ($shu <= $once) {
//                        $once = $shu;
//                    }
//
//                    foreach ($orderIdArr as $order_id) {
//                        $num++;
//                        if ($num <= $once) {
//                            $suc++;
//                            $findorder = self::find("and id={$order_id}");
//                            $within_price = WithinModel::get_price($findorder['price']);
//                            self::edit("and id={$order_id}", ['aid' => $_SESSION['admin_id'], 'aidtime' => time(), 'status1' => 2, 'payprice' => $findorder['price'], 'within_price' => $within_price]);
////                    self::tbname()->where("1=1 and id={$order_id}")->lock('for update')->update(['aid' => $_SESSION['admin_id'], 'aidtime' => time(), 'status1' => 2, 'payprice' => $findorder['price'], 'within_price' => $within_price]);
//                        }
//                    }
                } else {
                    return "您暂时无法领取任务，请稍等一伙！";
                }
            }else{
                return "您已领取任务，请30秒后领取！";
            }
//            $redis->set('hourordernum',$hourordernum-$suc,600);

//            file_put_contents($txturl,'');
//            $redis->rm('adminid');
//            Cache::rm('adminid');
//            $t2 = microtime(true);
//            echo '耗时'.round($t2-$t1,3).'秒<br>';

            //返回
//            if($suc>0){
//                setcookie("order", "1232", time()+30);
//                Cache::set('kefu'.$_SESSION['admin_id'],'123',30);
//                return "成功领取任务{$suc}单";
//            }else{
////                setcookie("order", "1232", time()+60);
//                return "暂无任务领取";
//            }
//        }catch(\Exception $e){
//            Db::rollback();
//            //exception($e);//直接报调试模式错误信息
////             file_put_contents($txturl,'');
////            $redis->rm('adminid');
////            Cache::set('adminid','',30);
//            return $e->getMessage();//把异常消息捕获并抛出
//        }
    }
    public static function getStrId($task_id)
    {
        $lists = self::select("and task_id={$task_id}");
        $str = "";
        foreach($lists as $v){
            $str .= $v['id'].',';
        }
        return substr($str, 0, -1);
    }

    // 求出标签今日单
//已通过任务
    public static function sumorder($starttime,$lasttime){
        $sumarr="" ;
        $xjsum='';
        $day = date("Y-m-d");
        if(!empty($starttime)){
            $start_time = strtotime($starttime." 00:00:00");
        }else{
            $start_time = strtotime($day." 00:00:00");
        }
        if(!empty($starttime)){
            $last_time = strtotime($lasttime." 23:59:59");
        }else{
            $last_time =strtotime($day." 23:59:59");
        }
        if( $_SESSION['role_id']==5) {
            $strId = MerchantModel::getStrId($_SESSION['admin_id']);
            $xjsum.=$sumarr.= " and a.merchant_id in({$strId})";
        }
        $sumarr .=" and a.intime>={$start_time} and a.intime<={$last_time}";
        $sumarr .=" and b.status in(3,6,7)";
        $sumarr .=" and a.status1 in(1,2,3,4)";
        $sumarr .=" and a.worktype=1";

        // 关联商家表进行查询
        $join = [
            ['task b','b.id=a.task_id'],
        ];
        $sum1=self::tbname()->alias('a')->join($join)->field('a.*,b.status')->where("1=1 $sumarr")->count();

        $xjsum .=" and a.intime>={$start_time} and a.intime<={$last_time}";
        $xjsum .=" and b.status=7";
        $xjsum .=" and a.status1 in(1,2)";
        $xjsum .=" and a.worktype=1";
        $sum2=self::tbname()->alias('a')->join($join)->field('a.*,b.status')->where("1=1 $xjsum")->count();
        $sum=$sum1-$sum2;
        return $sum;
    }

    //粉丝任务完成结算给商家（返款给商家）
    public static function doFinishMerchantspare($find)
    {
        $data = [];
        $data['status1'] = self::enumstatus1_4;
        $data['finishtime'] = time();
        self::edit("and id={$find['id']}",$data);
        $find_shop = ShopModel::find("and id=".$find['shop_id']);
        $where='';
        $where .= " and shop_wangwang='".$find['wangwang']."'";
        $where .=" and shop_id='".$find['shop_id']."'";
        $find_ordershop = OrdershopModel::find($where);
        if(empty($find_ordershop)){
            $ordsp = [];
            $ordsp['user_id'] = $find['user_id'];
            $ordsp['shop_id'] = $find['shop_id'];
            $ordsp['shop_name'] = $find_shop['name'];
            $ordsp['shop_wangwang'] = $find_shop['wangwang'];
            $ordsp['intime'] = time();
            $ordsp['uptime'] = time();
            OrdershopModel::add($ordsp);
        }

        $find_merchant = MerchantModel::find("and id={$find['merchant_id']}");
        //更新商家余额
        $mdata = [];
        // 订单实付金额小于单价

        $sparemoney=$find['price']-$find['payprice'];
        // 差价服务费
        $yes_without_price=MerchantwithoutModel::get_price($find['merchant_id'],$find['price']);
        $pay_without_price=MerchantwithoutModel::get_price($find['merchant_id'],$find['payprice']);
        $without_price=$yes_without_price-$pay_without_price;
        $mdata['money'] = $find_merchant['money'] + $sparemoney+$without_price;
        $nowithout_price = $find_merchant['money'] + $sparemoney;
        MerchantModel::edit("and id={$find['merchant_id']}",$mdata);
        if($sparemoney < 0) {
            //记录商家财务明细
            $ldata = [];
            $ldata['algorithm'] = 2;//减法
            $ldata['type'] = 11;//减差本金
            $ldata['merchant_id'] = $find['merchant_id'];//商家Id
            $ldata['shop_id'] = $find['shop_id'];//店铺Id
            $ldata['money'] = abs($sparemoney);//变动金额
            $ldata['order_code'] = $find['verifycode'];//任务编号
            $ldata['tradeno'] = "";//流水号
            $ldata['startmoney'] = $find_merchant['money'];//操作前余额
            $ldata['endmoney'] = $nowithout_price;//操作后金额
            $ldata['remark'] = "任务编号：{$find['verifycode']}，已结算差金额{$ldata['money']}元";//详情
            $ldata['intime'] = time();
            $ldata['uptime'] = time();

            MerchantmlogsModel::add($ldata);
        }elseif($sparemoney > 0){
            $ldata = [];
            $ldata['algorithm'] = 1;//加法
            $ldata['type'] = 10;//退还多余本金
            $ldata['merchant_id'] = $find['merchant_id'];//商家Id
            $ldata['shop_id'] = $find['shop_id'];//店铺Id
            $ldata['startmoney'] = $find_merchant['money'];//操作前余额
            $ldata['money'] = abs($sparemoney);//变动金额
            $ldata['endmoney'] =  $nowithout_price ;//操作后金额
            $ldata['order_code'] = $find['verifycode'];//任务编号
            $ldata['tradeno'] = "";//流水号
            $ldata['remark'] = "任务编号：{$find['verifycode']}，已结算退还金额{$ldata['money']}元";//详情
            $ldata['intime'] = time();
            $ldata['uptime'] = time();
            MerchantmlogsModel::add($ldata);
        }
        if($without_price < 0) {
            //记录商家财务明细 ;
            $ldata = [];
            $ldata['algorithm'] = 2;//减法
            $ldata['type'] = 13;//减差服务费
            $ldata['merchant_id'] = $find['merchant_id'];//商家Id
            $ldata['shop_id'] = $find['shop_id'];//店铺Id
            $ldata['money'] = abs($without_price);//变动金额
            $ldata['order_code'] = $find['verifycode'];//任务编号
            $ldata['tradeno'] = "";//流水号
            $ldata['startmoney'] = $nowithout_price;//操作前余额
            $ldata['endmoney'] = $mdata['money'];//操作后金额
            $ldata['remark'] = "任务编号：{$find['verifycode']}，已结算差服务费{$ldata['money']}元";//详情
            $ldata['intime'] = time();
            $ldata['uptime'] = time();

            MerchantmlogsModel::add($ldata);
        }elseif($without_price > 0){
            $ldata = [];
            $ldata['algorithm'] = 1;//加法
            $ldata['type'] = 14;//退还多余服务费
            $ldata['merchant_id'] = $find['merchant_id'];//商家Id
            $ldata['shop_id'] = $find['shop_id'];//店铺Id
            $ldata['startmoney'] = $nowithout_price;//操作前余额
            $ldata['money'] = abs($without_price);//变动金额
            $ldata['endmoney'] =  $mdata['money'] ;//操作后金额
            $ldata['order_code'] = $find['verifycode'];//任务编号
            $ldata['tradeno'] = "";//流水号
            $ldata['remark'] = "任务编号：{$find['verifycode']}，已结算退还服务费{$ldata['money']}元";//详情
            $ldata['intime'] = time();
            $ldata['uptime'] = time();

            MerchantmlogsModel::add($ldata);
        }

        $find_task = TaskModel::find("and id={$find['task_id']}");
        $count = self::count("and task_id={$find['task_id']} and status1=".self::enumstatus1_4);
        if($find_task['num']==$count){
            $tdata = [];
            $tdata['status'] = TaskModel::enumstatus6;
            $tdata['uptime'] = time();
            TaskModel::edit("and id='".$find_task['id']."'",$tdata);
        }
    }

    //粉丝任务完成结算客服余额
    public static function doFinishAdminspare($find)
    {
        $find_admin = AdminModel::find("and id={$find['aid']}");
        //更新客服冻结余额
        $mdata = [];
        $mdata['money'] = $find_admin['money'] - $find['payprice']-$find['within_price'];
//        $mdata['freeze_money'] = $find_admin['freeze_money']-$find['payprice']-$find['within_price'];
        AdminModel::edit("and id={$find['aid']}",$mdata);
        //检验此单是否已经结算过
        $day = date("Y-m-d");
        $start_time = strtotime($day." 00:00:00");
        $last_time =strtotime($day." 23:59:59");
        $sumarr='';
        $sumarr .=" and intime>={$start_time} and intime<={$last_time}";
        $checkadminlog=AdminmlogsModel::find(" $sumarr and admin_id={$find_admin['id']} and order_code={$find['verifycode']} ");
        if($checkadminlog){
            $jsdata=[];
            $jsdata['status1'] = self::enumstatus1_4;
            self::edit("and id={$find['id']}",$jsdata);
            echo "此单已经结算，请勿重复结算！";
            exit;
        }

        //记录客服财务明细 ;
        $ldata = [];
        $ldata['algorithm'] = 2;//减法
        $ldata['type'] = 2;//减差本金
        $ldata['admin_id'] = $find_admin['id'];//客服Id
        $ldata['shop_id'] = $find['shop_id'];//店铺Id
        $ldata['money'] = $find['payprice']+$find['within_price'];//变动金额
        $ldata['order_code'] = $find['verifycode'];//任务编号
        $ldata['tradeno'] = "";//流水号
        $ldata['startmoney'] = $find_admin['money'];//操作前余额
        $ldata['endmoney'] = $mdata['money'];//操作后金额
        $ldata['remark'] = "任务编号：{$find['verifycode']}，已结算扣除实际下单价：{$find['payprice']}，佣金：{$find['within_price']}，总金额：{$ldata['money']}";//详情
        $ldata['intime'] = time();
        $ldata['uptime'] = time();
        AdminmlogsModel::add($ldata);
    }
    //剩余需要领取的单数
    public static function sumspareorder(){
        $sumarr="" ;
        $day = date("Y-m-d");
        $start_time = strtotime($day." 00:00:00");
        $last_time = strtotime($day." 23:59:59");
        $sumarr .=" and a.intime>={$start_time} and a.intime<={$last_time}";
        $sumarr .=" and b.status =3";
        $sumarr .=" and a.worktype=1";
        $sumarr .=" and a.aid=0";

        // 关联商家表进行查询
        $join = [
            ['task b','b.id=a.task_id'],
        ];
        $sum=self::tbname()->alias('a')->join($join)->field('a.*,b.status')->where("1=1 $sumarr")->count();
        return $sum;
    }
    public static function ordercommentimg($order_id){
        return  Db::table('ordercommentimg')->where("order_id={$order_id}")->column('picpath');
    }
    //旺旺号打标提交数据
    public static function curl_https_get($curl, $data=array(), $header=array(), $timeout=30,$cookie_file){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查 信任任何证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 0 域名存在与否都不验证了（1检查证书中是否设置域名）
        curl_setopt($ch, CURLOPT_URL, $curl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_COOKIEFILE,  $cookie_file); // 获取cookies

        $response = curl_exec($ch);
        if($error=curl_error($ch)){
            die($error);
        }

        curl_close($ch);
        return $response;
    }
    //
    public static function curl_https($url, $data=array(), $header=array(), $timeout=30,$cookie_file){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查 信任任何证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 0 域名存在与否都不验证了（1检查证书中是否设置域名）
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_file); // 存储cookies

        $response = curl_exec($ch);

        if($error=curl_error($ch)){
            die($error);
        }

        curl_close($ch);
        return $response;
    }
    public static function https_require($url,$header=array(), $timeout=30){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查 信任任何证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 0 域名存在与否都不验证了（1检查证书中是否设置域名）
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $response = curl_exec($ch);

        if($error=curl_error($ch)){
            die($error);
        }

        curl_close($ch);
        return $response;
    }
    static function loseSpace($pcon){
        $pcon = preg_replace("/ /","",$pcon);
        $pcon = preg_replace("/&nbsp;/","",$pcon);
        $pcon = preg_replace("/　/","",$pcon);
        $pcon = preg_replace("/\r\n/","",$pcon);
        $pcon = str_replace(chr(13),"",$pcon);
        $pcon = str_replace(chr(10),"",$pcon);
        $pcon = str_replace(chr(9),"",$pcon);
        return $pcon;
    }
    static function do_dabiao($data){
        $result = Db::table('dabiao')->insertGetId($data);
        return $result;
    }
//    客服做单排名
    static public function paiming(){
        $sumarr="" ;
        $day = date("Y-m-d");
        $start_time = strtotime($day." 00:00:00");
        $last_time = strtotime($day." 23:59:59");
        $sumarr .=" and b.finishtime>={$start_time} and b.finishtime<={$last_time}";
        $sumarr .=" and status1=4";
        $sumarr .=" and type=1";
        $subQuery= Db::table('order')->alias('b')->field('count(b.id)')->where("b.aid=a.id $sumarr")->buildSql();
        $paiming= Db::table('admin')->alias('a')->field("a.*,$subQuery as ordernum")->order('ordernum desc')->limit(0,3)->where("1=1 and role_id=2")->select();
        return $paiming;
    }
}
?>