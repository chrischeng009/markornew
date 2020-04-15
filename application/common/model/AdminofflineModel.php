<?php
namespace app\common\model;

use think\Model;
use think\Db;

class AdminofflineModel extends Model{
    public static function tbname(){
        return db('adminoffline');
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
    public static function select($where=''){
        return self::tbname()->where("1=1 $where")->order('id desc')->select();
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
    //求和
    public static function sum($where='',$field){
        return self::tbname()->where("1=1 $where")->sum($field);
    }
    // 求出当日客服公款金额
    public static function summoney($aid){
        $sumarr="" ;
        $day = date("Y-m-d");
        $start_time = strtotime($day." 00:00:00");
        $last_time = strtotime($day." 23:59:59");
        $sumarr .=" and uptime>={$start_time} and uptime<={$last_time}";
        $sumarr .=" and status = 2";
        //   判断是业务员和客服
        if($aid){
            $sumarr .=" and admin_id ={$aid}";
        }
        $sum=self::sum($sumarr,'money');
        return $sum;
    }
    public static function enum_status_arr()
    {
        $arr[1] = '待处理';
        $arr[2] = '已处理';
        $arr[3] = '已拒绝';
        return $arr;
    }
    public static function enum_status_text($key)
    {
        $arr = self::enum_status_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
   
    /*
     * 申请金额
     */
    public static function do_add($params){
        Db::startTrans();
        try{
            ////数据处理
            $find_admin = AdminModel::find("and id=".$_SESSION['admin_id']);
            $data['admin_id'] = $params['admin_id'];
            $data['admin_money'] = $find_admin['money'];
            $data['money'] = $params['money'];
            $data['remark'] = $params['remark'];
            $data['status'] = 2;//待处理
            $data['intime'] = time();
            $data['uptime'] = time();
            self::add($data);
			
			   $adata = [];
                $adata['money'] = $find_admin['money']-$data['money'];
                AdminModel::edit("and id={$_SESSION['admin_id']}",$adata);
                //记录客服财务明细
                $ldata = [];
                $ldata['algorithm'] = 2;//加法
                $ldata['type'] = 7;//公款申请
                $ldata['admin_id'] = $_SESSION['admin_id'];//客服Id
                $ldata['startmoney'] = $find_admin['money'];//操作前余额
                $ldata['money'] = $data['money'];//变动金额
                $ldata['endmoney'] =  $adata['money'];//操作后金额
                $ldata['order_code'] = "";//订单号
                $ldata['tradeno'] = "";//流水号
                $ldata['remark'] = $data['remark'];//详情
                $ldata['intime'] = time();
                $ldata['uptime'] = time();
                AdminmlogsModel::add($ldata);

            //提交
            Db::commit();
            //返回
            return 'success';
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            //$this->error($e->getMessage());//把异常消息捕获并抛出
            return $e->getMessage();
        }
    }
    /*
     * 处理申请
     */
    public static function do_edit($id,$params)
    {
        Db::startTrans();
        try{
            ////数据处理
            $find = self::find("and id=".$id);
            if($find['status']!=1){
                exception("请勿重处理");
            }

            $find_admin = AdminModel::find("and id=".$find['admin_id']);
            $find_adminoffline = AdminofflineModel::find("and id=".$find['admin_id']);
            $data['status'] = $params['status'];
            $data['uptime'] = time();
            self::edit("and id='".$id."'",$data);

            //已处理
            if($params['status']==2){
                $adata = [];
                $adata['money'] = $find_admin['money']-$find['money'];
                AdminModel::edit("and id={$find['admin_id']}",$adata);
                //记录客服财务明细
                $ldata = [];
                $ldata['algorithm'] = 2;//加法
                $ldata['type'] = 7;//公款申请
                $ldata['admin_id'] = $find['admin_id'];//客服Id
                $ldata['startmoney'] = $find_admin['money'];//操作前余额
                $ldata['money'] = $find['money'];//变动金额
                $ldata['endmoney'] =  $adata['money'];//操作后金额
                $ldata['order_code'] = "";//订单号
                $ldata['tradeno'] = "";//流水号
                $ldata['remark'] = $find_adminoffline['remark'];//详情
                $ldata['intime'] = time();
                $ldata['uptime'] = time();
                AdminmlogsModel::add($ldata);
            }

            //提交
            Db::commit();
            //返回
            return 'success';
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            //$this->error($e->getMessage());//把异常消息捕获并抛出
            return $e->getMessage();
        }
    }







































}
?>