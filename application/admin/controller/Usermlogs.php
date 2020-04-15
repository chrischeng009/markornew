<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\UserModel;
use app\common\model\AdminModel;
use app\common\model\UsermlogsModel;//本model放最后方便看清是否弄错
class Usermlogs extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['abc'] = gett('abc');
        $_GET['user_id'] = gett('user_id');
        $_GET['type'] = gett('type');
        $_GET['algorithm'] = gett('algorithm');
        $_GET['aid'] = gett('aid');
        $_GET['order_code'] = gett('order_code');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        //业务员或者客服
        if($_SESSION['role_id']==2 || $_SESSION['role_id']==5){
            $_GET['aid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['aid'])){
            $strId = UserModel::getStrId($_GET['aid']);
            $where .= " and user_id in({$strId})";
        }
        if(!empty($_GET['user_id'])){
            $where .= " and user_id = '".$_GET['user_id']."'";
        }
        if(!empty($_GET['type'])){
            $where .= " and type = '".$_GET['type']."'";
        }
        if(!empty($_GET['algorithm'])){
            $where .= " and algorithm = '".$_GET['algorithm']."'";
        }
        if(!empty($_GET['order_code'])){
            $where .= " and order_code = '".$_GET['order_code']."'";
        }
        if(!empty($_GET['starttime'])){
            $where .= " and intime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
        }
        if(!empty($_GET['lasttime'])){
            $where .= " and intime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'abc'=>$_GET['abc'],
                'user_id'=>$_GET['user_id'],
                'type'=>$_GET['type'],
                'algorithm'=>$_GET['algorithm'],
                'aid'=>$_GET['aid']
            ]
        ];
        $query = UsermlogsModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('user_list',UserModel::select());
        $this->assign('enum_type_arr',UsermlogsModel::enum_type_arr());
        $this->assign('enum_algorithm2_arr',UsermlogsModel::enum_algorithm2_arr());
        $this->assign('server_list',AdminModel::select("and role_id=2"));//所属客服
        
    	return $this->fetch();
    }
    









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
