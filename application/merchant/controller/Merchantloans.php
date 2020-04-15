<?php
namespace app\merchant\controller;

use think\Controller;
use app\common\model\MerchantModel;
use app\common\model\MerchantloansModel;//本model放最后方便看清是否弄错

class Merchantloans extends Controller
{
    public function lists()
    {
        $where = "and merchant_id={$_SESSION['merchant_id']}";
        $_GET['status'] = gett('status');
        if(!empty($_GET['status'])){
            $where .= " and status = '".$_GET['status']."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = ['page'=>$page,'query'=>['status'=>$_GET['status']]];
        $query = MerchantloansModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('enum_status_arr',MerchantloansModel::enum_status_arr());

    	return $this->fetch();
    }
    public function add()
    {
    	return $this->fetch();
    }
    public function act_add()
    {
        $data = $_POST;
        $data['merchant_id'] = $_SESSION['merchant_id'];
        $data['status'] = 1;
        $data['intime'] = time();
        //发布邮件
        $time = date("Y-m-d H:i:s");
        $address = "";
        $title = '商家贷款记录';
        $url = $_SERVER['SERVER_NAME'] . "/home.php/admin/merchantloans/lists.html";
        $find_merchant = MerchantModel::find("and id=".$data['merchant_id']);
        $content = "主题：{$title}<br /> 
            商家手机号：{$find_merchant['mobile']},贷款金额：{$data['money']}，申请时间：{$time}
            <br />点击链接进行审核：{$url}";
        send_mail($address, $title, $content);
        $result = MerchantloansModel::add($data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function act_refund()
    {
        $params['merchant_id'] = $_SESSION['merchant_id'];
        $params['money'] = postt('money');
        $result = MerchantloansModel::do_refund($params);
        echo $result;
    }









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
