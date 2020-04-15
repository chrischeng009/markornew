<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\ConfigModel;//本model放最后方便看清是否弄错

class Config extends Controller
{
    public function edit()
    {
        $this->isAuth();
        $find = ConfigModel::find();
        $this->assign('find',$find);
        
        $this->assign('enum_synchro_arr',ConfigModel::enum_synchro_arr());
        $this->assign('enum_acolor_arr',ConfigModel::enum_acolor_arr());
        
        return $this->fetch();
    }
    public function act_edit()
    {
        $this->isAuth();
        $data = $_POST;
        unset($data['id']);
        $data['uptime'] = time();
        $result = ConfigModel::do_edit(postt('id'),$data);
        if($result){
            $_SESSION['acolor'] = postt('acolor');
            echo 'success';
        }else{
            echo '操作失败';
        }
    }
    public function uploadify()
    {
        $file = request()->file('dx_file');
        $logo = '';
        if($file){
            $info = $file->validate(['ext'=>'jpg,png,bmp,gif,gpeg'])->move(ROOT_PATH.'uploads');
            if(empty($info)){
                $return['code'] = 'error';
                $return['msg'] = $file->getError();
                echo json_encode($return);exit;
            }
            $path = $info->getSaveName();
            $logo = '/uploads/'.$path;
        }
        
        $return['code'] = '';
        $return['msg'] = '';
        if($logo){
            $return['code'] = 'success';
            $return['msg'] = $logo;
        }
        echo json_encode($return);exit;
    }
    public function uploadify2()
    {
        $file = request()->file('dx_file2');
        $logo = '';
        if($file){
            $info = $file->validate(['ext'=>'jpg,png,bmp,gif,gpeg'])->move(ROOT_PATH.'uploads');
            if(empty($info)){
                $return['code'] = 'error';
                $return['msg'] = $file->getError();
                echo json_encode($return);exit;
            }
            $path = $info->getSaveName();
            $logo = '/uploads/'.$path;
        }
        
        $return['code'] = '';
        $return['msg'] = '';
        if($logo){
            $return['code'] = 'success';
            $return['msg'] = $logo;
        }
        echo json_encode($return);exit;
    }
    public function uploadify3()
    {
        $file = request()->file('dx_file3');
        $logo = '';
        if($file){
            $info = $file->validate(['ext'=>'jpg,png,bmp,gif,gpeg'])->move(ROOT_PATH.'uploads');
            if(empty($info)){
                $return['code'] = 'error';
                $return['msg'] = $file->getError();
                echo json_encode($return);exit;
            }
            $path = $info->getSaveName();
            $logo = '/uploads/'.$path;
        }
        
        $return['code'] = '';
        $return['msg'] = '';
        if($logo){
            $return['code'] = 'success';
            $return['msg'] = $logo;
        }
        echo json_encode($return);exit;
    }
 public function uploadify4()
    {
        $file = request()->file('dx_file4');
        $logo = '';
        if($file){
            $info = $file->validate(['ext'=>'jpg,png,bmp,gif,gpeg'])->move(ROOT_PATH.'uploads');
            if(empty($info)){
                $return['code'] = 'error';
                $return['msg'] = $file->getError();
                echo json_encode($return);exit;
            }
            $path = $info->getSaveName();
            $logo = '/uploads/'.$path;
        }
        
        $return['code'] = '';
        $return['msg'] = '';
        if($logo){
            $return['code'] = 'success';
            $return['msg'] = $logo;
        }
        echo json_encode($return);exit;
    }
    public function uploadify5()
    {
        $file = request()->file('dx_file5');
        $logo = '';
        if($file){
            $info = $file->validate(['ext'=>'jpg,png,bmp,gif,gpeg'])->move(ROOT_PATH.'uploads');
            if(empty($info)){
                $return['code'] = 'error';
                $return['msg'] = $file->getError();
                echo json_encode($return);exit;
            }
            $path = $info->getSaveName();
            $logo = '/uploads/'.$path;
        }
        
        $return['code'] = '';
        $return['msg'] = '';
        if($logo){
            $return['code'] = 'success';
            $return['msg'] = $logo;
        }
        echo json_encode($return);exit;
    }







    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
