<?php
namespace app\common\model;

use think\Model;
use think\Db;

class MerchantmlogsModel extends Model{
    public static function tbname(){
        return db('merchantmlogs');
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
    const enumalgorithm1 = 1;
    const enumalgorithm2 = 2;
    public static function enum_algorithm_arr()
    {
        $arr[1] = '+';
        $arr[2] = '-';
        return $arr;
    }
    public static function enum_algorithm2_arr()
    {
        $arr[1] = '收入';
        $arr[2] = '支出';
        return $arr;
    }
    public static function enum_algorithm_text($key)
    {
        $arr = self::enum_algorithm_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    const enumtype1 = 1;
    const enumtype2 = 2;
    const enumtype3 = 3;
    const enumtype4 = 4;
    const enumtype5 = 5;
    const enumtype6 = 6;
    const enumtype7 = 7;
    const enumtype8 = 8;
    const enumtype9 = 9;
    const enumtype10 = 10;
    const enumtype11 = 11;
    const enumtype12 = 12;
    const enumtype13 = 13;
    const enumtype14 = 14;
    public static function enum_type_arr()
    {
        $arr[1] = '提现';
        $arr[2] = '充值';
        $arr[3] = '贷款';
        $arr[4] = '还款';
        $arr[5] = '任务';
        $arr[6] = '任务退回';
        $arr[7] = '提现拒绝';
        $arr[8] = '退单本金';
        $arr[9] = '退单服务费';
        $arr[10] = '退多余本金';
        $arr[11] = '减差本金';
        $arr[12] = '欠费金额';
        $arr[13] = '退多余服务费';
        $arr[14] = '减差服务费';
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
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
?>