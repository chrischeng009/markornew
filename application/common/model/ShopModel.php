<?php
namespace app\common\model;

use think\Model;
use think\Db;

class ShopModel extends Model{
    public static function tbname(){
        return db('shop');
    }
    //添加一条记录
    public static function add($data){
        return self::tbname()->insertGetId($data);
    }
    //查询一条记录
    public static function find($where=''){
        return self::tbname()->where("1=1 $where")->order('id asc')->find();
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
    const enumstatus1 = 1;
    const enumstatus2 = 2;
    const enumstatus3 = 3;
    public static function enum_status_arr()
    {
        $arr[1] = '待审核';
        $arr[2] = '已审核';
        $arr[3] = '未通过';
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
    public static function getStrName($merchant_id)
    {
        $lists = self::select("and merchant_id={$merchant_id}");
        $str = "";
        foreach($lists as $v){
            $str .= $v['name'].'<br/>';
        }
        return substr($str, 0, -5);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
?>