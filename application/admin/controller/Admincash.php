<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\AdminModel;
use app\common\model\BankModel;
use app\common\model\AdminbankModel;
use app\common\model\AdmincashModel;//本model放最后方便看清是否弄错

class Admincash extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['admin_id'] = gett('admin_id');
        $_GET['type'] = gett('type');
        $_GET['status'] = gett('status');
        $_GET['realname'] = gett('realname');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        //非超级管理员以及财务
        if($_SESSION['role_id']==2 || $_SESSION['role_id']==4){
            $_GET['admin_id']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['admin_id'])){
            $where .= " and admin_id = '".$_GET['admin_id']."'";
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
        $params = ['page'=>$page,
            'query'=>['admin_id'=>$_GET['admin_id'],
                'realname'=>$_GET['realname'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime'],
                
            ]];
        $query = AdmincashModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $sum = AdmincashModel::summoney($_GET['admin_id'],$_GET['starttime'],$_GET['lasttime']);
        $this->assign('sum',$sum);
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);
        
        $this->assign('admin_list',AdminModel::select("and role_id=2"));//所属客服
//        $this->assign('enum_type_arr',AdmincashModel::enum_type_arr());
        $this->assign('enum_status_arr',AdmincashModel::enum_status_arr());
//        $this->assign('bank_list',BankModel::select());
        $this->assign('adminbanklist',AdminbankModel::select(" and admin_id={$_SESSION['admin_id']}"));

    	return $this->fetch();
    }
    public function add()
    {
        $this->isAuth();
//        $this->assign('enum_type_arr',AdmincashModel::enum_type_arr());
        $this->assign('enum_status_arr',AdmincashModel::enum_status_arr());
//        $this->assign('bank_list',BankModel::select());
        $this->assign('adminbanklist',AdminbankModel::select(" and admin_id={$_SESSION['admin_id']}"));
    	return $this->fetch();
    }
    public function act_add()
    {
        $this->isAuth();
        $params = $_POST;
        $params['admin_id'] = $_SESSION['admin_id'];
        $params['remark'] = $_POST['remark'];
        $params['id'] = $_POST['adminbank_id'];
        $result = AdmincashModel::do_add($params);
        echo $result;
    }
    public function act_del()
    {
        $this->isAuth();
        $result = AdmincashModel::del("and id='".postt('id')."'");
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function edit()
    {
        $this->isAuth();
        $find = AdmincashModel::find("and id=".gett('id'));
        $this->assign('find',$find);
        
        $this->assign('type_name',AdmincashModel::enum_type_text($find['type']));
        $this->assign('enum_status_arr',AdmincashModel::enum_status_arr());
        $this->assign('find_admin',AdminModel::find("and id=".$find['admin_id']));
        
        return $this->fetch();
    }
    public function act_edit()
    {
        $this->isAuth();
        $params = $_POST;
        
        $result = AdmincashModel::do_edit(postt('id'),$params);
        echo $result;
    }
    //链接通过
    public function act_editurl()
    {
//        $this->isAuth();
        $params = $_GET;
        $result = AdmincashModel::do_edit(gett('id'),$params);
        echo $result;
        
    }
    









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
