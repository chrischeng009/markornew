<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\MerchantModel;
use app\common\model\AdminModel;
use app\common\model\MerchantmlogsModel;//本model放最后方便看清是否弄错

class Merchantmlogs extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['mobile'] = gett('mobile');
        $_GET['type'] = gett('type');
        $_GET['algorithm'] = gett('algorithm');
        $_GET['aid'] = gett('aid');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        $_GET['merchant_id'] = gett('merchant_id');
        $_GET['order_code'] = gett('order_code');
        $_GET['abc'] = gett('abc');
        if(!empty($_GET['mobile'])){
            $find_merchant = MerchantModel::find("and mobile='".$_GET['mobile']."'");
            if(!empty($find_merchant)){
                $where .= " and merchant_id={$find_merchant['id']}";
            }
        }
        if(!empty($_GET['merchant_id'])){
            $where .= " and merchant_id={$_GET['merchant_id']}";
        }
        if(!empty($_GET['order_code'])){
            $where .= " and order_code={$_GET['order_code']}";
        }
        if(!empty($_GET['type'])){
            $where .= " and type = '".$_GET['type']."'";
        }
        if(!empty($_GET['algorithm'])){
            $where .= " and algorithm = '".$_GET['algorithm']."'";
        }
        //业务员或者客服
        if($_SESSION['role_id']==2 || $_SESSION['role_id'] ==5){
            $_GET['aid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['aid'])){
            $strId = MerchantModel::getStrId($_GET['aid']);
            $where .= " and merchant_id in({$strId})";
        }
        $_GET['starttime'] = !empty($_GET['starttime'])?$_GET['starttime']:date("Y-m-d");
        $_GET['lasttime'] = !empty($_GET['lasttime'])?$_GET['lasttime']:date("Y-m-d");
        if(!empty($_GET['starttime'])){
            $where .= " and intime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
        }
        if(!empty($_GET['lasttime'])){
            $where .= " and intime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        if(!empty($_GET['abc'])){
            $params = [
                'page' => $page,
                'query' => [
                    'starttime' => $_GET['starttime'],
                    'lasttime' => $_GET['lasttime'],
                    'merchant_id' => $_GET['merchant_id'],
                    'abc' => $_GET['abc'],
                ]
            ];
        } else{
            $params = [
                'page' => $page,
                'query' => [
                    'mobile' => $_GET['mobile'],
                    'type' => $_GET['type'],
                    'algorithm' => $_GET['algorithm'],
                    'aid' => $_GET['aid'],
                    'starttime' => $_GET['starttime'],
                    'lasttime' => $_GET['lasttime'],
                    'merchant_id' => $_GET['merchant_id'],
                    'order_code' => $_GET['order_code'],
                ]
            ];
        }
        $query = MerchantmlogsModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();

        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('merchant_list',MerchantModel::select());
        $this->assign('enum_type_arr',MerchantmlogsModel::enum_type_arr());
        $this->assign('enum_algorithm2_arr',MerchantmlogsModel::enum_algorithm2_arr());
        $this->assign('teacher_list',AdminModel::select("and role_id=5"));//所属业务员
        
    	return $this->fetch();
    }
    









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
