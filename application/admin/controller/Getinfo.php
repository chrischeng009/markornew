<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/27
 * Time: 11:07
 */
namespace app\admin\controller;

use think\Controller;
use app\common\model\BankModel;
use app\common\model\RoleModel;
use app\common\model\ConfigModel;
use app\common\model\TaskModel;
use app\common\model\OrderModel;
use app\common\model\UsermlogsModel;
use app\common\model\UsercashModel;
use app\common\model\MerchantcashModel;
use app\common\model\AdmincashModel;
use app\common\model\MerchantrechargeModel;
use app\common\model\UserModel;
use app\common\model\MerchantModel;
use app\common\model\MerchantloansModel;
use app\common\model\AdminModel;
use app\common\model\ShopModel;
use app\common\model\GetinfoModel;//本model放最后方便看清是否弄错
class Getinfo extends Controller
{
    /*
     * 系统首页
     */
    public function index()
    {
        $type=$_GET['type'] = gett('type');
        $_GET['starttime'] = gett('starttime');
        $_GET['lasttime'] = gett('lasttime');
        $day = date("Y-m-d");
        $where='';
        if ($type==1){
                if(!empty($_GET['starttime'])){
                    $where .= " and intime >= '".strtotime($_GET['starttime']." 00:00:00")."'";
                }else{
                    $where .= " and intime >= '".strtotime($day." 00:00:00")."'";
                }
                if(!empty($_GET['lasttime'])){
                    $where .= " and intime <= '".strtotime($_GET['lasttime']." 23:59:59")."'";
                }else{
                    $where .= " and intime <= '".strtotime($day." 23:59:59")."'";
                }
                $where .=" and status=6";
                $gettaskinfo=TaskModel::select("$where");
                $orderArr =array();
                foreach ($gettaskinfo as $k=>$v){
                    $orderArr[]= OrderModel::get_total($v['id']);
                    $find_shop[] = ShopModel::find("and id=".$v['shop_id']);
                    $gettaskinfo[$k]['order_money']=$orderArr[$k]['money'];
                    $gettaskinfo[$k]['order_num']=$orderArr[$k]['num'];
                    $gettaskinfo[$k]['without_money']=$orderArr[$k]['without_money'];
                    $gettaskinfo[$k]['within_money']=$orderArr[$k]['within_money'];
                    $gettaskinfo[$k]['shopname']=$find_shop[$k]['name'];
                }
                $this->assign('gettaskinfo',$gettaskinfo);
        }elseif ($type==2){

        }

        $this->assign('type',$type);
        return $this->fetch();
    }
    public function lists()
    {
       
        return $this->fetch();
    }
 
}

