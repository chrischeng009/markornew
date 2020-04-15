<?php
namespace app\merchant\controller;

use think\Controller;
use app\common\model\ConfigModel;//本model放最后方便看清是否弄错

class Config extends Controller
{
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
    //上传多图
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
  //上传视频
    public function uploadify6()
    {
        $file = request()->file('dx_file6');
        $logo = '';
        if($file){
            $info = $file->validate(['ext'=>'mp4,ts,fly,mov,wmv,rmvb'])->move(ROOT_PATH.'uploads');
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
