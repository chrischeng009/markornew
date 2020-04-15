<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\MerchantModel;
use app\common\model\MerchantwithoutModel;//本model放最后方便看清是否弄错

class Merchantwithout extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['merchant_id'] = gett('merchant_id');
        if(!empty($_GET['merchant_id'])){
            $where .= " and merchant_id='".$_GET['merchant_id']."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = ['page'=>$page,'query'=>['merchant_id'=>$_GET['merchant_id']]];
        $query = MerchantwithoutModel::tbname()->where("1=1 $where")->order('price asc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('merchant_list',MerchantModel::select());
        
    	return $this->fetch();
    }
    public function edit()
    {
        $this->isAuth();
        $find = MerchantwithoutModel::find("and id=".gett('id'));
        $this->assign('find',$find);
        
        $this->assign('find_merchant',MerchantModel::find("and id={$find['merchant_id']}"));
        
        return $this->fetch();
    }
    public function act_edit()
    {
        $this->isAuth();
        $data = $_POST;
        unset($data['id']);
        $data['uptime'] = time();
        $result = MerchantwithoutModel::edit("and id='".postt('id')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
