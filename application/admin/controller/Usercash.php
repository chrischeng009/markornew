<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\UserModel;
use app\common\model\BankModel;
use app\common\model\AdminModel;
use app\common\model\UsercashModel;//本model放最后方便看清是否弄错

class Usercash extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['user_id'] = gett('user_id');
        $_GET['type'] = gett('type');
        $_GET['status'] = gett('status');
        $_GET['realname'] = gett('realname');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        $_GET['aid'] = gett('aid');
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
        if(!empty($_GET['status'])){
            $where .= " and status = '".$_GET['status']."'";
        }
        if(!empty($_GET['realname'])){
            $where .= " and realname like '%".$_GET['realname']."%'";
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
            'user_id'=>$_GET['user_id'], 
            'realname'=>$_GET['realname'], 
            'aid'=>$_GET['aid'],
            ]
        ];
        $query = UsercashModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $sum = UsercashModel::summoney($_GET['aid'],$_GET['starttime'],$_GET['lasttime']);
        $this->assign('sum',$sum);
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('user_list',UserModel::select());
        $this->assign('enum_type_arr',UsercashModel::enum_type_arr());
        $this->assign('enum_status_arr',UsercashModel::enum_status_arr());
        $this->assign('bank_list',BankModel::select());
        $this->assign('server_list',AdminModel::select("and role_id=2"));//所属客服
        
    	return $this->fetch();
    }
    public function act_edit()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        $params = $_POST;
        
        $result = UsercashModel::do_edit(postt('id'),$params);
        echo $result;
    }
    









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
