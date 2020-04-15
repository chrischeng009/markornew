<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\AdminModel;
use app\common\model\AdminmlogsModel;//本model放最后方便看清是否弄错

class Adminmlogs extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['type'] = gett('type');
        $_GET['algorithm'] = gett('algorithm');
        $_GET['aid'] = gett('aid');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        $_GET['order_code'] = gett('order_code');
        if(!empty($_GET['order_code'])){
            $where .= " and a.order_code={$_GET['order_code']}";
        }
        if(!empty($_GET['type'])){
            $where .= " and a.type = '".$_GET['type']."'";
        }
        if(!empty($_GET['algorithm'])){
            $where .= " and a.algorithm = '".$_GET['algorithm']."'";
        }
        //业务员或者客服
        if($_SESSION['role_id']==2){
            $_GET['aid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['aid'])){
            $where .= " and a.admin_id={$_GET['aid']}";
        }
        $_GET['starttime'] = !empty($_GET['starttime'])?$_GET['starttime']:date("Y-m-d");
        $_GET['lasttime'] = !empty($_GET['lasttime'])?$_GET['lasttime']:date("Y-m-d");
        if(!empty($_GET['starttime'])){
            $where .= " and a.intime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
        }
        if(!empty($_GET['lasttime'])){
            $where .= " and a.intime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
        }
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'type'=>$_GET['type'],
                'algorithm'=>$_GET['algorithm'],
                'aid'=>$_GET['aid'],
                'starttime'=>$_GET['starttime'],
                'lasttime'=>$_GET['lasttime'],
                'order_code'=>$_GET['order_code'],
            ]
        ];
        $join = [
//            ['shop b', 'a.shop_id=b.id'],
            ['admin c', 'a.admin_id=c.id'],
        ];
//        $query = AdminmlogsModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $query = AdminmlogsModel::tbname()->alias('a')->join($join)->field('a.*,c.name ')->where("1=1 $where")->order('a.id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $tongji=AdminmlogsModel::tbname()->alias('a')->join($join)->field('a.*,c.name ')->where("1=1 $where")->order('a.id desc')->sum('a.money');
        $this->assign('tongji',$tongji);
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);
        $this->assign('enum_type_arr',AdminmlogsModel::enum_type_arr());
        $this->assign('enum_algorithm2_arr',AdminmlogsModel::enum_algorithm2_arr());
        $this->assign('teacher_list',AdminModel::select("and role_id=2"));//所属客服
        
    	return $this->fetch();
    }
    









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
