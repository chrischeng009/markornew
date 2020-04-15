<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\MerchantModel;
use app\common\model\AdminModel;
use app\common\model\MerchantloansModel;//本model放最后方便看清是否弄错

class Merchantloans extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['mobile'] = gett('mobile');
        $_GET['status'] = gett('status');
        $_GET['aid'] = gett('aid');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        if(!empty($_GET['mobile'])){
            $find_merchant = MerchantModel::find("and mobile='".$_GET['mobile']."'");
            if(!empty($find_merchant)){
                $where .= " and merchant_id={$find_merchant['id']}";
            }
        }
        if(!empty($_GET['status'])){
            $where .= " and status = '".$_GET['status']."'";
        }
        //业务员或者客服
        if($_SESSION['role_id']==2 || $_SESSION['role_id']==5){
            $_GET['aid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['aid'])){
            $strId = MerchantModel::getStrId($_GET['aid']);
            $where .= " and merchant_id in({$strId})";
        }
        if(!empty($_GET['starttime'])){
            $where .= " and uptime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
        }
        if(!empty($_GET['lasttime'])){
            $where .= " and uptime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'mobile'=>$_GET['mobile'],
                'status'=>$_GET['status'],
                'aid'=>$_GET['aid'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime'],
            ]
        ];
        $query = MerchantloansModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);

        $page_show = $query->render();
        $sum = MerchantloansModel::summoney($_GET['aid'],$_GET['starttime'],$_GET['lasttime'],$_GET['mobile']);
        $this->assign('sum',$sum);

        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('merchant_list',MerchantModel::select());
        $this->assign('enum_status_arr',MerchantloansModel::enum_status_arr());
        $this->assign('teacher_list',AdminModel::select("and role_id=5"));//所属业务员
        
    	return $this->fetch();
    }
    public function act_edit()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        $data = $_POST;
        
        $result = MerchantloansModel::do_edit(postt('id'),$data);
        echo $result;
    }









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
