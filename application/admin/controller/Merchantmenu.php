<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\MerchantmenuModel;//本model放最后方便看清是否弄错

class Merchantmenu extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = 'and pid=0';
        $_GET['name'] = gett('name');
        $_GET['control'] = gett('control');
        if(!empty($_GET['name'])){
            $where .= " and name like '%".$_GET['name']."%'";
        }
        if(!empty($_GET['control'])){
            $where .= " and control='".$_GET['control']."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = ['page'=>$page,'query'=>['name'=>$_GET['name'],'control'=>$_GET['control']]];
        $query = MerchantmenuModel::tbname()->where("1=1 $where")->order('sort asc,name asc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('enum_isshow_arr',MerchantmenuModel::enum_isshow_arr());
        
    	return $this->fetch();
    }
    public function act_del()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        $result = MerchantmenuModel::del("and id='".postt('id')."'");
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function act_edit()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        $data = $_POST;
        $data['uptime'] = time();
        
        if(empty($_POST['id'])){
            $data['intime'] = time();
            $result = MerchantmenuModel::add($data);
        }else{
            $result = MerchantmenuModel::edit("and id='".postt('id')."'",$data);
        }
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function act_find()
    {
        $find = MerchantmenuModel::find("and id=".postt('id'));
        echo json_encode($find,JSON_UNESCAPED_UNICODE);
    }









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
