<?php
namespace app\merchant\controller;

use think\Controller;
use app\common\model\ShopcategoryModel;//本model放最后方便看清是否弄错
use app\common\model\ShopModel;//本model放最后方便看清是否弄错

class Shop extends Controller
{
    public function lists()
    {
        $where = "and merchant_id={$_SESSION['merchant_id']}";
        $_GET['status'] = gett('status');
        $_GET['name'] = gett('name');
        $_GET['category_id'] = gett('category_id');
        if(!empty($_GET['status'])){
            $where .= " and status = '".$_GET['merchant_id']."'";
        }
        if(!empty($_GET['name'])){
            $where .= " and name like '%".$_GET['name']."%'";
        }
        if(!empty($_GET['category_id'])){
            $where .= " and category_id = '".$_GET['category_id']."'";
        }
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = ['page'=>$page,'query'=>['status'=>$_GET['status'],'name'=>$_GET['name'],'category_id'=>$_GET['category_id']]];
        $query = ShopModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('enum_status_arr',ShopModel::enum_status_arr());
        $this->assign('category_list',ShopcategoryModel::select("and pid=0"));//店铺分类
        
    	return $this->fetch();
    }
    public function add()
    {
        $this->assign('category_list',ShopcategoryModel::select("and pid=0"));//店铺分类
    	return $this->fetch();
    }
    public function act_add()
    {
        $data = $_POST;
        $data['status'] = 1;
        $data['name'] = trim($_POST['name']);
        $data['merchant_id'] = $_SESSION['merchant_id'];
        $data['intime'] = time();
        $data['uptime'] = time();
        $result = ShopModel::add($data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function edit()
    {
        $find = ShopModel::find("and merchant_id={$_SESSION['merchant_id']} and id=".gett('id'));
        $this->assign('category_list',ShopcategoryModel::select("and pid=0"));//店铺分类
        $this->assign('find',$find);

        return $this->fetch();
    }
    public function act_edit()
    {
        $data = $_POST;
        $data['status'] = 1;
        $data['uptime'] = time();
        $result = ShopModel::edit("and merchant_id={$_SESSION['merchant_id']} and id='".postt('id')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function act_del()
    {
        $result = ShopModel::del("and merchant_id={$_SESSION['merchant_id']} and id='".postt('id')."'");
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }








    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
