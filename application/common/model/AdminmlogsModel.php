<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/15
 * Time: 17:47
 */
namespace app\common\model;
use think\Model;
use think\Db;
class AdminmlogsModel extends Model{
    public static function tbname(){
        return db('adminmlogs');
    }
//    添加一条记录
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
    public static function sum($where='',$field){
        return self::tbname()->where("1=1 $where")->sum($field);
    }
    const enumalgorithm1 = 1;
    const enumalgorithm2 = 2;
    const enumalgorithm3 = 3;
    public static function enum_algorithm_arr()
    {
        $arr[1] = '+';
        $arr[2] = '-';
        $arr[3] = '修改';
        return $arr;
    }
    public static function enum_algorithm2_arr()
    {
        $arr[1] = '收入';
        $arr[2] = '支出';
        $arr[3] = '修改';
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
    const enumtype9= 9;
    const enumtype10= 10;
    public static function enum_type_arr()
    {
        $arr[1] = '公款申请';
        $arr[2] = '任务';
        $arr[3] = '任务退回';
        $arr[4] = '退单本金';
        $arr[5] = '退单佣金';
        $arr[6] = '减差本金';
        $arr[7] = '线下金额申请';
        $arr[8] = '修改余额';
        $arr[9] = '减差佣金';
        $arr[10] = '退回佣金';
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