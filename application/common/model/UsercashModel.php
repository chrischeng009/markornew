<?php
namespace app\common\model;

use think\Model;
use think\Db;

class UsercashModel extends Model{
    public static function tbname(){
        return db('usercash');
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
    // 求出当日的客服旗下的粉丝提现金额
    public static function summoney($aid,$starttime,$lasttime){
        $sumarr="" ;
        $day = date("Y-m-d");
        if(!empty($starttime)){
            $start_time=strtotime($starttime." 00:00:00");

        }else{
            $start_time = strtotime($day." 00:00:00");
        }
        if(!empty($lasttime)){
           $last_time= strtotime($lasttime." 23:59:59");
        }else{
            $last_time = strtotime($day." 23:59:59");
        }
        $sumarr .=" and a.uptime>={$start_time} and a.uptime<={$last_time}";
        $sumarr .=" and a.status = 2";
        if($aid){
            $sumarr .=" and b.aid ={$aid}";
        }
        // 关联商家表进行查询
        $join = [
            ['user b','b.id=a.user_id'],
        ];
        $sum=self::tbname()->alias('a')->join($join)->field('a.*,b.aid')->where("1=1 $sumarr")->sum('a.money');
        return $sum;
    }
    public static function enum_status_arr()
    {
        $arr[1] = '待处理';
        $arr[2] = '已提现';
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
    const enumType1 = 1;
    const enumType2 = 2;
    const enumType3 = 3;
    public static function enum_type_arr()
    {
        return [
            self::enumType1 => '微信',
            self::enumType2 => '支付宝',
            self::enumType3 => '银行卡',
        ];
    }
    public static function enum_type_text($key)
    {
        $arr = self::enum_type_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    /*
     * 提现申请
     */
    public static function do_add($params){
        Db::startTrans();
        try{
            ////数据处理
            $find_user = UserModel::find("and id=".$params['user_id']);
            if($params['type']==self::enumType1){
                if(empty($find_user['weixinmoneycode'])){
                    exception("您未绑定微信收款码");
                }
                $data['weixinmoneycode'] = $find_user['weixinmoneycode'];
            }elseif($params['type']==self::enumType2){
                if(empty($find_user['zfb'])){
                    exception("您未绑定支付宝帐号");
                }
                $data['zfb'] = $find_user['zfb'];
            }elseif($params['type']==self::enumType3){
                if(empty($find_user['bankname'] || empty($find_user['bankcode']))){
                    exception("您未绑定银行帐号");
                }
                $data['bankname'] = $find_user['bankname'];
                $data['bankcode'] = $find_user['bankcode'];
            }else{
                exception("提现方式错误");
            }
            if($params['money']<10){
                exception("提现金额须10元以上");
            }
            if($params['money']>$find_user['money']){
                exception("提现金额超过余额");
            }
            $data['realname'] = $find_user['realname'];
            $data['user_id'] = $params['user_id'];
            $data['type'] = $params['type'];
            $data['money'] = $params['money'];
            $data['status'] = 1;//待处理
            $data['intime'] = time();
            self::add($data);
            
            //余额变动
            $udata['money'] = $find_user['money']-$params['money'];
            $udata['freeze_money'] = $find_user['freeze_money']+$params['money'];
            UserModel::edit("and id={$params['user_id']}",$udata);
            
            //记录粉丝财务明细
            $ldata = [];
            $ldata['algorithm'] = 2;//减法
            $ldata['type'] = 1;//提现
            $ldata['user_id'] = $params['user_id'];//粉丝Id
            $ldata['startmoney'] = $find_user['money'];//操作前余额
            $ldata['money'] = $params['money'];//变动金额
            $ldata['endmoney'] = $find_user['money']-$params['money'];//操作后金额
            $ldata['order_code'] = "";//订单号
            $ldata['tradeno'] = UsermlogsModel::get_tradeno();//流水号
            $ldata['remark'] = "";//详情
            $ldata['intime'] = time();
            $ldata['uptime'] = time();
            UsermlogsModel::add($ldata);
            
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
     * 处理提现
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
            
            $find_user = UserModel::find("and id=".$find['user_id']);
            $data['status'] = $params['status'];
            $data['uptime'] = time();
            self::edit("and id='".$id."'",$data);
            
            //已提现
            if($params['status']==2){
                $udata = [];
                $udata['freeze_money'] = $find_user['freeze_money']-$find['money'];
                UserModel::edit("and id={$find['user_id']}",$udata);
            }
            
            //已拒绝
            if($params['status']==3){
                //余额变动
                $udata = [];
                $udata['money'] = $find_user['money']+$find['money'];
                $udata['freeze_money'] = $find_user['freeze_money']-$find['money'];
                UserModel::edit("and id={$find['user_id']}",$udata);
                //记录粉丝财务明细
                $ldata = [];
                $ldata['algorithm'] = 1;//加法
                $ldata['type'] = 4;//提现拒绝
                $ldata['user_id'] = $find['user_id'];//粉丝Id
                $ldata['startmoney'] = $find_user['money'];//操作前余额
                $ldata['money'] = $find['money'];//变动金额
                $ldata['endmoney'] = $find_user['money']+$find['money'];//操作后金额
                $ldata['order_code'] = "";//订单号
                $ldata['tradeno'] = UsermlogsModel::get_tradeno();//流水号
                $ldata['remark'] = "";//详情
                $ldata['intime'] = time();
                $ldata['uptime'] = time();
                UsermlogsModel::add($ldata);
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