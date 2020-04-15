<?php
namespace app\common\model;

use think\Model;
use think\Db;

class AdmincashModel extends Model{
    public static function tbname(){
        return db('admincash');
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
    // 求出当日客服公款金额
    public static function summoney($aid,$starttime,$lasttime){
        $sumarr="" ;
        $day = date("Y-m-d");
        $start_time = !empty($starttime)? strtotime($starttime . " 00:00:00"):strtotime($day . " 00:00:00");
        $last_time = !empty($lasttime)?strtotime($lasttime . " 23:59:59"):strtotime($day." 23:59:59");
        $sumarr .=" and uptime>={$start_time} and uptime<={$last_time}";
        $sumarr .=" and status = 2";
        //   判断是业务员和客服
        if($aid){
            $sumarr .=" and admin_id ={$aid}";
        }
        $sum=self::sum($sumarr,'money');
        return $sum;
    }   
	public static function summoney2($aid){
        $sumarr="" ;
        $day = date("Y-m-d");
        $start_time = !empty($starttime)? strtotime($starttime . " 00:00:00"):strtotime($day . " 00:00:00");
        $last_time = !empty($lasttime)?strtotime($lasttime . " 23:59:59"):strtotime($day." 23:59:59");
        $sumarr .=" and uptime>={$start_time} and uptime<={$last_time}";
        $sumarr .=" and status = 2";
        //   判断是业务员和客服
        if($aid){
            $sumarr .=" and admin_id ={$aid}";
        }
        $sum=self::sum($sumarr,'money');
        return $sum;
    }
    public static function enum_status_arr()
    {
        $arr[1] = '待处理';
        $arr[2] = '已处理';
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
    const enumType1 = 1;
    const enumType2 = 2;
    const enumType3 = 3;
    public static function enum_type_arr()
    {
        return [
            self::enumType1 => '微信',
            self::enumType2 => '支付宝',
            self::enumType3 => '银行卡',
        ];
    }
    public static function enum_type_text($key)
    {
        $arr = self::enum_type_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    /*
     * 申请金额
     */
    public static function do_add($params){
        Db::startTrans();
        try{
            ////数据处理
            $find_adminbank = AdminbankModel::find("and id=".$params['id']);
            $find_admin = AdminModel::find("and id=".$_SESSION['admin_id']);
//            if($params['type']==self::enumType1){
//                if(empty($find_admin['weixinmoneycode'])){
//                    exception("您未绑定微信收款码");
//                }
//                $data['weixinmoneycode'] = $find_admin['weixinmoneycode'];
//            }elseif($params['type']==self::enumType2){
//                if(empty($find_admin['zfbmoneycode'])){
//                    exception("您未绑定支付宝收款码");
//                }
//                $data['zfbmoneycode'] = $find_admin['zfbmoneycode'];
//            }elseif($params['type']==self::enumType3){
//                if(empty($find_admin['bankname'] || empty($find_admin['bankcode']))){
//                    exception("您未绑定银行帐号");
//                }
//                $data['bankname'] = $find_admin['bankname'];
//                $data['bankcode'] = $find_admin['bankcode'];
//            }else{
//                exception("提现方式错误");
//            }
            
            $data['realname'] = $find_adminbank['realname'];
            $data['admin_id'] = $params['admin_id'];
            $data['admin_money'] = $find_admin['money'];
//            $data['type'] = $params['type'];
            $data['type']=self::enumType3;
            $data['money'] = $params['money'];
            $data['remark'] = $params['remark'];
            $data['bankcode'] = $find_adminbank['bankcode'];
            $data['bankname'] = $find_adminbank['bank_name'];
            $data['status'] = 1;//待处理
            $data['intime'] = time();
            $time=date("Y-m-d H:i:s");
            $id=self::add($data);
            $address="919474561@qq.com";
            $title='客服公款申请确认';
            $url=$_SERVER['SERVER_NAME']."/home.php/admin/admincash/act_editurl.html?id={$id}&status=2";
            $content = "主题：{$title}<br /> 客服({$find_admin['name']})申请公款，银行信息：{$data['bankname']}-{$data['realname']}-{$data['bankcode']}；
              申请公款金额为：{$data['money'] }，申请时间：{$time}<br />点击链接审核即可通过：{$url}";
            send_mail($address,$title,$content);
            $wchat = new \wechat\WechatOauth();
            $wchat->sendmessage('oHJid1S-dWytkhE1ItYOyMZ8z3eE',$content);
            $wchat->sendmessage('oHJid1Rd8X_DV8vdnEBMxeb1dK1I',$content);
            $wchat->sendmessage('oHJid1esIom5qfrGcCYMGb5Y4xLA',$content);
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
     * 处理申请
     */
    public static function do_edit($id,$params)
    {
            ////数据处理
            $find = self::find("and id=".$id);
            if($find['status']!=1){
                exception("请勿重处理");
            }
            
            $find_admin = AdminModel::find("and id=".$find['admin_id']);
            $find_admincash = AdmincashModel::find("and id=".$find['admin_id']);
            $data['status'] = $params['status'];
            $data['uptime'] = time();
            self::edit("and id='".$id."'",$data);
            
            //已处理
            if($params['status']==2){
                $adata = [];
                $adata['money'] = $find_admin['money']+$find['money'];
                AdminModel::edit("and id={$find['admin_id']}",$adata);
                //记录客服财务明细
                $ldata = [];
                $ldata['algorithm'] = 1;//加法
                $ldata['type'] = 1;//公款申请
                $ldata['admin_id'] = $find['admin_id'];//客服Id
                $ldata['startmoney'] = $find_admin['money'];//操作前余额
                $ldata['money'] = $find['money'];//变动金额
                $ldata['endmoney'] =  $adata['money'];//操作后金额
                $ldata['order_code'] = "";//订单号
                $ldata['tradeno'] = "";//流水号
                $ldata['remark'] = $find_admincash['remark'];//详情
                $ldata['intime'] = time();
                $ldata['uptime'] = time();
                AdminmlogsModel::add($ldata);
                //返回
                return 'success';
            }else{
                return '处理失败！';
            }

    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
?>