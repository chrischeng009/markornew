<?php
namespace app\merchant\controller;

use think\Controller;
use app\common\model\MerchantmlogsModel;//本model放最后方便看清是否弄错

class Merchantmlogs extends Controller
{
    public function lists()
    {
        $where = "and merchant_id={$_SESSION['merchant_id']}";
        $_GET['type'] = gett('type');
        $_GET['algorithm'] = gett('algorithm');
        if(!empty($_GET['type'])){
            $where .= " and type = '".$_GET['type']."'";
        }
        if(!empty($_GET['algorithm'])){
            $where .= " and algorithm = '".$_GET['algorithm']."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = ['page'=>$page,'query'=>['type'=>$_GET['type'],'algorithm'=>$_GET['algorithm']]];
        $query = MerchantmlogsModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('enum_type_arr',MerchantmlogsModel::enum_type_arr());
        $this->assign('enum_algorithm2_arr',MerchantmlogsModel::enum_algorithm2_arr());
        
    	return $this->fetch();
    }
    









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
