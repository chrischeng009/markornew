<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\WithinModel;//本model放最后方便看清是否弄错

class Within extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['id'] = gett('id');
        if(!empty($_GET['id'])){
            $where .= " and id='".$_GET['id']."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = ['page'=>$page,'query'=>['id'=>$_GET['id']]];
        $query = WithinModel::tbname()->where("1=1 $where")->order('price asc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

    	return $this->fetch();
    }
    public function add()
    {
        $this->isAuth();
    	return $this->fetch();
    }
    public function act_add()
    {
        $this->isAuth();
        $data = $_POST;
        $data['intime'] = time();
        $data['uptime'] = time();
        $result = WithinModel::add($data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function act_del()
    {
        $this->isAuth();
        $result = WithinModel::del("and id='".postt('id')."'");
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function edit()
    {
        $this->isAuth();
        $find = WithinModel::find("and id=".gett('id'));
        $this->assign('find',$find);
        
        return $this->fetch();
    }
    public function act_edit()
    {
        $this->isAuth();
        $data = $_POST;
        unset($data['id']);
        $data['uptime'] = time();
        $result = WithinModel::edit("and id='".postt('id')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
