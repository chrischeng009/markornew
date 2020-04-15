<?php
namespace app\common\model;

use think\Model;
use think\Db;

class UsermlogsModel extends Model{
    public static function tbname(){
        return db('Usermlogs');
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
    public static function enum_type_arr()
    {
        $arr[1] = '提现';
        $arr[2] = '返本金';
        $arr[3] = '返佣金';
        $arr[4] = '提现拒绝';
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
    public static function get_tradeno()
    {
        $day = date("Y-m-d");
        $start_time = strtotime($day." 00:00:00");
        $last_time = strtotime($day." 23:59:59");
        $count = self::count("and intime>={$start_time} and intime<={$last_time}");
        $num = 100000000+$count+1;
        $str = (string)$num;
        $str = date("Ymd").substr($str,1,8);//字符串截取，从第二个字符开始截取8位，字符共9位
        return $str;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
?>