<?php
namespace app\admin\controller;

use app\common\model\ShopcategoryModel;
use think\Controller;
use app\common\model\MerchantModel;
use app\common\model\AdminModel;
use app\common\model\ShopModel;//本model放最后方便看清是否弄错

class Shop extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['status'] = gett('status');
        $_GET['name'] = gett('name');
        $_GET['aid'] = gett('aid');
        $_GET['category_id'] = gett('category_id');
        if(!empty($_GET['status'])){
            $where .= " and a.status = '".$_GET['status']."'";
        }
        if(!empty($_GET['name'])){
            $where .= " and a.name like '%".$_GET['name']."%'";
        }
        //非超级管理员
        if($_SESSION['role_id'] !==1){
            $_GET['aid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['aid'])){
            $strId = MerchantModel::getStrId($_GET['aid']);
            $where .= " and a.merchant_id in({$strId})";
        }
        if(!empty($_GET['category_id'])){
            $where .= " and a.category_id = '".$_GET['category_id']."'";
        }
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'status'=>$_GET['status'],
                'name'=>$_GET['name'],
                'aid'=>$_GET['aid'],
                'category_id'=>$_GET['category_id'],
            ]
        ];
        $join = [
            ['merchant b', 'a.merchant_id=b.id'],
            ['admin c', 'b.aid=c.id'],
        ];

//        $query = ShopModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $query = ShopModel::tbname()->alias('a')->join($join)->field('a.*,b.mobile,b.aid,c.name as admin_name')->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);
        $this->assign('merchant_list',MerchantModel::select());
        $this->assign('enum_status_arr',ShopModel::enum_status_arr());
        $this->assign('teacher_list',AdminModel::select("and role_id=5"));//所属业务员
        $this->assign('category_list',ShopcategoryModel::select("and pid=0"));//店铺分类
        
    	return $this->fetch();
    }
    public function act_edit_status()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        $data['status'] = postt('status');
        $data['uptime'] = time();
        $result = ShopModel::edit("and id='".postt('id')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function edit()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        
        $find = ShopModel::find("and id=".gett('id'));
        $this->assign('category_list',ShopcategoryModel::select("and pid=0"));//店铺分类
        $this->assign('find',$find);

        return $this->fetch();
    }
    public function act_edit()
    {
        $auth = $this->control.'-lists';
        $this->isAuth($auth);
        
        $data = $_POST;
        $data['uptime'] = time();
        $result = ShopModel::edit("and id=".postt('id'),$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }








    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
