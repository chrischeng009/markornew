<?php
namespace app\common\model;

use think\Model;
use think\Db;

class WithoutModel extends Model{
    public static function tbname(){
        return db('without');
    }
    //添加一条记录
    public static function add($data){
        return self::tbname()->insertGetId($data);
    }
    //查询一条记录
    public static function find($where=''){
        return self::tbname()->where("1=1 $where")->find();
    }
    //查询多条记录
    public static function select($where=''){
        return self::tbname()->where("1=1 $where")->order('price asc')->select();
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
    public static function do_add($data){
        Db::startTrans();
        try{
            $without_id = self::add($data);
            
            $merchant_list = MerchantModel::select();
            foreach($merchant_list as $v){
                $merchantwithout['without_id'] = $without_id;
                $merchantwithout['merchant_id'] = $v['id'];
                $merchantwithout['start'] = $data['start'];
                $merchantwithout['end'] = $data['end'];
                $merchantwithout['price'] = $data['price'];
                $merchantwithout['intime'] = time();
                $merchantwithout['uptime'] = time();
                MerchantwithoutModel::add($merchantwithout);
            }
            
            //提交
            Db::commit();
            //返回
            return true;
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            //$this->error($e->getMessage());//把异常消息捕获并抛出
            return false;
        }
    }
    
    
}
?>