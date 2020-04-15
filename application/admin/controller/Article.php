<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\CategoryModel;
use app\common\model\ArticleModel;//本model放最后方便看清是否弄错

class Article extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['title'] = gett('title');
        $_GET['category_id'] = gett('category_id');
        if(!empty($_GET['title'])){
            $where .= " and title like '%".$_GET['title']."%'";
        }
        if(!empty($_GET['category_id'])){
            $where .= " and category_id='".$_GET['category_id']."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = ['page'=>$page,'query'=>['title'=>$_GET['title'],'category_id'=>$_GET['category_id']]];
        $query = ArticleModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);
        
        $this->assign('enum_status_arr',ArticleModel::enum_status_arr());
        $this->assign('enum_flag_arr',ArticleModel::enum_flag_arr());
        
        $this->assign('category_list',CategoryModel::select("and pid=0"));
        
    	return $this->fetch();
    }
    public function add()
    {
        $this->isAuth();
        $this->assign('enum_status_arr',ArticleModel::enum_status_arr());
        $this->assign('enum_flag_arr',ArticleModel::enum_flag_arr());
        $this->assign('category_list',CategoryModel::select("and pid=0"));
        
    	return $this->fetch();
    }
    public function act_add()
    {
        $this->isAuth();
        $data = $_POST;
        $data['intime'] = time();
        $data['uptime'] = time();
        $data['admin_id'] = $_SESSION['admin_id'];
        $result = ArticleModel::add($data);
        if($result){
            $this->success('操作成功','/home.php/'.$this->module.'/'.$this->control.'/lists.html');
        }else{
            echo '操作失败';
        }
    }
    public function edit()
    {
        $this->isAuth();
        $find = ArticleModel::find("and id=".gett('id'));
        $this->assign('find',$find);
        
        $this->assign('enum_status_arr',ArticleModel::enum_status_arr());
        $this->assign('enum_flag_arr',ArticleModel::enum_flag_arr());
        $this->assign('category_list',CategoryModel::select("and pid=0"));
        
    	return $this->fetch();
    }
    public function act_edit()
    {
        $this->isAuth();
        $data = $_POST;
        unset($data['id']);
        $data['uptime'] = time();
        $result = ArticleModel::edit("and id='".postt('id')."'",$data);
        if($result){
            $this->success('操作成功','/home.php/'.$this->module.'/'.$this->control.'/lists.html');
        }else{
            $this->error('操作失败');
        }
    }
    public function act_del()
    {
        $this->isAuth();
        $result = ArticleModel::del("and id='".postt('id')."'");
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function act_copy()
    {
        $find = ArticleModel::find("and id=".postt('id'));
        unset($find['id']);
        
        $data = $find;
        $data['title'] = $find['title'].'-copy';
        $data['intime'] = time();
        $data['uptime'] = time();
        $result = ArticleModel::add($data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    










    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
