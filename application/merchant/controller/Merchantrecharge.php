<?php
namespace app\merchant\controller;

use think\Controller;
use app\common\model\ConfigModel;
use app\common\model\MerchantModel;
use app\common\model\MerchantrechargeModel;//本model放最后方便看清是否弄错

class Merchantrecharge extends Controller
{
    public function lists()
    {
        $where = "and merchant_id={$_SESSION['merchant_id']}";
        $_GET['type'] = gett('type');
        $_GET['status'] = gett('status');
        if(!empty($_GET['type'])){
            $where .= " and type = '".$_GET['type']."'";
        }
        if(!empty($_GET['status'])){
            $where .= " and status = '".$_GET['status']."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = ['page'=>$page,'query'=>['type'=>$_GET['type'],'status'=>$_GET['status']]];
        $query = MerchantrechargeModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('enum_type_arr',MerchantrechargeModel::enum_type_arr());
        $this->assign('enum_status_arr',MerchantrechargeModel::enum_status_arr());
        $this->assign('find_config',ConfigModel::find());
        
    	return $this->fetch();
    }
    public function add()
    {
        $this->assign('enum_type_arr',MerchantrechargeModel::enum_type_arr());
        $this->assign('enum_status_arr',MerchantrechargeModel::enum_status_arr());
        $this->assign('find_config',ConfigModel::find());
    	return $this->fetch();
    }
    public function act_add()
    {
        $data = $_POST;
        if($data['type']==1){
            unset($data['bankname']);unset($data['realname']);unset($data['bankcode']);
            $czfs='支付宝充值';
        }else{
            unset($data['zfbmoneycode']);
            $czfs='银行卡充值';
        }
        $data['merchant_id'] = $_SESSION['merchant_id'];
        $data['status'] = 1;
        $data['intime'] = time();
        //发布邮件
        $time = date("Y-m-d H:i:s");
        $address = "";
        $title = '商家端充值';
        $url = $_SERVER['SERVER_NAME'] . "/home.php/admin/merchantrecharge/lists.html";
        $find_merchant = MerchantModel::find("and id=".$data['merchant_id']);
        $content = "主题：{$title}<br /> 
            商家手机号：{$find_merchant['mobile']},充值金额：{$data['money']}，申请时间：{$time}，充值方式：{$czfs}
            <br />点击链接进行审核：{$url}";
        send_mail($address, $title, $content);
        //end
        $result = MerchantrechargeModel::add($data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    
    









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
