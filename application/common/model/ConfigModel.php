<?php
namespace app\common\model;

use think\Model;
use think\Db;

class ConfigModel extends Model{
    public static function tbname(){
        return db('config');
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
    /*
     * 所有是否公用
     */
    const enumIsnot1 = 1;
    const enumIsnot2 = 2;
    public static function enum_isnot_arr()
    {
        return [
            self::enumIsnot1 => '否',
            self::enumIsnot2 => '是',
        ];
    }
    public static function enum_isnot_text($key)
    {
        $arr = self::enum_isnot_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    const enumIsfreeze1 = 1;
    const enumIsfreeze2 = 2;
    public static function enum_isfreeze_arr()
    {
        $arr[1] = '未冻结';
        $arr[2] = '已冻结';
        return $arr;
    }
    public static function enum_isfreeze_text($key)
    {
        $arr = self::enum_isfreeze_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    const enumroleid1 = 1;
    const enumroleid2 = 2;
    const enumroleid3 = 3;
    const enumroleid4 = 4;
    const enumroleid5 = 5;
    public static function enum_roleid_arr()
    {
        $arr[1] = '超级管理员';
        $arr[2] = '客服';
        $arr[3] = '财务';
        $arr[4] = '采购';
        $arr[5] = '业务员';
        return $arr;
    }
    public static function enum_roleid_text($key)
    {
        $arr = self::enum_roleid_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    //后台头部颜色
    public static function enum_acolor_arr()
    {
        $arr['#399bff'] = '蓝色';
        $arr['#ff0d3f'] = '红色';
        $arr['#5eb95e'] = '绿色';
        $arr['#f8fa4c'] = '黄色';
        $arr['#FFAF06'] = '橙色';
        $arr['#c25de3'] = '紫色';
        $arr['#f874b2'] = '粉色';
        return $arr;
    }
    public static function enum_acolor_text($key)
    {
        $arr = self::enum_acolor_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    //同步类型：商家工作时间、商家每天任务数、商家服务费
    public static function enum_synchro_arr()
    {
        $arr[1] = '商家工作时间';
        $arr[2] = '商家每天任务数';
        $arr[3] = '商家服务费';
        return $arr;
    }
    public static function enum_synchro_text($key)
    {
        $arr = self::enum_synchro_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    public static function do_edit($id,$data){
        Db::startTrans();
        try{
            //数据处理
            self::edit("and id='".$id."'",$data);
            
            //商家工作时间
            if($data['synchro']==1){
                $merchant['worktime'] = $data['worktime'];
                MerchantModel::edit("and id>=1",$merchant);
            }
            
            //商家每天任务数
            if($data['synchro']==2){
                $merchant['worknum'] = $data['worknum'];
                MerchantModel::edit("and id>=1",$merchant);
            }
            
            //商家服务费
            if($data['synchro']==3){
                $without_list = WithoutModel::select();
                $merchant_list = MerchantModel::select();
                foreach($without_list as $v){
                    foreach($merchant_list as $vv){
                        $find_merchantwithout = MerchantwithoutModel::find("and without_id={$v['id']} and merchant_id={$vv['id']}");
                        $merchantwithout = [];
                        $merchantwithout['start'] = $v['start'];
                        $merchantwithout['end'] = $v['end'];
                        $merchantwithout['price'] = $v['price'];
                        $merchantwithout['uptime'] = time();
                        if($find_merchantwithout){
                            MerchantwithoutModel::edit("and without_id={$v['id']} and merchant_id={$vv['id']}",$merchantwithout);
                        }else{
                            $merchantwithout['intime'] = time();
                            $merchantwithout['without_id'] = $v['id'];
                            $merchantwithout['merchant_id'] = $vv['id'];
                            MerchantwithoutModel::add($merchantwithout);
                        }
                        unset($merchantwithout);unset($vv);
                    }
                    unset($v);
                }
            }
            
            //提交
            Db::commit();
            //返回
            return true;
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);
            return false;
        }
    }
    
    
    
    
}
?>