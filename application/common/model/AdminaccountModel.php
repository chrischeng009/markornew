<?php
namespace app\common\model;

use think\Model;
use think\Db;

class AdminaccountModel extends Model{
    public static function tbname(){
        return db('adminaccount');
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
    /*
     * 申请金额
     */
    public static function do_add($params){
        Db::startTrans();
        try{
            ////数据处理
            $find_admin = AdminModel::find("and id={$_SESSION['admin_id']}");
            $adarr=[];
            $adarr['money']=$params['confirmmoney'];
            $adarr['freeze_money'] = $params['confirmmoney'];
            AdminModel::edit(" and id=".$_SESSION['admin_id'],$adarr);
            //生成客服修改余额财务记录
            $ldata = [];
            $ldata['algorithm'] = 3;//修改
            $ldata['type'] = 8;//修改余额
            $ldata['admin_id'] = $find_admin['id'];//客服Id
            $ldata['money'] =$params['confirmmoney'];//变动金额
            $ldata['startmoney'] = $find_admin['money'];//操作前余额
            $ldata['endmoney'] = $params['confirmmoney'];//操作后金额
            $ldata['remark'] = "客服（{$find_admin['name']}）修改余额为：{$params['confirmmoney']}";//详情
            $ldata['intime'] = time();
            $ldata['uptime'] = time();
            AdminmlogsModel::add($ldata);
            $data['admin_id'] = $_SESSION['admin_id'];
            $data['yest_money'] = $params['yestmoney'];
            $data['apply_money'] = $params['applymoney'];
            $data['order_num'] = $params['order_num'];
            $data['order_price'] = $params['order_price'];
            $data['order_within'] = $params['order_within'];
            $data['offline_money'] = $params['offline_money'];
            $data['refund_order_num'] = $params['refund_order_num'];
            $data['refund_order_price'] = $params['refund_order_price'];
            $data['refund_order_within'] = $params['refund_order_within'];
            $data['money'] = $params['money'];
            $data['confirmmoney'] = $params['confirmmoney'];
            $data['differmoney'] = $params['differmoney'];//相差金额
            $data['remark'] = $params['remark'];
            $data['intime'] = time();
            $account_id=self::add($data);
            $picarr=[];
            foreach ($params['pic'] as $v){
                $picarr['admin_id']=$_SESSION['admin_id'];
                $picarr['account_id']=$account_id;
                $picarr['position']='adminaccount';
                $picarr['picpath']=$v;
                $picarr['intime']=time();
                Db::table('adminaccountimg')->insertGetId($picarr);
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
    /*
     * 删除记录
     */
    public static function do_del($params){
        Db::startTrans();
        try{
            ////数据处理
            //删除记录前先删除图片
            $picarr=Db::table('adminaccountimg')->where("account_id={$params['id']}")->select();
            foreach ($picarr as $v){
                $url=$_SERVER["DOCUMENT_ROOT"].$v['picpath'];
                unlink($url);
                Db::table('adminaccountimg')->delete("id={$v['id']}");
            }
            //删除客服结账相关记录
            self::del(" and id=".$params['id']);
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