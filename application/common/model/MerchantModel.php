<?php
namespace app\common\model;

use think\Model;
use think\Db;

class MerchantModel extends Model{
    public static function tbname(){
        return db('merchant');
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
    const enumType1 = 1;
    const enumType2 = 2;
    const enumType3 = 3;
    public static function enum_type_arr()
    {
        $arr[1] = '普通商家';
        $arr[2] = 'VIP';
        $arr[3] = '超级VIP';
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
    const enumStatus1 = 1;
    const enumStatus2 = 2;
    const enumStatus3 = 3;
    public static function enum_status_arr()
    {
        $arr[1] = '待审核';
        $arr[2] = '已审核';
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
    public static function enum_isfreeze_arr()
    {
        $arr[1] = '不冻结';
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
    public static function do_add($data){
        Db::startTrans();
        try{
            ////数据处理
            //exception("导入失败，会员帐号：{$user_name}，已重复");//抛异常并让catch抛出
            $merchant_id = self::add($data);
            
            $without_list = WithoutModel::select();
            foreach($without_list as $v){
                $merchantwithout['merchant_id'] = $merchant_id;
                $merchantwithout['without_id'] = $v['id'];
                $merchantwithout['start'] = $v['start'];
                $merchantwithout['end'] = $v['end'];
                $merchantwithout['price'] = $v['price'];
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
    //查询aid下所有商家Id
    public static function getStrId($aid)
    {
        $lists = self::select("and aid={$aid}");
        if(empty($lists)){
            return 99999999;
//            return 1;
        }
        $str = "";
        foreach($lists as $v){
            $str .= $v['id'].',';
        }
        return substr($str, 0, -1);
    }
    //查询工作时间返回小时数
    public static function getWorkHour($id,$inHour)
    {
        $find = self::find("and id={$id}");
        if(empty($find['worktime'])){
            return 1;
        }
        $temp = explode('-',$find['worktime']);

        //$arr[0] = substr($temp[0],0,-3);
        $arr[1] = substr($temp[1],0,-3);
        if($inHour>=$arr[1]){
            return 1;
        }
        
        $workHour = $arr[1]-$inHour;
        return $workHour;
    }
    //查询工作时间
    public static function getWorkTime($id)
    {
        $arr[0] = 7;
        $arr[1] = 21;
        $find = self::find("and id={$id}");
        if(empty($find['worktime'])){
            return $arr;
        }
        $temp = explode('-',$find['worktime']);
        $arr[0] = substr($temp[0],0,-3);
        $arr[1] = substr($temp[1],0,-3);
        return $arr;
    }
    //根据店铺名查询商家id
    public static function getShopID($shop_name){
        $where='';
        $where .= " and name like '%".$shop_name."%'";
        $merchant=ShopModel::find("$where");
        return $merchant['merchant_id'];
    }
    //根据手机号查询商家id
    public static function getMerchantID($mobile){
        $where='';
        $where .= " and mobile={$mobile}";
        $merchant=self::find("$where");
        return $merchant['id'];
    }




}
?>