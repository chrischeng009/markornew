<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4
 * Time: 17:07
 */
namespace app\commang\controller;

use think\Controller;
use think\Db;
use app\common\model\MerchantModel;
use app\common\model\ShopModel;
use app\common\model\TaskdetailModel;
use app\common\model\TaskModel;
use app\common\model\ConfigModel;
use app\common\model\UserModel;
use app\common\model\AdminModel;
use app\common\model\OrderModel;//本model放最后方便看清是否弄错
use app\common\model\MerchantmlogsModel;
use app\common\model\GoodsModel;
use app\common\model\HongbaoModel;
use think\cache\driver\Redis;
use think\Cache;
class Index extends Controller
{
    //    运行脚本程序 实现自动下架
    public function index()
    {
        //获取未提交订单截图的订单
        
        $where = '';
        $day = date("Y-m-d");
//        $where .= "  a.payimage IS NULL";
        $where .= "a.type in (1,2)";
        $where .= " and a.status1 in(1,2,3)";
        $where .= " and a.status2 = 0";
        $where .= " and c.status in(1,2,3)";
        $where .= " and c.worktype = 1";
        $where .= " and a.worktime >= '" . strtotime($day . " 00:00:00") . "'";
        $where .= " and a.worktime <= '" . strtotime($day . " 23:59:59") . "'";
        $join = [
            ['task c', 'a.task_id=c.id'],
        ];
        $Nofinishorder = Db::table('order')->alias('a')->join($join)->field('a.*,c.status')->where($where)->order('a.id desc')->select();
//       echo  OrderModel::tbname()->getLastSql();
//        var_dump($Nofinishorder);
//        die;
        if ($Nofinishorder) {
            foreach ($Nofinishorder as $k => $v) {
                $arr = [];
                $taskarr = [];
                $arr["exceptioninfo"] = "系统下架";
                $arr["status2"] = '3';
                $arr["type"] = '2';
//            $arr["id"] = $v["id"];
                $taskarr['status'] = 7;
                $taskarr['uptime'] = time();
                TaskModel::edit("and id='" . $v['task_id'] . "'", $taskarr);
                //任务状态为待审核和已上架
                if ($v['status'] !== 1) {
                    //已返款
                    OrderModel::edit("and id={$v['id']}", $arr);
                    $find_merchant = MerchantModel::find("and id={$v['merchant_id']}");
                    //更新商家余额
                    $mdata = [];
                    $mdata['money'] = $find_merchant['money'] + $v ['price'] + $v['without_price'];
                    //待审核任务
                    if ($v['status'] == 2) {
                        $mdata['freeze_money'] = $find_merchant['freeze_money'] - $v ['price'] - $v['without_price'];
                    }
                    MerchantModel::edit("and id={$v['merchant_id']}", $mdata);

                    //记录商家财务明细
                    $ldata = [];
                    $ldata['algorithm'] = MerchantmlogsModel::enumalgorithm1;//加法
                    $ldata['type'] = MerchantmlogsModel::enumtype8;//退单本金
                    $ldata['merchant_id'] = $v['merchant_id'];//商家Id
                    $ldata['shop_id'] = $v['shop_id'];//店铺Id
                    $ldata['startmoney'] = $find_merchant['money'];//操作前余额
                    $ldata['money'] = $v['price'];//变动金额
                    $ldata['endmoney'] = $find_merchant['money'] + $v['price'];//操作后金额
                    $ldata['order_code'] = $v['verifycode'];//任务编号
                    $ldata['tradeno'] = "";//流水号
                    $ldata['remark'] = "任务编号：{$v['verifycode']}";//详情
                    $ldata['intime'] = time();
                    $ldata['uptime'] = time();
                    MerchantmlogsModel::add($ldata);

                    //记录商家财务明细
                    $ldata = [];
                    $ldata['algorithm'] = MerchantmlogsModel::enumalgorithm1;//加法
                    $ldata['type'] = MerchantmlogsModel::enumtype9;//退单服务费
                    $ldata['merchant_id'] = $v['merchant_id'];//商家Id
                    $ldata['shop_id'] = $v['shop_id'];//店铺Id
                    $ldata['startmoney'] = $find_merchant['money'] + $v['price'];//操作前余额
                    $ldata['money'] = $v['without_price'];//变动金额
                    $ldata['endmoney'] = $find_merchant['money'] + $v['price'] + $v['without_price'];//操作后金额
                    $ldata['order_code'] = $v['verifycode'];//任务编号
                    $ldata['tradeno'] = "";//流水号
                    $ldata['remark'] = "任务编号：{$v['verifycode']}";//详情
                    $ldata['intime'] = time();
                    $ldata['uptime'] = time();
                    MerchantmlogsModel::add($ldata);
                }
            }
        }else{

            $data = '';
            $day = date("Y-m-d");
//        $where .= "  a.payimage IS NULL";
            $data .= " and status =3";
            $data .= " and worktype = 1";
            $data .= " and worktime >= '" . strtotime($day . " 00:00:00") . "'";
            $data .= " and worktime <= '" . strtotime($day . " 23:59:59") . "'";
            $task_find=TaskModel::select("$data");
            foreach ($task_find as $k=>$v){
                $taskarr['status'] = 6;
                $taskarr['uptime'] = time();
                TaskModel::edit(" and id={$task_find['id']}",$taskarr);
            }

        }
        if($this->refund()) {
            return 'success';
        } else {
//                $this->error('下架失败，或者没有可下架订单');
            return 'error';
//                exception("该退款状态不符合！");
        }
    }

// 运行脚本实现自动上架明日单任务今天上架
    public function uptask()
    {
        //获取明日单任务
        $where = '';
        $day = date("Y-m-d", strtotime("-1 day"));
        $where .= " and intime >= '" . strtotime($day . " 00:00:00") . "'";
        $where .= " and intime <= '" . strtotime($day . " 23:59:59") . "'";
        $where .= " and worktype=2";
        $taskarr = TaskModel::select($where);
        if ($taskarr) {
            foreach ($taskarr as $k => $v) {
                $editarr = [];
                $editarr['worktype'] = 1;
                $editarr['worktime'] = time();
                $editarr['intime'] = time();
                TaskModel::edit("and id={$v['id']}", $editarr);
                OrderModel::edit("and task_id={$v['id']}", $editarr);
            }
            return 'success';
        } else {
            return 'false';
        }
    }

    //商家还贷款
    /*
         * 处理还款
         */
    public static function refund()
    {
        Db::startTrans();
        try{
            $find_merchant2 = MerchantModel::select("and loans_money >0");
            if($find_merchant2) {
                foreach ($find_merchant2 as $k => $find_merchant) {
                    $mdata = [];
                    $mdata['money'] = $find_merchant['money'] - $find_merchant['loans_money'];
                    $mdata['loans_money'] = 0;
                    MerchantModel::edit("and id={$find_merchant['id']}", $mdata);
                    //查询商家店铺
                    $find_shop = ShopModel::find("and merchant_id={$find_merchant['id']}");
                    //记录商家财务明细
                    $ldata = [];
                    $ldata['algorithm'] = 2;//减法
                    $ldata['type'] = 4;//还款
                    $ldata['merchant_id'] = $find_merchant['id'];//商家Id
                    $ldata['shop_id'] = isset($find_shop['id']) ? $find_shop['id'] : "";//店铺Id
                    $ldata['startmoney'] = $find_merchant['money'];//操作前余额
                    $ldata['money'] = $find_merchant['loans_money'];//变动金额
                    $ldata['endmoney'] = $find_merchant['money'] - $find_merchant['loans_money'];//操作后金额
                    $ldata['order_code'] = "";//订单号
                    $ldata['tradeno'] = "";//流水
                    $ldata['remark'] = "贷款金额由{$find_merchant['loans_money']}元降到{$mdata['loans_money']}元";//详情
                    $ldata['intime'] = time();
                    $ldata['uptime'] = time();
                    MerchantmlogsModel::add($ldata);
                }
//die;

                //提交
                Db::commit();
                //返回
                return 'success';
            }
            return '操作失败';
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            //$this->error($e->getMessage());//把异常消息捕获并抛出
            return '操作失败';
        }
    }
//    excel表格下载api
    public static function downloadapi()
    {
        Db::startTrans();
        try{
            $name=$_GET['name'];
            if($name) {
                $day = date("Y-m-d");
                $where = '';
                $where .= " and type in (1,3)";
                $where .= " and shop_name like '%" . $name . "%'";
                $where .= " and status1 = 4";
                $where .= " and worktype = 1";
                $where .= " and worktime >= '" . strtotime($day . " 00:00:00") . "'";
                $where .= " and worktime <= '" . strtotime($day . " 23:59:59") . "'";
                $orderarr = OrderModel::select("$where");
                $msg = '';
                if ($orderarr) {
                    $msg['code'] = 1;//数据返回成功码
                    $msg['data'] = $orderarr;
                } else {
                    $msg['code'] = 0;//数据返回失败
                    $msg['data'] = '';
                }
//die
                //提交
                Db::commit();
                //返回
                return json_encode($msg, JSON_UNESCAPED_UNICODE);
            }
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            return $e->getMessage();//把异常消息捕获并抛出
//            return '操作失败';
        }
    }
//    查询店铺api接口
    public static function shopapi()
    {
        Db::startTrans();
        try{
                $day = date("Y-m-d");
                $where = '';
                $where .= " status in (3,6)";
                $where .= " and worktime >= '" . strtotime($day . " 00:00:00") . "'";
                $where .= " and worktime <= '" . strtotime($day . " 23:59:59") . "'";
                $taskarr = TaskModel::tbname()->where("$where")->Distinct(true)->column('shop_name');
                $msg = '';
                if ($taskarr) {
                    $msg['code'] = 1;//数据返回成功码
                    $msg['data'] = $taskarr;
                } else {
                    $msg['code'] = 0;//数据返回失败
                    $msg['data'] = '';
                }
//die
                //提交
                Db::commit();
                //返回
                return json_encode($msg, JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            return $e->getMessage();//把异常消息捕获并抛出
//            return '操作失败';
        }
    }
 
    
    public function del_comment(){
        $where='';
        $where .= " and intime >= '" . strtotime("-1 month" . " 00:00:00") . "'";
        $where .= " and intime <= '" . strtotime("-1 month" . " 23:59:59") . "'";
        $where .= " and iscomment=2";
        $orderList=OrderModel::select("$where");
        foreach($orderList as $v){
            OrderModel::do_com_del($v['id']);
        }
        echo 'success';
    }
//    public function test(){
////        Cache::store('redis')->set('username','lzfcjs',30);
////        return  Cache::get('username');
//        $redis= new Redis(config('cache.redis'));
////        $redis->set('username','lzfcjs',60);
////        var_dump(empty($redis->get('username')));
//        $arr='html';
////       return $redis->get('username' );
//        if( in_array($arr,$redis->lrange('list', 0, -1))){
//        $redis->lpush('list','html');
////        $value = $redis->lpop('list');
//            $info = $redis->lrange('list', 0, -1);
//        return json($info);
//
//    }
    /*
        * 计算领取单数
        */
    public function act_jisuan()
    {

        $day = date("Y-m-d");
        $result = OrderModel::do_jisuan($day);
        echo $result;
    }


}