<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\ConfigModel;
use app\common\model\AdminModel;
use app\common\model\MerchantModel;//本model放最后方便看清是否弄错

class Merchant extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['mobile'] = gett('mobile');
        $_GET['status'] = gett('status');
        $_GET['name'] = gett('name');
        $_GET['aid'] = gett('aid');
        if(!empty($_GET['mobile'])){
            $where .= " and mobile='".$_GET['mobile']."'";
        }
        if(!empty($_GET['status'])){
            $where .= " and status='".$_GET['status']."'";
        }
        if(!empty($_GET['name'])) {
            $merchain_id = MerchantModel::getShopID($_GET['name']);
            if ($merchain_id) {
                $merchain_id = $merchain_id;
            } else {
                $merchain_id=99999;
            }
            $where .= " and id ={$merchain_id}";
        }
        // 判断是否为业务员
        if($_SESSION['role_id']==5){
            $_GET['aid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['aid'])){
            $where .= " and aid='".$_GET['aid']."'";
        }
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;

        $params = [
            'page'=>$page,
            'query'=>[
                'mobile'=>$_GET['mobile'],
                'status'=>$_GET['status'],
                'name'=>$_GET['name'],
                'aid'=>$_GET['aid'],
            ]
        ];
        $query = MerchantModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $totalmoney=MerchantModel::tbname()->where("status=2")->order('id desc')->sum('money');
        $this->assign('totalmoney',$totalmoney);
        $this->assign('enum_type_arr',MerchantModel::enum_type_arr());
        $this->assign('enum_status_arr',MerchantModel::enum_status_arr());
        $this->assign('enum_isfreeze_arr',ConfigModel::enum_isnot_arr());
        $this->assign('teacher_list',AdminModel::select("and role_id=5"));//负责导师-业务员
        
    	return $this->fetch();
    }
    public function edit()
    {
        $this->isAuth();
        $find = MerchantModel::find("and id=".gett('id'));
        $this->assign('find',$find);
        
        $this->assign('enum_type_arr',MerchantModel::enum_type_arr());
        $this->assign('enum_status_arr',MerchantModel::enum_status_arr());
        $this->assign('enum_isfreeze_arr',ConfigModel::enum_isnot_arr());
        
        $this->assign('teacher_list',AdminModel::select("and role_id=5"));//负责导师-业务员
        
    	return $this->fetch();
    }
    public function act_edit()
    {
        $this->isAuth();
        
        $data = $_POST;
        unset($data['password']);
        
        if(!empty($_POST['password'])){
            $data['password'] = md5(trim($_POST['password']));
        }
        $data['uptime'] = time();
        $find_config = ConfigModel::find();
        $data['worktime'] = !empty($data['worktime'])?$data['worktime']:$find_config['worktime'];
        $count = substr_count($data['worktime'],':');
        if($count!=2){
            echo "工作时间格式错误，正确的时间格式为 10:00-21:00";exit;
        }
        $count = substr_count($data['worktime'],'-');
        if($count!=1){
            echo "工作时间格式错误，正确的时间格式为 10:00-21:00";exit;
        }
        $data['worknum'] = !empty($data['worknum'])?$data['worknum']:$find_config['worknum'];
        $data['comrate']=$_POST['comrate']/100;
        $result = MerchantModel::edit("and id='".$_POST['id']."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    
    
    
    










    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
