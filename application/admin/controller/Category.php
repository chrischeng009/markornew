<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\CategoryModel;//本model放最后方便看清是否弄错

class Category extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = 'and pid=0';
        $_GET['name'] = gett('name');
        if(!empty($_GET['name'])){
            $where .= " and name like '%".$_GET['name']."%'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = ['page'=>$page,'query'=>['name'=>$_GET['name']]];
        $query = CategoryModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

    	return $this->fetch();
    }
    public function add()
    {
        $this->isAuth();
        $_GET['id'] = isset($_GET['id'])?gett('id'):'0';
        $find = CategoryModel::find("and id=".$_GET['id']);
        $find['name'] = isset($find['name'])?$find['name']:'无';
        $find['id'] = isset($find['id'])?$find['id']:'0';
        $this->assign('find',$find);
        
    	return $this->fetch();
    }
    public function act_add()
    {
        $this->isAuth();
        $data = $_POST;
        $data['intime'] = time();
        $data['uptime'] = time();
        $result = CategoryModel::add($data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function act_del()
    {
        $this->isAuth();
        $id = CategoryModel::getChildId(postt('id'));
        $where = " and id in (".$id.")";
        $result = CategoryModel::del($where);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function edit()
    {
        $this->isAuth();
        $find = CategoryModel::find("and id=".gett('id'));
        $this->assign('find',$find);
        
        $find_parent = CategoryModel::find("and id=".$find['pid']);
        $find_parent['name'] = isset($find_parent['name'])?$find_parent['name']:'无';
        $this->assign('find_parent',$find_parent);
        
        return $this->fetch();
    }
    public function act_edit()
    {
        $this->isAuth();
        $data = $_POST;
        unset($data['id']);
        $data['uptime'] = time();
        $result = CategoryModel::edit("and id='".postt('id')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
