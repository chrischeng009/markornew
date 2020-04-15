<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\AdminModel;
use app\common\model\ConfigModel;
use app\common\model\AreaModel;
use app\common\model\OrdershopModel;
use app\common\model\UserModel;//本model放最后方便看清是否弄错

class user extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['id'] = gett('id');
        $_GET['mobile'] = gett('mobile');
        $_GET['wangwang'] = gett('wangwang');
        $_GET['tbgrade'] = gett('tbgrade');
        $_GET['province'] = gett('province');
        $_GET['sex'] = gett('sex');
        $_GET['orderday'] = gett('orderday');
        $_GET['aid'] = gett('aid');
        $_GET['status2'] = gett('status2');
        if(!empty($_GET['id'])){
            $where .= " and id='".$_GET['id']."'";
        }
        if(!empty($_GET['mobile'])){
            $where .= " and mobile='".$_GET['mobile']."'";
        }
        if(!empty($_GET['wangwang'])){
            $where .= " and wangwang='".$_GET['wangwang']."'";
        }
        if(!empty($_GET['tbgrade'])){
            $where .= " and tbgrade='".$_GET['tbgrade']."'";
        }
        if(!empty($_GET['province'])){
            $where .= " and province='".$_GET['province']."'";
        }
        if(!empty($_GET['sex'])){
            $where .= " and sex='".$_GET['sex']."'";
        }
        if(!empty($_GET['status2'])){
            $where .= " and status2='".$_GET['status2']."'";
        }
        if($_GET['orderday']!=''){
            $today = date("Y-m-d",time());
            if($_GET['orderday']==0){
                $where .= " and ordertime >= '".strtotime($today." 00:00:00")."'";
                $where .= " and ordertime <= '".strtotime($today." 23:59:59")."'";
            }
            if($_GET['orderday']>0){
                $day = date("Y-m-d",time()-(86400*$_GET['orderday']));
                $where .= " and ordertime >= '".strtotime($day." 00:00:00")."'";
                $where .= " and ordertime <= '".strtotime($today." 23:59:59")."'";
            }
        }
        //非超级管理员
        if($_SESSION['role_id']!==1){
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
                'id'=>$_GET['id'],
                'mobile'=>$_GET['mobile'],
                'wangwang'=>$_GET['wangwang'],
                'tbgrade'=>$_GET['tbgrade'],
                'province'=>$_GET['province'],
                'sex'=>$_GET['sex'],
                'orderday'=>$_GET['orderday'],
                'aid'=>$_GET['aid'],
            ]
        ];
        $query = UserModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);

        $this->assign('enum_status2_arr',UserModel::enum_status2_arr());
        $this->assign('enum_tbgrade_arr',UserModel::enum_tbgrade_arr());
        $this->assign('area_list',AreaModel::select("and pid=0"));
        $this->assign('enum_sex_arr',UserModel::enum_sex_arr());
        $this->assign('usertotal',UserModel::count($where));
        $this->assign('server_list',AdminModel::select("and role_id=2"));//所属客服
        
    	return $this->fetch();
    }
    /*
     * 帐号审核
     */
    public function lists2()
    {
        $this->isAuth();
        $where = " and status in(".UserModel::enumStatus2.",".UserModel::enumStatus4.")";
        $_GET['id'] = gett('id');
        $_GET['mobile'] = gett('mobile');
        $_GET['wangwang'] = gett('wangwang');
        $_GET['status2'] = gett('status2');
        $_GET['isfreeze'] = gett('isfreeze');
        $_GET['aid'] = gett('aid');
        $_GET['status'] = gett('status');
        if(!empty($_GET['id'])){
            $where .= " and id='".$_GET['id']."'";
        }
        if(!empty($_GET['mobile'])){
            $where .= " and mobile='".$_GET['mobile']."'";
        }
        if(!empty($_GET['wangwang'])){
            $where .= " and wangwang='".$_GET['wangwang']."'";
        }
        if(!empty($_GET['status2'])){
            $where .= " and status2='".$_GET['status2']."'";
        }
        if(!empty($_GET['isfreeze'])){
            $where .= " and isfreeze='".$_GET['isfreeze']."'";
        }
        //非超级管理员
        if($_SESSION['role_id']!==1){
            $_GET['aid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['aid'])){
            $where .= " and aid='".$_GET['aid']."'";
        }
        if(!empty($_GET['status'])){
            $where .= " and status='".$_GET['status']."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'id'=>$_GET['id'],
                'mobile'=>$_GET['mobile'],
                'isfreeze'=>$_GET['isfreeze'],
                'wangwang'=>$_GET['wangwang'],
                'status2'=>$_GET['status2'],
                'aid'=>$_GET['aid'],
            ]
        ];
        $query = UserModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);
        
        $this->assign('enum_sex_arr',UserModel::enum_sex_arr());
        $this->assign('enum_birthyear_arr',UserModel::enum_birthyear_arr());
        $this->assign('enum_status_arr',UserModel::enum_status_arr());
        $this->assign('enum_status2_arr',UserModel::enum_status2_arr());
        $this->assign('enum_isfreeze_arr',ConfigModel::enum_isnot_arr());
        $this->assign('enum_tbgrade_arr',UserModel::enum_tbgrade_arr());
        $this->assign('server_list',AdminModel::select("and role_id=2"));//所属客服
        
    	return $this->fetch();
    }
    /*
     * 实名认证
     */
    public function lists3()
    {
        $this->isAuth();
        $where = " and status2 in(".UserModel::enumStatus2_2.",".UserModel::enumStatus2_4.")";
        $_GET['id'] = gett('id');
        $_GET['mobile'] = gett('mobile');
        $_GET['wangwang'] = gett('wangwang');
        $_GET['status2'] = gett('status2');
        $_GET['isfreeze'] = gett('isfreeze');
        $_GET['aid'] = gett('aid');
        if(!empty($_GET['id'])){
            $where .= " and id='".$_GET['id']."'";
        }
        if(!empty($_GET['mobile'])){
            $where .= " and mobile='".$_GET['mobile']."'";
        }
        if(!empty($_GET['wangwang'])){
            $where .= " and wangwang='".$_GET['wangwang']."'";
        }
        if(!empty($_GET['status2'])){
            $where .= " and status2='".$_GET['status2']."'";
        }
        if(!empty($_GET['isfreeze'])){
            $where .= " and isfreeze='".$_GET['isfreeze']."'";
        }
        //非超级管理员
        if($_SESSION['role_id']!==1){
            $_GET['aid']=$_SESSION['admin_id'];
        }
        if(!empty($_GET['aid'])){
            $where .= " and aid='".$_GET['aid']."'";
        }
        if(!empty($_GET['status2'])){
            $where .= " and status2='".$_GET['status2']."'";
        }
        
        $limit = !empty($_GET['limit'])?$_GET['limit']:10;
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $params = [
            'page'=>$page,
            'query'=>[
                'id'=>$_GET['id'],
                'mobile'=>$_GET['mobile'],
                'isfreeze'=>$_GET['isfreeze'],
                'wangwang'=>$_GET['wangwang'],
                'status2'=>$_GET['status2'],
                'aid'=>$_GET['aid'],
            ]
        ];
        $query = UserModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit,false,$params);
        $page_show = $query->render();
        $this->assign('page_show',$page_show);
        $this->assign('lists',$query);
        
        $this->assign('enum_sex_arr',UserModel::enum_sex_arr());
        $this->assign('enum_birthyear_arr',UserModel::enum_birthyear_arr());
        $this->assign('enum_status_arr',UserModel::enum_status_arr());
        $this->assign('enum_status2_arr',UserModel::enum_status2_arr());
        $this->assign('enum_isfreeze_arr',ConfigModel::enum_isnot_arr());
        $this->assign('enum_tbgrade_arr',UserModel::enum_tbgrade_arr());
        $this->assign('server_list',AdminModel::select("and role_id=2"));//所属客服
        
    	return $this->fetch();
    }
    public function edit()
    {
        $this->isAuth();
        $find = UserModel::find("and id=".gett('id'));
        $this->assign('find',$find);
        
        $this->assign('sex_name',UserModel::enum_sex_text($find['sex']));
        $preference_name = '';
        if($find['preference']){
            $prefArr = explode(',',$find['preference']);
            foreach($prefArr as $v){
                $preference_name .= UserModel::enum_preference_text($v).'&nbsp;';
            }
        }
        $this->assign('preference_name',$preference_name);
        $this->assign('marry_name',UserModel::enum_marry_text($find['marry']));
        $this->assign('income_name',UserModel::enum_income_text($find['income']));
        $this->assign('degree_name',UserModel::enum_degree_text($find['degree']));
        $this->assign('job_name',UserModel::enum_job_text($find['job']));
        
        $this->assign('teacher_list',AdminModel::select("and role_id=2"));//客服
        $this->assign('enum_isfreeze_arr',ConfigModel::enum_isfreeze_arr());
        $this->assign('enum_status_arr',UserModel::enum_status_arr());
        $this->assign('enum_status2_arr',UserModel::enum_status2_arr());
        $this->assign('enum_tbgrade_arr',UserModel::enum_tbgrade_arr());
        
    	return $this->fetch();
    }
    public function act_edit()
    {
        $this->isAuth();
        $count = UserModel::count("and (mobile='".postt('mobile')."' or wangwang='".postt('wangwang')."') and id!=".postt('id'));
        if($count>=1){
            echo "手机号或旺旺号已存在";exit;
        }
        
        $data = $_POST;
        unset($data['id']);unset($data['password']);
        
        if(!empty($_POST['password'])){
            $data['password'] = md5(trim($_POST['password']));
        }
        $data['uptime'] = time();
        $result = UserModel::edit("and id='".postt('id')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function act_find()
    {
        $find = UserModel::find("and id=".postt('id'));
        echo json_encode($find,JSON_UNESCAPED_UNICODE);
    }
    /*
     * 资料审核
     */
    public function act_edit_status()
    {
        $auth = $this->control.'-lists2';
        $this->isAuth($auth);
        $data['statusremark'] = postt('statusremark');
        $data['status'] = postt('status');
        $data['tbgrade'] = postt('tbgrade');
        $data['uptime'] = time();
        if($data['status']==UserModel::enumStatus3 && empty($data['tbgrade'])){
            echo '请先设置等级';exit;
        }
        $result = UserModel::edit("and id='".postt('user_id')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    /*
     * 认证状态
     */
    public function act_edit_status2()
    {
        $auth = $this->control.'-lists3';
        $this->isAuth($auth);
        if(isset($_POST['status2remark'])){
            $data['status2remark'] = postt('status2remark');
        }
        $data['status2'] = postt('status2');
        $data['uptime'] = time();
        $result = UserModel::edit("and id='".postt('id')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function act_edit_isfreeze()
    {
        $this->isAuth();
        $data['isfreeze'] = postt('isfreeze');
        $data['uptime'] = time();
        $result = UserModel::edit("and id='".postt('id')."'",$data);
        if($result){
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function act_ordershopname()
    {
        $ordershop_list = OrdershopModel::select("and user_id=".postt('id'));
        $html = '';
        foreach($ordershop_list as $v){
            $html .= "<p>{$v['shop_name']}（{$v['shop_wangwang']}）</p>";
        }
        echo $html;
    }
    










    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
