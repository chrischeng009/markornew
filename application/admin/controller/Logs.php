<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\LogsModel;//本model放最后方便看清是否弄错

class Logs extends Controller
{
    public function lists()
    {
        $where = '';
        $_GET['ass_id'] = gett('ass_id');//关联Id
        if(!empty($_GET['ass_id'])){
            $where .= " and ass_id = '%".$_GET['ass_id']."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = ['page'=>$page,'query'=>['ass_id'=>$_GET['ass_id']]];
        $query = LogsModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

    	return $this->fetch();
    }
    









    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
