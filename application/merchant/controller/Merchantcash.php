<?php
namespace app\merchant\controller;

use think\Controller;
use app\common\model\MerchantModel;
use app\common\model\MerchantcashModel;//本model放最后方便看清是否弄错

class Merchantcash extends Controller
{
    public function lists()
    {
        $where = "and merchant_id={$_SESSION['merchant_id']}";
        $_GET['status'] = gett('status');
        $_GET['realname'] = gett('realname');
        if(!empty($_GET['status'])){
            $where .= " and status = '".$_GET['status']."'";
        }
        if(!empty($_GET['realname'])){
            $where .= " and realname like '%".$_GET['realname']."%'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = ['page'=>$page,'query'=>['status'=>$_GET['status'],'realname'=>$_GET['realname']]];
        $query = MerchantcashModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);
        $this->assign('enum_status_arr',MerchantcashModel::enum_status_arr());
        
        $this->assign('find_merchant',MerchantModel::find("and id={$_SESSION['merchant_id']}"));
        
    	return $this->fetch();
    }
    public function add()
    {
        
    	return $this->fetch();
    }
    public function act_add()
    {
        $data['merchant_id'] = $_SESSION['merchant_id'];
        $data['money'] = postt('money');
        $data['bankname'] = postt('bankname');
        $data['realname'] = postt('realname');
        $data['bankcode'] = postt('bankcode');
        $data['remark'] = postt('remark');
        //发布邮件
        $time = date("Y-m-d H:i:s");
        $address = "";
        $title = '商家提现记录';
        $url = $_SERVER['SERVER_NAME'] . "/home.php/admin/merchantcash/lists.html";
        $find_merchant = MerchantModel::find("and id=".$data['merchant_id']);
        $content = "主题：{$title}<br /> 
            商家手机号：{$find_merchant['mobile']},提现金额：{$data['money']}，申请时间：{$time}
            <br />点击链接进行审核：{$url}";
        send_mail($address, $title, $content);
        $result = MerchantcashModel::do_add($data);
        echo $result;
    }
    public function act_check()
    {
        $where='';
        $where=" and id={$_SESSION['merchant_id']}";
        $result = MerchantModel::find($where);
        if($result['bankname']=='' || $result['bankcode']=='' || $result['realname']==''){
           $result2='请先绑定银行卡信息';
        }else{
            $result2= 'success';
        }
        echo $result2;
    }










    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
