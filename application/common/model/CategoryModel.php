<?php
namespace app\common\model;

use think\Model;
use think\Db;

class CategoryModel extends Model{
    public static function tbname(){
        return db('category');
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
    //自己+子级id
    public static function getChildId($id){
        //一级二级
        $lists = self::select("and pid=$id");
        $str = $id.',';
        foreach($lists as $v){
            $str .= $v['id'].',';
        }
        $str = substr($str, 0, -1);
        
        return $str;
    }
    
    
   
    
    
    
    
}
?>