<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\MerchantModel;
use app\common\model\ConfigModel;
use app\common\model\AdminModel;
use app\common\model\MerchantrechargeModel;//本model放最后方便看清是否弄错

class Merchantrecharge extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['mobile'] = gett('mobile');
        $_GET['type'] = gett('type');
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
        if(!empty($_GET['type'])){
            $where .= " and type = '".$_GET['type']."'";
        }
        if(!empty($_GET['status'])){
            $where .= " and status = '".$_GET['status']."'";
        }
        //非超级管理员以及财务
        $aid=$_SESSION['role_id'];
        if($aid==2 || $aid==5){
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
                'type'=>$_GET['type'],
                'status'=>$_GET['status'],
                'aid'=>$_GET['aid'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime'],
            ]
        ];
        $query = MerchantrechargeModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $sum= MerchantrechargeModel::summoney($aid,$_GET['starttime'],$_GET['lasttime'],$_GET['mobile']);
        $this->assign('sum',$sum);
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('merchant_list',MerchantModel::select());
        $this->assign('enum_type_arr',MerchantrechargeModel::enum_type_arr());
        $this->assign('enum_status_arr',MerchantrechargeModel::enum_status_arr());
        $this->assign('find_config',ConfigModel::find());
        $this->assign('teacher_list',AdminModel::select("and role_id=5"));//所属业务员
        
    	return $this->fetch();
    }
    public function act_edit()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        $data = $_POST;
        
        $result = MerchantrechargeModel::do_edit(postt('id'),$data);
        echo $result;
    }
    









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
