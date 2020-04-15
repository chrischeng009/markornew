<?php
namespace app\common\model;

use think\Model;
use think\Db;

class HongbaoModel extends Model{
    public static function tbname(){
        return db('hongbao');
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
    //求和
    public static function sum($where='',$field){
        return self::tbname()->where("1=1 $where")->sum($field);
    }
     public static function enum_type_arr()
    {
        $arr[1] = '线上红包';
        $arr[2] = '线下红包';
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

    public static function enum_status_arr()
    {
        $arr[1] = '未发送';
        $arr[2] = '已发送';
        $arr[3] = '线下发送';
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
    /**
     * [xmltoarray xml格式转换为数组]
     * @param  [type] $xml [xml]
     * @return [type]      [xml 转化为array]
     */
    public static function xmltoarray($xml) {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring),true);
        return $val;
    }

    /**
     * [arraytoxml 将数组转换成xml格式（简单方法）:]
     * @param  [type] $data [数组]
     * @return [type]       [array 转 xml]
     */
    public static function arraytoxml($data){
        $str='<xml>';
        foreach($data as $k=>$v) {
            $str.='<'.$k.'>'.$v.'</'.$k.'>';
        }
        $str.='</xml>';
        return $str;
    }

    /**
     * [createNoncestr 生成随机字符串]
     * @param  integer $length [长度]
     * @return [type]          [字母大小写加数字]
     */
    public static function createNoncestr($length =32){
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYabcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";

        for($i=0;$i<$length;$i++){
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }
    public  static function curl_post_ssl($url, $xmldata, $second = 30, $aHeader = array()){
        $isdir ="/wwwroot/nouser/vendor/wxpay/cert/";//证书位置;绝对路径

        $ch = curl_init();//初始化curl

        curl_setopt($ch, CURLOPT_TIMEOUT, $second);//设置执行最长秒数
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');//证书类型
        curl_setopt($ch, CURLOPT_SSLCERT, $isdir . 'apiclient_cert.pem');//证书位置
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');//CURLOPT_SSLKEY中规定的私钥的加密类型
        curl_setopt($ch, CURLOPT_SSLKEY, $isdir . 'apiclient_key.pem');//证书位置
        curl_setopt($ch, CURLOPT_CAINFO, 'PEM');
        // curl_setopt($ch, CURLOPT_CAINFO, $isdir . 'rootca.pem');
        if (count($aHeader) >= 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);//设置头部
        }
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmldata);//全部数据使用HTTP协议中的"POST"操作来发送

        $data = curl_exec($ch);//执行回话
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }
    public static function sendMoney($amount,$openid,$ordernum){

        $total_amount = (100) * $amount;

        $data=array(
            'wxappid'=>'wx448b8134cf19b48b',//商户账号appid
            'mch_id'=> '1537263191',//商户号
            'send_name'=> 'maoke',//发送者名称
            'nonce_str'=>self::createNoncestr(),//随机字符串
            'mch_billno'=> $ordernum,//商户订单号
            'act_name'=>'maoke',
            're_openid'=> $openid,//用户openid
            'total_num'=>1,
            'total_amount'=> $total_amount,//金额
            'wishing'=>'红包发放',//企业付款描述信息
            'remark'=>'红包发放',
            'scene_id'=>'PRODUCT_1',
            'client_ip'=> '39.100.107.152',//Ip地址
        );

        //生成签名算法
        $secrect_key='lzfcjs666lzfcjs666lzfcjs666lzf66';///这个就是个API密码。MD5 32位。
        $data=array_filter($data);
        ksort($data);
        $str='';
        foreach($data as $k=>$v) {
            $str.=$k.'='.$v.'&';
        }
        $str.='key='.$secrect_key;
        $sign=md5($str);
        $data['sign']=strtoupper($sign);

        //生成签名算法


        $xml=self::arraytoxml($data);

        $url='https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack'; //调用接口
        $res=self::curl_post_ssl($url,$xml);

        $return=self::xmltoarray($res);

        $return['trade_no']=$data['mch_billno'];
        return $return;
    }

    //发放红包
    public  static function  act_hongbao($orprice,$openid,$ordernum){
        $res= self::sendMoney($orprice,$openid,$ordernum);
        return $res;
    }
    //查询红包是否被领取
    public static function do_search_hongbao($id){
        $data=array(
            'wxappid'=>'wx448b8134cf19b48b',//商户账号appid
            'mch_id'=> '1537263191',//商户号
            'nonce_str'=>self::createNoncestr(),//随机字符串
            'mch_billno'=> $id,//商户订单号
            'bill_type '=>'MCHT',

        );
        //生成签名算法
        $secrect_key='lzfcjs666lzfcjs666lzfcjs666lzf66';///这个就是个API密码。MD5 32位。
        $data=array_filter($data);
        ksort($data);
        $str='';
        foreach($data as $k=>$v) {
            $str.=$k.'='.$v.'&';
        }
        $str.='key='.$secrect_key;
        $sign=md5($str);
        $data['sign']=strtoupper($sign);

        //生成签名算法
        $xml=self::arraytoxml($data);

        $url='https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo '; //调用接口
        $res=self::curl_post_ssl($url,$xml);
        $return=self::xmltoarray($res);
        $return['trade_no']=$data['mch_billno'];
        return $return;
    }
     public static function hbshopname($order_id){
        $order = OrderModel::find("and id={$order_id}");
        return $order['shop_name'];
    }
}
?>