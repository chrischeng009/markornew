<?php
namespace app\common\model;

use think\Model;
use think\Db;

class BannerModel extends Model{
    public static function tbname(){
        return db('banner');
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
    public static function enum_status_arr()
    {
        $arr[1] = '未审核';
        $arr[2] = '已审核';
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
    public static function enum_flag_arr()
    {
        $arr[1] = '推荐';
        $arr[2] = '特荐';
        $arr[3] = '热门';
        $arr[4] = '头条';
        return $arr;
    }
    public static function enum_flag_text($key)
    {
        $arr = self::enum_flag_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    
    
    
}
?>